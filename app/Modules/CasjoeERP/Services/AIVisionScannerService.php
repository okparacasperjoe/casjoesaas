<?php

namespace App\Modules\CasjoeERP\Services;

use App\Core\Database;
use PDO;

class AIVisionScannerService
{
    /**
     * Save base64 image data to public uploads directory.
     */
    public static function saveImage(string $base64Data, int $tenantId): ?string
    {
        try {
            // Remove data URI scheme prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $ext = strtolower($type[1]);
                if ($ext === 'jpeg') $ext = 'jpg';
            } else {
                $ext = 'jpg';
            }

            $decoded = base64_decode($base64Data);
            if (!$decoded) {
                return null;
            }

            // Ensure storage directory exists
            $uploadDir = __DIR__ . '/../../../../public/uploads/erp/scans';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = 'scan_' . $tenantId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filepath = $uploadDir . '/' . $filename;

            file_put_contents($filepath, $decoded);

            return '/uploads/erp/scans/' . $filename;
        } catch (\Throwable $e) {
            error_log("AIVisionScannerService::saveImage error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Process image via Multimodal AI Vision (Qwen2.5-VL on HF or fallback).
     */
    public static function scan(string $base64Data, string $userPrompt, int $tenantId): array
    {
        $imageUrl = self::saveImage($base64Data, $tenantId);

        // Normalize base64 data URL for API
        $dataUrl = $base64Data;
        if (!str_starts_with($dataUrl, 'data:image/')) {
            $dataUrl = 'data:image/jpeg;base64,' . $base64Data;
        }

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'ai_%'");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $hfKey = $settings['ai_huggingface_key'] ?? '';
        $openaiKey = $settings['ai_openai_key'] ?? '';

        $extracted = null;

        // 1. Try Hugging Face Qwen2.5-VL-72B-Instruct
        if (!empty($hfKey)) {
            $extracted = self::callHuggingFaceVision($dataUrl, $userPrompt, $hfKey);
        }

        // 2. Fallback to OpenAI GPT-4o-mini if HF failed or not configured
        if (!$extracted && !empty($openaiKey)) {
            $extracted = self::callOpenAIVision($dataUrl, $userPrompt, $openaiKey);
        }

        // 3. Fallback heuristics if API call fails
        if (!$extracted) {
            $extracted = self::fallbackExtraction($userPrompt);
        }

        $extracted['image_url'] = $imageUrl;
        return $extracted;
    }

    /**
     * Call Hugging Face Router Vision Endpoint.
     */
    private static function callHuggingFaceVision(string $dataUrl, string $userPrompt, string $apiKey): ?array
    {
        $systemPrompt = "You are Cori AI Business Vision Scanner for Casjoe LLC ERP.\n"
            . "Carefully analyze the image (receipt, product/inventory, appointment note/schedule, business card/lead).\n"
            . "Identify the record type and extract all business details.\n"
            . "Categories:\n"
            . "1. 'inventory': Products, items, barcodes, shelf tags, stock. Fields: name, sku, quantity, unit_price, category, description.\n"
            . "2. 'booking': Appointments, meetings, calendar notes. Fields: guest_name, guest_email, guest_phone, booking_date (YYYY-MM-DD), start_time (HH:MM), end_time (HH:MM), notes.\n"
            . "3. 'expense': Receipts, invoices, bills, fuel slips. Fields: merchant, amount (numeric), date (YYYY-MM-DD), category, description, currency.\n"
            . "4. 'lead': Business cards, contact badges, contact sheets. Fields: name, company, email, phone, title, notes.\n"
            . "5. 'general': Other business documents.\n\n"
            . "Return ONLY valid JSON with no markdown wrapping or preamble:\n"
            . "{\n"
            . "  \"record_type\": \"inventory\" | \"booking\" | \"expense\" | \"lead\" | \"general\",\n"
            . "  \"title\": \"Short descriptive title\",\n"
            . "  \"summary\": \"Clear 1-2 sentence summary of what was found\",\n"
            . "  \"data\": { ... extracted fields ... },\n"
            . "  \"confidence\": \"high\" | \"medium\" | \"low\"\n"
            . "}";

        $promptText = !empty($userPrompt) ? "User note: \"$userPrompt\"\nScan this image and extract the record." : "Scan this image and extract the business record.";

        $payload = [
            'model' => 'Qwen/Qwen2.5-VL-72B-Instruct',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $promptText],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                    ]
                ]
            ],
            'max_tokens' => 600,
            'temperature' => 0.1
        ];

        $ch = curl_init('https://router.huggingface.co/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $resp = curl_exec($ch);
        curl_close($ch);

        if (!$resp) return null;

        $json = json_decode($resp, true);
        $content = $json['choices'][0]['message']['content'] ?? '';
        return self::parseJsonContent($content);
    }

    /**
     * Call OpenAI GPT-4o-mini Vision Endpoint.
     */
    private static function callOpenAIVision(string $dataUrl, string $userPrompt, string $apiKey): ?array
    {
        $systemPrompt = "You are Cori AI Business Vision Scanner for Casjoe LLC ERP. Extract structured business data from images into JSON with keys: record_type (inventory|booking|expense|lead|general), title, summary, data (key-value attributes), confidence.";

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $userPrompt ?: 'Extract the business details.'],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                    ]
                ]
            ],
            'max_tokens' => 500,
            'response_format' => ['type' => 'json_object']
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $resp = curl_exec($ch);
        curl_close($ch);

        if (!$resp) return null;

        $json = json_decode($resp, true);
        $content = $json['choices'][0]['message']['content'] ?? '';
        return self::parseJsonContent($content);
    }

    /**
     * Clean and parse model output into array.
     */
    private static function parseJsonContent(string $content): ?array
    {
        $content = trim($content);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $content, $m)) {
            $content = trim($m[1]);
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || empty($decoded['record_type'])) {
            return null;
        }

        return [
            'record_type' => strtolower($decoded['record_type']),
            'title'       => $decoded['title'] ?? 'Scanned Record',
            'summary'     => $decoded['summary'] ?? 'Detected business details from photo.',
            'data'        => (array)($decoded['data'] ?? []),
            'confidence'  => $decoded['confidence'] ?? 'high'
        ];
    }

    /**
     * Fallback heuristic parser if AI vision is offline.
     */
    private static function fallbackExtraction(string $userPrompt): array
    {
        $promptLower = strtolower($userPrompt);

        if (strpos($promptLower, 'inventory') !== false || strpos($promptLower, 'product') !== false || strpos($promptLower, 'stock') !== false) {
            return [
                'record_type' => 'inventory',
                'title'       => 'Scanned Product / Inventory Item',
                'summary'     => 'Image received for inventory addition.',
                'data'        => [
                    'name'       => 'New Scanned Item',
                    'sku'        => 'SKU-' . rand(1000, 9999),
                    'quantity'   => 1,
                    'unit_price' => 0.00,
                    'category'   => 'General'
                ],
                'confidence'  => 'medium'
            ];
        }

        if (strpos($promptLower, 'booking') !== false || strpos($promptLower, 'appointment') !== false || strpos($promptLower, 'meeting') !== false) {
            return [
                'record_type' => 'booking',
                'title'       => 'Scanned Appointment Request',
                'summary'     => 'Image received for appointment scheduling.',
                'data'        => [
                    'guest_name'   => 'Guest Contact',
                    'booking_date' => date('Y-m-d', strtotime('+1 day')),
                    'start_time'   => '10:00',
                    'end_time'     => '10:30',
                    'notes'        => $userPrompt ?: 'Meeting scheduled via AI Vision'
                ],
                'confidence'  => 'medium'
            ];
        }

        // Default to expense
        return [
            'record_type' => 'expense',
            'title'       => 'Scanned Receipt / Expense',
            'summary'     => 'Image captured for expense logging.',
            'data'        => [
                'merchant'    => 'Receipt Vendor',
                'amount'      => 0.00,
                'date'        => date('Y-m-d'),
                'category'    => 'General'
            ],
            'confidence'  => 'medium'
        ];
    }

    /**
     * Record the extracted entity directly into the CasjoeERP database.
     */
    public static function recordDirectly(string $recordType, array $fields, int $tenantId, ?string $imageUrl = null): array
    {
        $pdo = Database::getInstance()->getConnection();

        switch ($recordType) {
            case 'inventory':
                return self::saveInventoryRecord($pdo, $fields, $tenantId);

            case 'booking':
                return self::saveBookingRecord($pdo, $fields, $tenantId);

            case 'expense':
                return self::saveExpenseRecord($pdo, $fields, $tenantId, $imageUrl);

            case 'lead':
                return self::saveLeadRecord($pdo, $fields, $tenantId);

            default:
                return [
                    'success' => false,
                    'message' => 'Unrecognized record type for automatic recording.',
                    'view_url' => '/erp'
                ];
        }
    }

    /**
     * Save to erp_inventory and erp_inventory_items.
     */
    private static function saveInventoryRecord(PDO $pdo, array $fields, int $tenantId): array
    {
        try {
            $name      = trim($fields['name'] ?? $fields['item_name'] ?? 'Scanned Product');
            $sku       = trim($fields['sku'] ?? 'SKU-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 4)) . '-' . rand(100, 999));
            $quantity  = (int)($fields['quantity'] ?? $fields['qty'] ?? 1);
            if ($quantity < 0) $quantity = 0;
            $unitPrice = (float)preg_replace('/[^0-9.]/', '', (string)($fields['unit_price'] ?? $fields['price'] ?? 0));
            $category  = trim($fields['category'] ?? 'General');
            $desc      = trim($fields['description'] ?? '');

            // 1. Insert/Update erp_inventory
            $stmt = $pdo->prepare("SELECT id, quantity FROM erp_inventory WHERE tenant_id = ? AND (sku = ? OR item_name = ?) LIMIT 1");
            $stmt->execute([$tenantId, $sku, $name]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQty = (int)$existing['quantity'] + $quantity;
                $pdo->prepare("UPDATE erp_inventory SET quantity = ?, unit_price = ?, category = ? WHERE id = ? AND tenant_id = ?")
                    ->execute([$newQty, $unitPrice, $category, $existing['id'], $tenantId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO erp_inventory (tenant_id, item_name, sku, quantity, unit_price, category) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tenantId, $name, $sku, $quantity, $unitPrice, $category]);
            }

            // 2. Also keep erp_inventory_items in sync
            try {
                $chk = $pdo->prepare("SELECT id, stock_quantity FROM erp_inventory_items WHERE tenant_id = ? AND (sku = ? OR name = ?) LIMIT 1");
                $chk->execute([$tenantId, $sku, $name]);
                $exItem = $chk->fetch(PDO::FETCH_ASSOC);
                if ($exItem) {
                    $itemQty = (int)$exItem['stock_quantity'] + $quantity;
                    $pdo->prepare("UPDATE erp_inventory_items SET stock_quantity = ?, unit_price = ? WHERE id = ? AND tenant_id = ?")
                        ->execute([$itemQty, $unitPrice, $exItem['id'], $tenantId]);
                } else {
                    $ins = $pdo->prepare("INSERT INTO erp_inventory_items (tenant_id, sku, name, description, unit_price, stock_quantity, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
                    $ins->execute([$tenantId, $sku, $name, $desc, $unitPrice, $quantity]);
                }
            } catch (\Throwable $e) {
                // Non-fatal if table variation
            }

            return [
                'success'  => true,
                'message'  => "📦 **$name** added to inventory! (SKU: `$sku`, Qty: **$quantity**, Unit Price: **" . number_format($unitPrice, 2) . "**)",
                'view_url' => '/erp/inventory'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Failed to save inventory item: " . $e->getMessage(),
                'view_url' => '/erp/inventory'
            ];
        }
    }

    /**
     * Save to erp_scheduler_bookings.
     */
    private static function saveBookingRecord(PDO $pdo, array $fields, int $tenantId): array
    {
        try {
            $guestName  = trim($fields['guest_name'] ?? $fields['name'] ?? 'Guest');
            $guestEmail = trim($fields['guest_email'] ?? $fields['email'] ?? '');
            $guestPhone = trim($fields['guest_phone'] ?? $fields['phone'] ?? '');
            $notes      = trim($fields['notes'] ?? $fields['guest_notes'] ?? 'Booked via AI Vision Scan');

            $date = trim($fields['booking_date'] ?? $fields['date'] ?? date('Y-m-d', strtotime('+1 day')));
            $timeVal = strtotime($date);
            if ($timeVal) {
                $date = date('Y-m-d', $timeVal);
            } else {
                $date = date('Y-m-d', strtotime('+1 day'));
            }

            $startTime = trim($fields['start_time'] ?? '10:00');
            if (strlen($startTime) === 5) $startTime .= ':00';
            $endTime = trim($fields['end_time'] ?? '10:30');
            if (strlen($endTime) === 5) $endTime .= ':00';

            // Find tenant active profile or default to 1
            $profileStmt = $pdo->prepare("SELECT id FROM erp_scheduler_profiles WHERE tenant_id = ? AND is_active = 1 LIMIT 1");
            $profileStmt->execute([$tenantId]);
            $profileId = (int)($profileStmt->fetchColumn() ?: 1);

            $cancelToken = bin2hex(random_bytes(16));

            $stmt = $pdo->prepare("
                INSERT INTO erp_scheduler_bookings 
                (tenant_id, profile_id, guest_name, guest_email, guest_phone, guest_notes, booking_date, start_time, end_time, status, cancel_token)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?)
            ");
            $stmt->execute([$tenantId, $profileId, $guestName, $guestEmail, $guestPhone, $notes, $date, $startTime, $endTime, $cancelToken]);

            return [
                'success'  => true,
                'message'  => "📅 Appointment confirmed for **$guestName** on **$date** at **$startTime** - **$endTime**!",
                'view_url' => '/erp/scheduler'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Failed to save booking: " . $e->getMessage(),
                'view_url' => '/erp/scheduler'
            ];
        }
    }

    /**
     * Save to erp_expenses.
     */
    private static function saveExpenseRecord(PDO $pdo, array $fields, int $tenantId, ?string $imageUrl = null): array
    {
        try {
            $merchant = trim($fields['merchant'] ?? $fields['vendor'] ?? 'Expense');
            $desc = trim($fields['description'] ?? $merchant);
            $amount = (float)preg_replace('/[^0-9.]/', '', (string)($fields['amount'] ?? 0));
            if ($amount <= 0) {
                return ['success' => false, 'message' => "Invalid amount detected on receipt.", 'view_url' => '/erp/finance/expenses'];
            }

            $date = trim($fields['date'] ?? date('Y-m-d'));
            $t = strtotime($date);
            $date = $t ? date('Y-m-d', $t) : date('Y-m-d');

            $category = trim($fields['category'] ?? 'General');

            $stmt = $pdo->prepare("
                INSERT INTO erp_expenses (tenant_id, description, amount, date, category, receipt_path, status)
                VALUES (?, ?, ?, ?, ?, ?, 'approved')
            ");
            $stmt->execute([$tenantId, "$merchant - $desc", $amount, $date, $category, $imageUrl]);

            // Also record in transactions if available
            try {
                $txStmt = $pdo->prepare("
                    INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category)
                    VALUES (?, ?, ?, 'expense', ?, ?)
                ");
                $txStmt->execute([$tenantId, "$merchant - $desc", $amount, $date, $category]);
            } catch (\Throwable $e) {
                // Optional table sync
            }

            return [
                'success'  => true,
                'message'  => "💸 Expense of **" . number_format($amount, 2) . "** at **$merchant** recorded under **$category**!",
                'view_url' => '/erp/finance/expenses'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Failed to record expense: " . $e->getMessage(),
                'view_url' => '/erp/finance/expenses'
            ];
        }
    }

    /**
     * Save to erp_crm_leads.
     */
    private static function saveLeadRecord(PDO $pdo, array $fields, int $tenantId): array
    {
        try {
            $name = trim($fields['name'] ?? 'New Contact');
            $company = trim($fields['company'] ?? '');
            $email = trim($fields['email'] ?? '');
            $phone = trim($fields['phone'] ?? '');
            $title = trim($fields['title'] ?? '');

            // Check existing columns in erp_crm_leads
            $colsStmt = $pdo->query("SHOW COLUMNS FROM erp_crm_leads");
            $existingCols = $colsStmt->fetchAll(\PDO::FETCH_COLUMN);

            if (in_array('email', $existingCols)) {
                $stmt = $pdo->prepare("
                    INSERT INTO erp_crm_leads (tenant_id, name, email, phone, company, status)
                    VALUES (?, ?, ?, ?, ?, 'new')
                ");
                $stmt->execute([$tenantId, $name, $email, $phone, $company]);
            } else {
                $fullName = $name;
                if ($company) $fullName .= " ($company)";
                if ($email) $fullName .= " • $email";
                if ($phone) $fullName .= " • $phone";
                $sourceCol = in_array('source', $existingCols) ? 'source, ' : '';
                $sourceVal = in_array('source', $existingCols) ? "'AI Vision Scan', " : '';
                $stmt = $pdo->prepare("
                    INSERT INTO erp_crm_leads (tenant_id, name, {$sourceCol}status)
                    VALUES (?, ?, {$sourceVal}'new')
                ");
                $stmt->execute([$tenantId, $fullName]);
            }

            return [
                'success'  => true,
                'message'  => "👤 Lead **$name**" . ($company ? " from **$company**" : "") . " saved to CRM!",
                'view_url' => '/erp/crm/leads'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Failed to save lead: " . $e->getMessage(),
                'view_url' => '/erp/crm/leads'
            ];
        }
    }

    /**
     * Generate interactive preview card for AI Chat bubble.
     */
    public static function formatCard(array $scan, bool $alreadySaved = false, ?string $saveMessage = null, ?string $viewUrl = null): string
    {
        $type = $scan['record_type'] ?? 'general';
        $title = htmlspecialchars($scan['title'] ?? 'Scanned Record');
        $summary = htmlspecialchars($scan['summary'] ?? '');
        $data = $scan['data'] ?? [];
        $imageUrl = $scan['image_url'] ?? '';

        $typeMeta = [
            'inventory' => ['label' => 'Inventory Item', 'icon' => '📦', 'color' => '#3b82f6', 'action' => 'Save to Inventory', 'url' => '/erp/inventory'],
            'booking'   => ['label' => 'Appointment / Booking', 'icon' => '📅', 'color' => '#10b981', 'action' => 'Confirm Booking', 'url' => '/erp/scheduler'],
            'expense'   => ['label' => 'Expense / Receipt', 'icon' => '💸', 'color' => '#f59e0b', 'action' => 'Save Expense', 'url' => '/erp/finance/expenses'],
            'lead'      => ['label' => 'Lead / Contact', 'icon' => '👤', 'color' => '#8b5cf6', 'action' => 'Save to CRM', 'url' => '/erp/crm/leads'],
            'general'   => ['label' => 'Business Record', 'icon' => '📄', 'color' => '#64748b', 'action' => 'Save Record', 'url' => '/erp']
        ];

        $meta = $typeMeta[$type] ?? $typeMeta['general'];

        $html = "<div class=\"cori-scan-card\" style=\"border-radius:12px; padding:12px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,166,0,0.25); margin:8px 0;\">";
        $html .= "<div style=\"display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;\">";
        $html .= "<span style=\"font-size:0.75rem; font-weight:700; text-transform:uppercase; padding:3px 8px; border-radius:6px; background:" . $meta['color'] . "22; color:" . $meta['color'] . "; border:1px solid " . $meta['color'] . "44;\">" . $meta['icon'] . " " . $meta['label'] . "</span>";
        $html .= "<span style=\"font-size:0.72rem; color:rgba(255,255,255,0.5);\">AI Vision OCR</span>";
        $html .= "</div>";

        $html .= "<div style=\"font-weight:700; font-size:0.95rem; color:#fff; margin-bottom:4px;\">$title</div>";
        if ($summary) {
            $html .= "<div style=\"font-size:0.82rem; color:rgba(255,255,255,0.7); margin-bottom:10px;\">$summary</div>";
        }

        // Field key-values
        if (!empty($data)) {
            $html .= "<div style=\"background:rgba(0,0,0,0.25); border-radius:8px; padding:8px 10px; margin-bottom:10px; font-size:0.8rem;\">";
            foreach ($data as $k => $v) {
                if (is_array($v)) $v = implode(', ', $v);
                $label = ucwords(str_replace('_', ' ', $k));
                $html .= "<div style=\"display:flex; justify-content:space-between; padding:3px 0; border-bottom:1px solid rgba(255,255,255,0.05);\">";
                $html .= "<span style=\"color:rgba(255,255,255,0.6);\">$label:</span>";
                $html .= "<span style=\"font-weight:600; color:#FFA600; text-align:right;\">" . htmlspecialchars((string)$v) . "</span>";
                $html .= "</div>";
            }
            $html .= "</div>";
        }

        // Actions
        if ($alreadySaved) {
            $html .= "<div style=\"display:flex; align-items:center; gap:8px; margin-top:8px;\">";
            $html .= "<span style=\"color:#10b981; font-weight:700; font-size:0.85rem;\">✅ Recorded in ERP!</span>";
            $url = $viewUrl ?: $meta['url'];
            $html .= "<a href=\"$url\" class=\"btn-ai\" style=\"margin-left:auto; text-decoration:none; display:inline-block; font-size:0.78rem; padding:5px 12px;\">View Record →</a>";
            $html .= "</div>";
        } else {
            $encodedData = htmlspecialchars(json_encode([
                'record_type' => $type,
                'fields'      => $data,
                'image_url'   => $imageUrl
            ]), ENT_QUOTES, 'UTF-8');

            $html .= "<div class=\"cori-scan-actions\" style=\"display:flex; gap:8px; margin-top:10px;\">";
            $html .= "<button type=\"button\" class=\"btn-ai cori-scan-confirm-btn\" data-action-payload=\"$encodedData\" onclick=\"executeCoriScanAction(this)\" style=\"flex:1; padding:8px 12px; font-size:0.82rem; cursor:pointer;\">" . $meta['icon'] . " " . $meta['action'] . "</button>";
            $html .= "<a href=\"" . $meta['url'] . "\" style=\"padding:8px 12px; font-size:0.82rem; color:#FFA600; text-decoration:none; border:1px solid rgba(255,166,0,0.3); border-radius:8px; display:flex; align-items:center;\">Open ERP</a>";
            $html .= "</div>";
        }

        $html .= "</div>";
        return $html;
    }
}
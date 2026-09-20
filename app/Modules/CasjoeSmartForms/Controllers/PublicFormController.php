<?php

namespace App\Modules\CasjoeSmartForms\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class PublicFormController
{
    public function show($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        // Since this is public, we might not have a logged-in tenant context in the same way,
        // but for subdomain multi-tenancy we should still have TenantContext resolved.
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM smart_forms WHERE id = ?", [$id]);
        $form = $stmt->fetch();

        if (!$form) {
            http_response_code(404);
            echo "Form not found";
            return;
        }
        
        $settings = json_decode($form['settings'] ?? '{}', true);
        
        // 1. Check Form Expiry & Limits
        $closedMsg = !empty($settings['closed_message']) ? $settings['closed_message'] : 'This form is no longer accepting responses.';
        
        if (!empty($settings['expiry_date'])) {
            if (strtotime('now') > strtotime($settings['expiry_date'])) {
                $form['status'] = 'expired';
                $form['closed_message'] = $closedMsg;
            }
        }
        if (!empty($settings['limit_submissions']) && $form['status'] !== 'expired') {
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM smart_form_submissions WHERE form_id = ?");
            $stmtCount->execute([$form['id']]);
            $count = $stmtCount->fetchColumn();
            if ($count >= (int)$settings['limit_submissions']) {
                $form['status'] = 'expired';
                $form['closed_message'] = $closedMsg;
            }
        }

        // Block inactive or expired forms from public access
        if ($form['status'] !== 'active') {
            http_response_code(403);
            require_once __DIR__ . '/../Views/public/unavailable.php';
            return;
        }

        // Track view
        $this->trackEvent($form['id'], 'view', $form['tenant_id']);

        // Inject payment keys if payment is enabled
        $settings = json_decode($form['settings'] ?? '{}', true);
        $paystackPublicKey = '';
        $flutterwavePublicKey = '';
        if (!empty($settings['payment_enabled'])) {
            $stmtKey = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'paystack_public_key'");
            $stmtKey->execute();
            $paystackPublicKey = $stmtKey->fetchColumn() ?: '';

            $stmtFlw = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'flutterwave_public_key'");
            $stmtFlw->execute();
            $flutterwavePublicKey = $stmtFlw->fetchColumn() ?: '';
        }

        require_once __DIR__ . '/../Views/public/render.php';
    }

    public function submit($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // 1. Handle File Uploads & Data
        $submissionData = $_POST;

        if (!empty($_FILES)) {
            // Use CasjoeCloud storage for file management integration
            $cloudUploadDir = __DIR__ . '/../../../../storage/tenants/' . $tenantId . '/cloud';
            if (!is_dir($cloudUploadDir)) {
                mkdir($cloudUploadDir, 0777, true);
            }

            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    // Simple validation: limit size to 10MB
                    if ($file['size'] > 10 * 1024 * 1024) continue;

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'csv'];
                    
                    if (in_array($ext, $allowed)) {
                        $filename = uniqid('form_') . '.' . $ext;
                        $targetPath = $cloudUploadDir . '/' . $filename;
                        
                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            // Store in CasjoeCloud database for file management
                            $publicUrl = '/cloud/asset/' . $tenantId . '/' . $filename;
                            
                            // Insert into cloud_files table so it appears in CasjoeCloud
                            $stmt = $db->prepare("INSERT INTO cloud_files (tenant_id, folder_id, name, path, type, size_bytes) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$tenantId, null, $file['name'], $publicUrl, $file['type'], $file['size']]);
                            
                            // Store URL and filename in submission data
                            $submissionData[$key] = $publicUrl;
                            $submissionData[$key . '_filename'] = $file['name'];
                        }
                    }
                }
            }
        }

        $jsonData = json_encode($submissionData);
        
        $db->query("INSERT INTO smart_form_submissions (tenant_id, form_id, data) VALUES (?, ?, ?)", 
            [$tenantId, $id, $jsonData]);

        // 2. Track Completion
        $this->trackEvent($id, 'completion', $tenantId);

        // --- EMAIL AUTOMATION ---
        $stmt = $db->query("SELECT settings, user_id FROM smart_forms WHERE id = ?", [$id]);
        $formRow = $stmt->fetch();
        $settings = json_decode($formRow['settings'] ?? '{}', true);

        // Check Form Expiry & Limits before accepting POST
        if (!empty($settings['expiry_date']) && strtotime('now') > strtotime($settings['expiry_date'])) {
            echo json_encode(['success' => false, 'message' => 'This form has expired.']);
            return;
        }
        if (!empty($settings['limit_submissions'])) {
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM smart_form_submissions WHERE form_id = ?");
            $stmtCount->execute([$id]);
            $count = $stmtCount->fetchColumn();
            if ($count >= (int)$settings['limit_submissions']) {
                echo json_encode(['success' => false, 'message' => 'This form has reached its submission limit.']);
                return;
            }
        }

        $formMode = $settings['form_mode'] ?? 'classic';
        if (!empty($settings['email_automation_enabled']) && !empty($settings['email_subject']) && !empty($settings['email_body'])) {
            // Find user email in submission data
            $userEmail = null;
            foreach ($submissionData as $key => $value) {
                // Check if key contains 'email' or value looks like email
                if (strpos(strtolower($key), 'email') !== false && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $userEmail = $value;
                    break;
                }
            }
            // Fallback: Check values directly
             if (!$userEmail) {
                foreach ($submissionData as $value) {
                    if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $userEmail = $value;
                        break;
                    }
                }
            }

            if ($userEmail) {
                // Send Email
                // Optionally replace variables like {{name}} if we wanted to be fancy, but simple for now.
                \App\Core\Mailer::send($userEmail, $settings['email_subject'], $settings['email_body']);
            }
        }

        // --- ADMIN NOTIFICATION ---
        if (!empty($settings['notify_admin'])) {
            $adminEmail = $settings['notify_email'] ?? null;
            if (!$adminEmail) {
                // Get owner email
                $stmtAdmin = $db->prepare("SELECT email FROM users WHERE id = ?");
                $stmtAdmin->execute([$formRow['user_id']]);
                $adminEmail = $stmtAdmin->fetchColumn();
            }

            if ($adminEmail) {
                $stmtTitle = $db->prepare("SELECT title FROM smart_forms WHERE id = ?");
                $stmtTitle->execute([$id]);
                $formTitle = $stmtTitle->fetchColumn() ?: 'Smart Form';

                $body = "<h2>New Submission Received</h2>";
                $body .= "<p>You have received a new submission for: <strong>" . htmlspecialchars($formTitle) . "</strong></p>";
                $body .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; max-width: 600px;'>";
                foreach ($submissionData as $k => $v) {
                    if ($k === '_payment_ref') continue;
                    $val = is_array($v) ? implode(', ', $v) : $v;
                    $body .= "<tr><td style='background:#f4f4f4; width:40%;'><strong>" . htmlspecialchars($k) . "</strong></td><td>" . nl2br(htmlspecialchars($val)) . "</td></tr>";
                }
                $body .= "</table>";
                $body .= "<br><p><a href='https://" . $_SERVER['HTTP_HOST'] . "/smart-forms/responses/" . $id . "'>View in Dashboard</a></p>";

                \App\Core\Mailer::send($adminEmail, "New Submission: " . $formTitle, $body);
            }
        }
        // ------------------------

        // --- WEBHOOK INTEGRATION ---
        if (!empty($settings['webhook_enabled']) && !empty($settings['webhook_url'])) {
            try {
                $webhookPayload = json_encode([
                    'event' => 'form_submission',
                    'form_id' => $id,
                    'tenant_id' => $tenantId,
                    'submitted_at' => date('Y-m-d H:i:s'),
                    'data' => $submissionData
                ]);

                $ch = curl_init($settings['webhook_url']);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $webhookPayload,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 5 // Don't block the user if webhook is slow
                ]);
                curl_exec($ch);
            } catch (\Exception $e) {
                error_log("Smart Forms Webhook Failed: " . $e->getMessage());
            }
        }
        // ---------------------------

        // 3. Payment Integration (Inline Checkout)
        $submissionId = $db->lastInsertId();

        if (!empty($settings['payment_enabled']) && !empty($submissionData['_payment_ref'])) {
            $paymentRef = $submissionData['_payment_ref'];
            $amount = $settings['payment_amount'] ?? 0;
            $currency = $settings['payment_currency'] ?? 'NGN';
            $gateway = $settings['payment_gateway'] ?? 'paystack';
            $paymentVerified = false;

            // Verify payment server-side
            if ($gateway === 'paystack') {
                $stmtKey = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'paystack_secret_key'");
                $stmtKey->execute();
                $secretKey = $stmtKey->fetchColumn();

                if ($secretKey) {
                    $ch = curl_init("https://api.paystack.co/transaction/verify/" . urlencode($paymentRef));
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => ["Authorization: Bearer $secretKey"]
                    ]);
                    $response = json_decode(curl_exec($ch), true);

                    if (!empty($response['data']['status']) && $response['data']['status'] === 'success') {
                        $paymentVerified = true;
                        $amount = ($response['data']['amount'] ?? 0) / 100; // Convert from kobo
                        $currency = $response['data']['currency'] ?? $currency;
                    }
                }
            } elseif ($gateway === 'flutterwave') {
                $stmtKey = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'flutterwave_secret_key'");
                $stmtKey->execute();
                $secretKey = $stmtKey->fetchColumn();

                if ($secretKey) {
                    $ch = curl_init("https://api.flutterwave.com/v3/transactions/" . urlencode($paymentRef) . "/verify");
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => ["Authorization: Bearer $secretKey"]
                    ]);
                    $response = json_decode(curl_exec($ch), true);

                    if (!empty($response['data']['status']) && $response['data']['status'] === 'successful') {
                        $paymentVerified = true;
                        $amount = $response['data']['amount'] ?? $amount;
                        $currency = $response['data']['currency'] ?? $currency;
                    }
                }
            }

            $paymentStatus = $paymentVerified ? 'paid' : 'pending';
            $db->query("UPDATE smart_form_submissions SET payment_status = ?, payment_reference = ?, amount = ?, currency = ? WHERE id = ?",
                [$paymentStatus, $paymentRef, $amount, $currency, $submissionId]);

        } elseif (!empty($settings['payment_enabled'])) {
            // Payment enabled but no ref provided (shouldn't happen with inline, but fallback)
            $amount = $settings['payment_amount'] ?? 0;
            $currency = $settings['payment_currency'] ?? 'NGN';
            $db->query("UPDATE smart_form_submissions SET payment_status = 'pending', amount = ?, currency = ? WHERE id = ?",
                [$amount, $currency, $submissionId]);
        }

        $stmt = $db->query("SELECT title FROM smart_forms WHERE id = ?", [$id]);
        $formTitle = $stmt->fetchColumn() ?: 'Smart Form';

        $leadName = null;
        $leadEmail = null;
        $leadPhone = null;
        $leadCompany = null;
        $leadNotes = "";

        foreach ($submissionData as $key => $val) {
            if (is_array($val)) $val = implode(', ', $val);
            
            $lowKey = strtolower($key);
            if (!$leadName && (strpos($lowKey, 'name') !== false || strpos($lowKey, 'full_name') !== false)) $leadName = $val;
            if (!$leadEmail && (strpos($lowKey, 'email') !== false)) $leadEmail = $val;
            if (!$leadPhone && (strpos($lowKey, 'phone') !== false || strpos($lowKey, 'tel') !== false)) $leadPhone = $val;
            if (!$leadCompany && (strpos($lowKey, 'company') !== false || strpos($lowKey, 'business') !== false)) $leadCompany = $val;
            
            $leadNotes .= ucfirst($key) . ": " . $val . "\n";
        }

        try {
            \App\Core\Services\WorkflowEngineService::dispatch($tenantId, 'form_submitted', [
                'form_id' => $id,
                'lead_name' => $leadName,
                'lead_email' => $leadEmail,
                'lead_phone' => $leadPhone,
                'form_title' => $formTitle,
                'data' => $submissionData
            ]);
        } catch (\Exception $e) {
            error_log('Workflow trigger error (form_submitted): ' . $e->getMessage());
        }

        // --- Unified Inbox Integration ---
        try {
            $convId = \App\Core\Services\InboxService::createConversation([
                'tenant_id' => $tenantId,
                'contact_name' => $leadName ?: 'Form Respondent',
                'contact_email' => $leadEmail ?: null,
                'contact_phone' => $leadPhone ?: null,
                'channel' => 'form',
                'subject' => $formTitle . ' submission',
                'source_type' => 'smart_form_submissions',
                'source_id' => $submissionId,
            ]);
            // Format submission data as readable content
            $formContent = '';
            foreach ($submissionData as $key => $val) {
                if (is_array($val)) $val = implode(', ', $val);
                $formContent .= ucfirst(str_replace('_', ' ', $key)) . ': ' . $val . "\n";
            }
            \App\Core\Services\InboxService::addMessage($convId, [
                'tenant_id' => $tenantId,
                'direction' => 'inbound',
                'sender_type' => 'contact',
                'sender_name' => $leadName ?: 'Anonymous',
                'content' => $formContent,
                'content_type' => 'form_data',
                'channel' => 'form',
            ]);
        } catch (\Exception $e) {
            error_log('Inbox integration error (form): ' . $e->getMessage());
        }

        // --- CRM SYNC ---
        if (!empty($settings['crm_sync_enabled'])) {

            if ($leadName || $leadEmail) {
                $stmt = $db->prepare("INSERT INTO erp_crm_leads (tenant_id, name, email, phone, company, source, status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, 'new', ?, NOW())");
                $stmt->execute([
                    $tenantId,
                    $leadName ?: 'Contact from ' . $formTitle,
                    $leadEmail ?: '',
                    $leadPhone ?: '',
                    $leadCompany ?: '',
                    $formTitle . ' (Smart Form)',
                    $leadNotes
                ]);

                // Award lead scoring points for form submission
                try {
                    $newLeadId = $db->lastInsertId();
                    \App\Core\Services\LeadScoringService::addPoints($tenantId, (int)$newLeadId, 'Smart Form submitted: ' . $formTitle, 10);
                } catch (\Exception $e) {
                    error_log('Lead scoring error: ' . $e->getMessage());
                }

                // --- Lead Capture Email Notification ---
                try {
                    $stmt = $db->prepare("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = 'company_email'");
                    $stmt->execute([$tenantId]);
                    $adminEmail = $stmt->fetchColumn();

                    if (!$adminEmail) {
                        $stmt = $db->prepare("SELECT email FROM users WHERE tenant_id = ? ORDER BY id ASC LIMIT 1");
                        $stmt->execute([$tenantId]);
                        $adminEmail = $stmt->fetchColumn();
                    }

                    if ($adminEmail) {
                        $emailBody = "<h2>New Lead Captured!</h2>";
                        $emailBody .= "<p>A new lead has been captured from your Smart Form: <strong>" . htmlspecialchars($formTitle) . "</strong>.</p>";
                        $emailBody .= "<ul>";
                        $emailBody .= "<li><strong>Name:</strong> " . htmlspecialchars($leadName ?: 'N/A') . "</li>";
                        $emailBody .= "<li><strong>Email:</strong> " . htmlspecialchars($leadEmail ?: 'N/A') . "</li>";
                        $emailBody .= "<li><strong>Phone:</strong> " . htmlspecialchars($leadPhone ?: 'N/A') . "</li>";
                        $emailBody .= "<li><strong>Company:</strong> " . htmlspecialchars($leadCompany ?: 'N/A') . "</li>";
                        $emailBody .= "<li><strong>Notes:</strong><br>" . nl2br(htmlspecialchars($leadNotes ?: 'N/A')) . "</li>";
                        $emailBody .= "</ul>";
                        $emailBody .= "<p>Log in to your CRM to view and manage this lead.</p>";

                        \App\Core\Mailer::send($adminEmail, "New Lead Captured: " . ($leadName ?: 'Unknown'), $emailBody);
                    }
                } catch (\Exception $e) {
                    error_log("Failed to send lead capture email: " . $e->getMessage());
                }
                // ----------------------------------------
            }
        }
        // ----------------

        // Default Success — support both AJAX and traditional redirect
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                  || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => $settings['redirect_url'] ?? "/sf/$id?status=success"]);
            exit;
        }

        header("Location: /sf/$id?status=success");
        exit;
    }

    public function trackAJAX($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        // Simple endpoint for JS beacons
        $data = json_decode(file_get_contents('php://input'), true);
        $event = $data['event'] ?? 'view';
        $metadata = $data['metadata'] ?? [];

        // Resolve Tenant (Public context might need domain check, but for now we query from form)
        // We need tenant_id to store stats. 
        $db = Database::getInstance();
        $stmt = $db->query("SELECT tenant_id FROM smart_forms WHERE id = ?", [$id]);
        $form = $stmt->fetch();
        
        if ($form) {
            $this->storeEvent($id, $event, $form['tenant_id'], $metadata);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit;
    }

    private function trackEvent($formId, $type, $tenantId)
    {
        $this->storeEvent($formId, $type, $tenantId);
    }

    private function storeEvent($formId, $type, $tenantId, $metadata = [])
    {
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Simple device detection logic
        $device = 'desktop';
        if (preg_match('/(mobile|android|iphone|ipad)/i', $ua)) {
            $device = 'mobile';
        }

        $metaJson = !empty($metadata) ? json_encode($metadata) : null;

        $db->query("INSERT INTO smart_form_analytics (tenant_id, form_id, event_type, metadata, ip_address, device_type) VALUES (?, ?, ?, ?, ?, ?)", 
            [$tenantId, $formId, $type, $metaJson, $ip, $device]);
    }
}


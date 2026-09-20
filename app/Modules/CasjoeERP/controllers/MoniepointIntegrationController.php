<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Notification;
use PDO;

class MoniepointIntegrationController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId() ?: ($_SESSION['tenant_id'] ?? null);
    }

    /**
     * View the integration settings page
     */
    public function settings()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint'");
        $stmt->execute([$this->tenantId]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $tenantId = $this->tenantId;

        // Fetch all registered POS terminals
        $terminals = [];
        try {
            $stmtTerms = $this->pdo->prepare("
                SELECT t.*, l.name as location_name 
                FROM erp_pos_terminals t 
                LEFT JOIN erp_locations l ON l.id = t.location_id 
                WHERE t.tenant_id = ? 
                ORDER BY t.id ASC
            ");
            $stmtTerms->execute([$this->tenantId]);
            $terminals = $stmtTerms->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // If no terminals exist in erp_pos_terminals yet, but one is in erp_payment_integrations, auto-seed it:
            if (empty($terminals) && !empty($integration['terminal_serial'])) {
                $insTerm = $this->pdo->prepare("INSERT INTO erp_pos_terminals (tenant_id, terminal_name, terminal_serial, is_active) VALUES (?, 'Primary POS', ?, 1)");
                $insTerm->execute([$this->tenantId, $integration['terminal_serial']]);
                $stmtTerms->execute([$this->tenantId]);
                $terminals = $stmtTerms->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
        } catch (\Throwable $e) {}

        // Fetch locations if available
        $locations = [];
        try {
            $stmtLoc = $this->pdo->prepare("SELECT id, name FROM erp_locations WHERE tenant_id = ? ORDER BY name ASC");
            $stmtLoc->execute([$this->tenantId]);
            $locations = $stmtLoc->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        // Fetch recent Moniepoint transactions for preview
        $stmtTx = $this->pdo->prepare("SELECT * FROM erp_transactions WHERE tenant_id = ? AND category = 'Moniepoint POS' ORDER BY date DESC, id DESC LIMIT 10");
        $stmtTx->execute([$this->tenantId]);
        $recentMoniepointTx = $stmtTx->fetchAll(PDO::FETCH_ASSOC) ?: [];

        require __DIR__ . '/../Views/moniepoint_settings.php';
    }

    /**
     * Save the integration credentials
     */
    public function saveSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /erp/settings/moniepoint');
            exit;
        }

        $clientId = trim($_POST['client_id'] ?? '');
        $clientSecret = trim($_POST['client_secret'] ?? '');
        $terminalSerial = trim($_POST['terminal_serial'] ?? '');
        $webhookSecret = trim($_POST['webhook_secret'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $this->pdo->prepare("SELECT id FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint'");
        $stmt->execute([$this->tenantId]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $update = $this->pdo->prepare("UPDATE erp_payment_integrations SET client_id = ?, client_secret = ?, terminal_serial = ?, webhook_secret = ?, is_active = ? WHERE tenant_id = ? AND provider = 'moniepoint'");
            $update->execute([$clientId, $clientSecret, $terminalSerial, $webhookSecret, $isActive, $this->tenantId]);
        } else {
            $insert = $this->pdo->prepare("INSERT INTO erp_payment_integrations (tenant_id, provider, client_id, client_secret, terminal_serial, webhook_secret, is_active) VALUES (?, 'moniepoint', ?, ?, ?, ?, ?)");
            $insert->execute([$this->tenantId, $clientId, $clientSecret, $terminalSerial, $webhookSecret, $isActive]);
        }

        // Also ensure this terminal is registered in erp_pos_terminals
        if (!empty($terminalSerial)) {
            try {
                $stmtCheck = $this->pdo->prepare("SELECT id FROM erp_pos_terminals WHERE tenant_id = ? AND terminal_serial = ?");
                $stmtCheck->execute([$this->tenantId, $terminalSerial]);
                if (!$stmtCheck->fetch()) {
                    $stmtAdd = $this->pdo->prepare("INSERT INTO erp_pos_terminals (tenant_id, terminal_name, terminal_serial, is_active) VALUES (?, 'Primary POS', ?, 1)");
                    $stmtAdd->execute([$this->tenantId, $terminalSerial]);
                }
            } catch (\Throwable $e) {}
        }

        $_SESSION['flash_message'] = "Moniepoint integration settings saved successfully.";
        header('Location: /erp/settings/moniepoint?saved=1');
        exit;
    }

    /**
     * Add an additional POS terminal to the business
     */
    public function addTerminal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /erp/settings/moniepoint');
            exit;
        }

        $name = trim($_POST['terminal_name'] ?? 'POS Terminal');
        $serial = trim($_POST['terminal_serial'] ?? '');
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;

        if (!empty($serial)) {
            try {
                $stmt = $this->pdo->prepare("
                    INSERT INTO erp_pos_terminals (tenant_id, terminal_name, terminal_serial, location_id, is_active)
                    VALUES (?, ?, ?, ?, 1)
                    ON DUPLICATE KEY UPDATE terminal_name = VALUES(terminal_name), location_id = VALUES(location_id), is_active = 1
                ");
                $stmt->execute([$this->tenantId, $name, $serial, $locationId]);

                $_SESSION['flash_message'] = "POS Terminal '{$name}' ({$serial}) added successfully.";
            } catch (\Throwable $e) {
                $_SESSION['flash_message'] = "Error adding terminal: " . $e->getMessage();
            }
        }

        header('Location: /erp/settings/moniepoint?saved=1');
        exit;
    }

    /**
     * Delete a POS terminal
     */
    public function deleteTerminal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /erp/settings/moniepoint');
            exit;
        }

        $terminalId = (int)($_POST['terminal_id'] ?? 0);
        if ($terminalId > 0) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM erp_pos_terminals WHERE id = ? AND tenant_id = ?");
                $stmt->execute([$terminalId, $this->tenantId]);
                $_SESSION['flash_message'] = "Terminal removed successfully.";
            } catch (\Throwable $e) {
                $_SESSION['flash_message'] = "Error removing terminal: " . $e->getMessage();
            }
        }

        header('Location: /erp/settings/moniepoint?saved=1');
        exit;
    }

    /**
     * Webhook listener to receive real-time deposits and debits from Moniepoint POS
     * Supports multiple terminals per business automatically
     */
    public function webhook($params = [])
    {
        header('Content-Type: application/json');

        // Health check / GET ping
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode(['status' => 'ok', 'service' => 'Casjoe ERP Moniepoint Webhook Endpoint']);
            exit;
        }

        $rawBody = file_get_contents('php://input');
        $payload = json_decode($rawBody, true) ?: $_POST;

        if (empty($payload)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Empty webhook payload.']);
            exit;
        }

        // 1. Identify Tenant & Specific Terminal
        $tenantId = null;
        $terminalName = null;

        if (!empty($params['tenant_id'])) {
            $tenantId = (int)$params['tenant_id'];
        } elseif (!empty($_GET['tenant_id'])) {
            $tenantId = (int)$_GET['tenant_id'];
        } elseif (!empty($payload['tenant_id']) || !empty($payload['tenantId'])) {
            $tenantId = (int)($payload['tenant_id'] ?? $payload['tenantId']);
        }

        $terminalSerial = trim($payload['terminalSerial'] ?? ($payload['terminalId'] ?? ($payload['terminal_serial'] ?? '')));

        if (!empty($terminalSerial)) {
            // Check multi-terminal registry
            try {
                $stmtTerm = $this->pdo->prepare("
                    SELECT t.tenant_id, t.terminal_name, l.name as location_name 
                    FROM erp_pos_terminals t 
                    LEFT JOIN erp_locations l ON l.id = t.location_id 
                    WHERE t.terminal_serial = ? AND t.is_active = 1 
                    LIMIT 1
                ");
                $stmtTerm->execute([$terminalSerial]);
                $termRow = $stmtTerm->fetch(PDO::FETCH_ASSOC);
                if ($termRow) {
                    if (!$tenantId) $tenantId = (int)$termRow['tenant_id'];
                    $terminalName = $termRow['terminal_name'] . (!empty($termRow['location_name']) ? " [{$termRow['location_name']}]" : "");
                }
            } catch (\Throwable $e) {}

            // Fallback to erp_payment_integrations
            if (!$tenantId) {
                $stmtTenant = $this->pdo->prepare("SELECT tenant_id FROM erp_payment_integrations WHERE terminal_serial = ? AND is_active = 1 LIMIT 1");
                $stmtTenant->execute([$terminalSerial]);
                $tenantId = $stmtTenant->fetchColumn();
            }
        }

        if (!$tenantId) {
            $clientId = trim($payload['clientId'] ?? ($payload['client_id'] ?? ''));
            if (!empty($clientId)) {
                $stmtTenant = $this->pdo->prepare("SELECT tenant_id FROM erp_payment_integrations WHERE client_id = ? AND is_active = 1 LIMIT 1");
                $stmtTenant->execute([$clientId]);
                $tenantId = $stmtTenant->fetchColumn();
            }
        }

        if (!$tenantId) {
            error_log("Moniepoint Webhook Notice: Unmatched tenant for payload " . substr($rawBody, 0, 300));
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Tenant not identified for terminal/client.']);
            exit;
        }

        // 2. Extract Transaction Details
        $reference = trim($payload['transactionReference'] ?? ($payload['reference'] ?? ($payload['merchantReference'] ?? ($payload['paymentReference'] ?? ''))));
        if (empty($reference)) {
            $reference = 'MP-' . time() . '-' . rand(1000, 9999);
        }

        // Idempotency check: prevent duplicate recordings
        $stmtDup = $this->pdo->prepare("SELECT id FROM erp_transactions WHERE tenant_id = ? AND reference = ? LIMIT 1");
        $stmtDup->execute([$tenantId, $reference]);
        $existingId = $stmtDup->fetchColumn();
        if ($existingId) {
            echo json_encode(['status' => 'already_processed', 'transaction_id' => $existingId]);
            exit;
        }

        $amount = (float)($payload['amount'] ?? ($payload['actualAmount'] ?? ($payload['requestAmount'] ?? ($payload['totalAmount'] ?? 0))));
        if ($amount <= 0 && isset($payload['data']['amount'])) {
            $amount = (float)$payload['data']['amount'];
        }

        $eventType = strtoupper(trim($payload['transactionType'] ?? ($payload['eventType'] ?? ($payload['paymentMethod'] ?? ($payload['type'] ?? 'PURCHASE')))));

        // 3. Determine if Deposit (Income) or Debit (Expense)
        $debitKeywords = ['DEBIT', 'TRANSFER', 'DISBURSEMENT', 'PAYOUT', 'WITHDRAWAL', 'REFUND', 'OUTFLOW', 'EXPENSE'];
        $isDebit = false;
        foreach ($debitKeywords as $kw) {
            if (strpos($eventType, $kw) !== false) {
                $isDebit = true;
                break;
            }
        }

        $type = $isDebit ? 'expense' : 'income';
        $direction = $isDebit ? 'Debit (Outflow)' : 'Deposit (Inflow)';

        $paymentMethod = $payload['actualPaymentMethod'] ?? ($payload['paymentMethod'] ?? ($payload['channel'] ?? 'POS Terminal'));
        $date = !empty($payload['paidOn']) ? date('Y-m-d', strtotime($payload['paidOn'])) : date('Y-m-d');

        $terminalLabel = $terminalName ? "via {$terminalName} ({$terminalSerial})" : (!empty($terminalSerial) ? "via POS {$terminalSerial}" : "via POS");
        $desc = "Moniepoint POS {$direction} - {$paymentMethod} {$terminalLabel} (Ref: {$reference})";

        // 4. Record in erp_transactions (Directly impacts ERP Stats)
        $stmtInsert = $this->pdo->prepare("
            INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category, reference) 
            VALUES (?, ?, ?, ?, ?, 'Moniepoint POS', ?)
        ");
        $stmtInsert->execute([
            $tenantId,
            $desc,
            $amount,
            $type,
            $date,
            $reference
        ]);
        $insertedId = $this->pdo->lastInsertId();

        // 5. Update last_sync_at timestamp
        try {
            $stmtSync = $this->pdo->prepare("UPDATE erp_payment_integrations SET last_sync_at = NOW() WHERE tenant_id = ?");
            $stmtSync->execute([$tenantId]);
        } catch (\Throwable $e) {}

        // 6. Send Real-Time In-App Notification to Tenant Admins
        try {
            $stmtUsers = $this->pdo->prepare("SELECT id FROM users WHERE tenant_id = ? AND role = 'admin'");
            $stmtUsers->execute([$tenantId]);
            $adminIds = $stmtUsers->fetchAll(PDO::FETCH_COLUMN) ?: [];

            $formattedAmt = '₦' . number_format($amount, 2);
            $notifTitle = "Moniepoint {$direction}: {$formattedAmt}";
            $notifMsg = "A {$direction} of {$formattedAmt} was processed on {$terminalLabel} and added to your ERP stats.";

            foreach ($adminIds as $adminId) {
                Notification::send($adminId, $notifTitle, $notifMsg, '/erp/transactions');
            }
        } catch (\Throwable $e) {
            error_log("Moniepoint notification send error: " . $e->getMessage());
        }

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => "Transaction recorded as {$type} in ERP stats.",
            'transaction_id' => $insertedId,
            'terminal' => $terminalLabel,
            'reference' => $reference
        ]);
        exit;
    }

    /**
     * Simulate a test deposit or debit for instant merchant verification
     */
    public function simulateTest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /erp/settings/moniepoint');
            exit;
        }

        $testType = $_POST['test_type'] ?? 'deposit';
        $amount = (float)($_POST['test_amount'] ?? 5000);
        if ($amount <= 0) $amount = 5000;

        $targetSerial = trim($_POST['terminal_serial'] ?? '');
        $terminalName = null;

        if (!empty($targetSerial)) {
            $stmt = $this->pdo->prepare("SELECT terminal_name FROM erp_pos_terminals WHERE tenant_id = ? AND terminal_serial = ?");
            $stmt->execute([$this->tenantId, $targetSerial]);
            $terminalName = $stmt->fetchColumn();
        } else {
            $stmt = $this->pdo->prepare("SELECT terminal_name, terminal_serial FROM erp_pos_terminals WHERE tenant_id = ? LIMIT 1");
            $stmt->execute([$this->tenantId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $terminalName = $row['terminal_name'];
                $targetSerial = $row['terminal_serial'];
            } else {
                $targetSerial = 'MP-DEMO-POS';
                $terminalName = 'Main POS';
            }
        }

        $reference = 'TEST-' . strtoupper($testType) . '-' . time();
        $type = ($testType === 'debit') ? 'expense' : 'income';
        $direction = ($testType === 'debit') ? 'Debit (Outflow)' : 'Deposit (Inflow)';
        $terminalLabel = "via {$terminalName} ({$targetSerial})";
        $desc = "Moniepoint POS {$direction} - CARD {$terminalLabel} (Ref: {$reference}) [Test Simulation]";

        $stmtInsert = $this->pdo->prepare("
            INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category, reference) 
            VALUES (?, ?, ?, ?, ?, 'Moniepoint POS', ?)
        ");
        $stmtInsert->execute([
            $this->tenantId,
            $desc,
            $amount,
            $type,
            date('Y-m-d'),
            $reference
        ]);

        try {
            $stmtSync = $this->pdo->prepare("UPDATE erp_payment_integrations SET last_sync_at = NOW() WHERE tenant_id = ?");
            $stmtSync->execute([$this->tenantId]);
        } catch (\Throwable $e) {}

        $formattedAmt = '₦' . number_format($amount, 2);
        $notifTitle = "Moniepoint {$direction}: {$formattedAmt}";
        $notifMsg = "Test simulation: A {$direction} of {$formattedAmt} was processed on {$terminalLabel} and logged to your ERP stats.";

        if (!empty($_SESSION['user_id'])) {
            Notification::send($_SESSION['user_id'], $notifTitle, $notifMsg, '/erp/transactions');
        }

        $_SESSION['flash_message'] = "Test {$direction} of {$formattedAmt} on {$terminalLabel} successfully recorded! Check your notification bell and ERP stats.";
        header('Location: /erp/settings/moniepoint?saved=1');
        exit;
    }

    /**
     * API to push a payment to POS and record in ERP ledger
     */
    public function pushPayment()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        if (!$this->tenantId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid tenant session.']);
            return;
        }

        $amount = (float)($_POST['amount'] ?? 0);
        $description = trim($_POST['description'] ?? 'POS Payment');
        $targetTerminalSerial = trim($_POST['terminal_serial'] ?? '');

        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint' AND is_active = 1");
        $stmt->execute([$this->tenantId]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$integration || empty($integration['client_id']) || empty($integration['client_secret'])) {
            echo json_encode(['success' => false, 'message' => 'Moniepoint POS is not configured or disabled. Please configure your credentials in ERP Settings.']);
            return;
        }

        // If specific terminal not sent, find default from erp_pos_terminals or integration
        if (empty($targetTerminalSerial)) {
            try {
                $stmtT = $this->pdo->prepare("SELECT terminal_serial FROM erp_pos_terminals WHERE tenant_id = ? AND is_active = 1 LIMIT 1");
                $stmtT->execute([$this->tenantId]);
                $targetTerminalSerial = $stmtT->fetchColumn();
            } catch (\Throwable $e) {}
            if (empty($targetTerminalSerial)) {
                $targetTerminalSerial = $integration['terminal_serial'] ?? '';
            }
        }

        if (empty($targetTerminalSerial)) {
            echo json_encode(['success' => false, 'message' => 'No active POS terminal found. Please add a terminal serial in Moniepoint Settings.']);
            return;
        }

        $moniepoint = new \App\Modules\CasjoeERP\Services\MoniepointService(
            $integration['client_id'],
            $integration['client_secret'],
            $targetTerminalSerial
        );

        $merchantReference = 'ERP-' . $this->tenantId . '-' . time() . '-' . rand(1000, 9999);

        try {
            // Push payment request
            $response = $moniepoint->pushPayment($amount, $merchantReference);
            
            // Record the transaction as inflow in ERP ledger
            $stmt = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category, reference) VALUES (?, ?, ?, 'income', ?, 'Moniepoint POS', ?)");
            $stmt->execute([$this->tenantId, $description . " (Terminal: {$targetTerminalSerial})", $amount, date('Y-m-d'), $merchantReference]);

            // Real-time notification
            try {
                $formattedAmt = '₦' . number_format($amount, 2);
                Notification::send($_SESSION['user_id'] ?? 0, "Moniepoint Payment Received: {$formattedAmt}", "Payment of {$formattedAmt} received on terminal {$targetTerminalSerial}.", '/erp/transactions');
            } catch (\Throwable $e) {}

            echo json_encode([
                'success' => true,
                'message' => "Payment pushed to terminal {$targetTerminalSerial} successfully.",
                'reference' => $merchantReference,
                'terminal' => $targetTerminalSerial,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Moniepoint Error: ' . $e->getMessage()
            ]);
        }
    }
}

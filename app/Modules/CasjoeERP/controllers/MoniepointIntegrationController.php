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

        $_SESSION['flash_message'] = "Moniepoint integration settings saved successfully.";
        header('Location: /erp/settings/moniepoint?saved=1');
        exit;
    }

    /**
     * Webhook listener to receive real-time deposits and debits from Moniepoint POS
     * Accessible by Moniepoint servers without session authentication
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

        // 1. Identify Tenant
        $tenantId = null;
        if (!empty($params['tenant_id'])) {
            $tenantId = (int)$params['tenant_id'];
        } elseif (!empty($_GET['tenant_id'])) {
            $tenantId = (int)$_GET['tenant_id'];
        } elseif (!empty($payload['tenant_id']) || !empty($payload['tenantId'])) {
            $tenantId = (int)($payload['tenant_id'] ?? $payload['tenantId']);
        }

        $terminalSerial = trim($payload['terminalSerial'] ?? ($payload['terminalId'] ?? ($payload['terminal_serial'] ?? '')));

        if (!$tenantId && !empty($terminalSerial)) {
            $stmtTenant = $this->pdo->prepare("SELECT tenant_id FROM erp_payment_integrations WHERE terminal_serial = ? AND is_active = 1 LIMIT 1");
            $stmtTenant->execute([$terminalSerial]);
            $tenantId = $stmtTenant->fetchColumn();
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
        $responseCode = trim($payload['responseCode'] ?? ($payload['status'] ?? ($payload['processingStatus'] ?? '')));

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

        $desc = "Moniepoint POS {$direction} - {$paymentMethod} (Ref: {$reference})";

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
            $notifMsg = "A {$direction} of {$formattedAmt} was processed on your Moniepoint POS" . (!empty($terminalSerial) ? " ({$terminalSerial})" : "") . " and added to your ERP stats.";

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

        $stmt = $this->pdo->prepare("SELECT terminal_serial FROM erp_payment_integrations WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $termSerial = $stmt->fetchColumn() ?: 'MP-DEMO-POS';

        $simulatedPayload = [
            'tenant_id' => $this->tenantId,
            'terminalSerial' => $termSerial,
            'amount' => $amount,
            'transactionType' => ($testType === 'debit') ? 'POS_WITHDRAWAL' : 'CARD_PURCHASE',
            'paymentMethod' => 'CARD',
            'transactionReference' => 'TEST-' . strtoupper($testType) . '-' . time(),
            'responseCode' => '00'
        ];

        // Call webhook internally
        $_SERVER['REQUEST_METHOD'] = 'POST';
        // Execute internal simulation
        $reference = $simulatedPayload['transactionReference'];
        $type = ($testType === 'debit') ? 'expense' : 'income';
        $direction = ($testType === 'debit') ? 'Debit (Outflow)' : 'Deposit (Inflow)';
        $desc = "Moniepoint POS {$direction} - CARD (Ref: {$reference}) [Test Simulation]";

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
        $notifMsg = "Test simulation: A {$direction} of {$formattedAmt} was processed and logged to your ERP stats.";

        if (!empty($_SESSION['user_id'])) {
            Notification::send($_SESSION['user_id'], $notifTitle, $notifMsg, '/erp/transactions');
        }

        $_SESSION['flash_message'] = "Test {$direction} of {$formattedAmt} successfully recorded! Check your notification bell and ERP stats.";
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

        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint' AND is_active = 1");
        $stmt->execute([$this->tenantId]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$integration || empty($integration['client_id']) || empty($integration['client_secret']) || empty($integration['terminal_serial'])) {
            echo json_encode(['success' => false, 'message' => 'Moniepoint POS is not configured or disabled. Please configure your credentials in ERP Settings.']);
            return;
        }

        $moniepoint = new \App\Modules\CasjoeERP\Services\MoniepointService(
            $integration['client_id'],
            $integration['client_secret'],
            $integration['terminal_serial']
        );

        $merchantReference = 'ERP-' . $this->tenantId . '-' . time() . '-' . rand(1000, 9999);

        try {
            // Push payment request
            $response = $moniepoint->pushPayment($amount, $merchantReference);
            
            // Record the transaction as inflow in ERP ledger
            $stmt = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date, category, reference) VALUES (?, ?, ?, 'income', ?, 'Moniepoint POS', ?)");
            $stmt->execute([$this->tenantId, $description, $amount, date('Y-m-d'), $merchantReference]);

            // Real-time notification
            try {
                $formattedAmt = '₦' . number_format($amount, 2);
                Notification::send($_SESSION['user_id'] ?? 0, "Moniepoint Payment Received: {$formattedAmt}", "Payment of {$formattedAmt} received on terminal {$integration['terminal_serial']}.", '/erp/transactions');
            } catch (\Throwable $e) {}

            echo json_encode([
                'success' => true,
                'message' => 'Payment pushed to terminal successfully.',
                'reference' => $merchantReference,
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

<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
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
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $this->pdo->prepare("SELECT id FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint'");
        $stmt->execute([$this->tenantId]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $update = $this->pdo->prepare("UPDATE erp_payment_integrations SET client_id = ?, client_secret = ?, terminal_serial = ?, is_active = ? WHERE tenant_id = ? AND provider = 'moniepoint'");
            $update->execute([$clientId, $clientSecret, $terminalSerial, $isActive, $this->tenantId]);
        } else {
            $insert = $this->pdo->prepare("INSERT INTO erp_payment_integrations (tenant_id, provider, client_id, client_secret, terminal_serial, is_active) VALUES (?, 'moniepoint', ?, ?, ?, ?)");
            $insert->execute([$this->tenantId, $clientId, $clientSecret, $terminalSerial, $isActive]);
        }

        $_SESSION['flash_message'] = "Moniepoint integration settings saved successfully.";
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
            $stmt = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, account_id, type, amount, reference, description, status) VALUES (?, ?, 'inflow', ?, ?, ?, 'completed')");
            $accountId = (int)($_POST['account_id'] ?? 0);
            $stmt->execute([$this->tenantId, $accountId, $amount, $merchantReference, $description]);

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

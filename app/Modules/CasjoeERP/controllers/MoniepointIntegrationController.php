<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Controller;
use App\Core\Database;

class MoniepointIntegrationController extends Controller
{
    /**
     * View the integration settings page
     */
    public function settings()
    {
        $tenantId = $_SESSION['tenant_id'];
        $pdo = Database::getInstance()->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint'");
        $stmt->execute([$tenantId]);
        $integration = $stmt->fetch();

        // Render view (assumes we will create a view or append to existing settings)
        $this->render('CasjoeERP::moniepoint_settings', [
            'integration' => $integration
        ]);
    }

    /**
     * Save the integration credentials
     */
    public function saveSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/casjoe-erp/settings');
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $clientId = $_POST['client_id'] ?? '';
        $clientSecret = $_POST['client_secret'] ?? '';
        $terminalSerial = $_POST['terminal_serial'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT id FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint'");
        $stmt->execute([$tenantId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $update = $pdo->prepare("UPDATE erp_payment_integrations SET client_id = ?, client_secret = ?, terminal_serial = ?, is_active = ? WHERE tenant_id = ? AND provider = 'moniepoint'");
            $update->execute([$clientId, $clientSecret, $terminalSerial, $isActive, $tenantId]);
        } else {
            $insert = $pdo->prepare("INSERT INTO erp_payment_integrations (tenant_id, provider, client_id, client_secret, terminal_serial, is_active) VALUES (?, 'moniepoint', ?, ?, ?, ?)");
            $insert->execute([$tenantId, $clientId, $clientSecret, $terminalSerial, $isActive]);
        }

        // Set flash message
        $_SESSION['flash_message'] = "Moniepoint integration settings saved successfully.";
        $this->redirect('/casjoe-erp/settings/moniepoint');
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

        $tenantId = $_SESSION['tenant_id'];
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? 'POS Payment';

        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
            return;
        }

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM erp_payment_integrations WHERE tenant_id = ? AND provider = 'moniepoint' AND is_active = 1");
        $stmt->execute([$tenantId]);
        $integration = $stmt->fetch();

        if (!$integration || empty($integration['client_id']) || empty($integration['client_secret']) || empty($integration['terminal_serial'])) {
            echo json_encode(['success' => false, 'message' => 'Moniepoint POS is not configured or disabled.']);
            return;
        }

        $moniepoint = new \App\Modules\CasjoeERP\Services\MoniepointService(
            $integration['client_id'],
            $integration['client_secret'],
            $integration['terminal_serial']
        );

        $merchantReference = 'ERP-' . $tenantId . '-' . time() . '-' . rand(1000, 9999);

        try {
            // Push payment request
            $response = $moniepoint->pushPayment($amount, $merchantReference);
            
            // Assume success if no exception is thrown and save the transaction as inflow in ERP ledger
            $stmt = $pdo->prepare("INSERT INTO erp_transactions (tenant_id, account_id, type, amount, reference, description, status) VALUES (?, ?, 'inflow', ?, ?, ?, 'completed')");
            // Find a default account ID for this tenant (or pass from frontend). Hardcoding 0 for demo purposes unless passed.
            $accountId = $_POST['account_id'] ?? 0;
            $stmt->execute([$tenantId, $accountId, $amount, $merchantReference, $description]);

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

<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Services\AIService;

class BusinessBuilderController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = \App\Core\TenantContext::getTenantId();
    }

    public function index()
    {
        require __DIR__ . '/../Views/ai/builder.php';
    }

    public function chat()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            echo json_encode(['error' => 'Empty message']);
            return;
        }

        // State Machine Initialization
        if (!isset($_SESSION['ai_builder_state'])) {
            $_SESSION['ai_builder_state'] = 'industry';
        }

        $state = $_SESSION['ai_builder_state'];
        $response = '';
        $quickReplies = [];
        $action = null;

        if ($state === 'industry') {
            // User just answered what their industry is
            $_SESSION['ai_builder_industry'] = $message;
            $_SESSION['ai_builder_state'] = 'size';
            $response = "Got it! Running a " . htmlspecialchars($message) . " business is exciting. Do you work solo, or do you have a team?";
            $quickReplies = ["I am a solopreneur", "I have a small team", "We are a large agency"];
        } elseif ($state === 'size') {
            $_SESSION['ai_builder_size'] = $message;
            $_SESSION['ai_builder_state'] = 'name';
            $response = "Perfect! What is the official name of your business?";
        } elseif ($state === 'name') {
            $_SESSION['ai_builder_name'] = $message;
            
            // Save to DB
            $stmt = $this->pdo->prepare("UPDATE tenants SET name = ? WHERE id = ?");
            $stmt->execute([$message, $this->tenantId]);

            $_SESSION['ai_builder_state'] = 'currency';
            $response = "Great name! What currency will you primarily bill your clients in?";
            $quickReplies = ["USD", "EUR", "GBP", "NGN", "ZAR", "KES"];
        } elseif ($state === 'currency') {
            $_SESSION['ai_builder_currency'] = $message;
            
            // Save to DB
            $stmt = $this->pdo->prepare("UPDATE tenants SET currency = ? WHERE id = ?");
            $stmt->execute([$message, $this->tenantId]);

            $stmtUser = $this->pdo->prepare("UPDATE users SET currency = ? WHERE id = ?");
            $stmtUser->execute([$message, $_SESSION['user_id']]);
            $_SESSION['currency'] = $message;

            $_SESSION['ai_builder_state'] = 'complete';
            $response = "All set! I've saved your business details. Now, I'll redirect you to the settings page so you can configure your payment gateway and start receiving payments.";
            $action = 'redirect_to_payment';
            
            $this->completeOnboardingImplicit($message);
        } else {
            $response = "Your onboarding is already complete! Redirecting...";
            $action = 'redirect_to_payment';
        }

        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'response' => $response,
            'quick_replies' => $quickReplies,
            'action' => $action
        ]);
    }

    private function completeOnboardingImplicit($currency)
    {
        $currencies = array_unique([$currency, 'USD']);
        foreach ($currencies as $curr) {
            $stmt = $this->pdo->prepare("SELECT id FROM cp_wallets WHERE tenant_id = ? AND currency = ?");
            $stmt->execute([$this->tenantId, $curr]);
            if (!$stmt->fetch()) {
                $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                         ->execute([$this->tenantId, $_SESSION['user_id'], $curr]);
            }
        }
        $this->pdo->prepare("UPDATE tenants SET onboarding_step = 8 WHERE id = ?")
             ->execute([$this->tenantId]);
    }

    public function complete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // Auto-create wallets (Local Currency + USD)
        $stmt = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmt->execute([$this->tenantId]);
        $localCurrency = $stmt->fetchColumn() ?: 'NGN';

        $currencies = array_unique([$localCurrency, 'USD']);
        foreach ($currencies as $curr) {
            $stmt = $this->pdo->prepare("SELECT id FROM cp_wallets WHERE tenant_id = ? AND currency = ?");
            $stmt->execute([$this->tenantId, $curr]);
            if (!$stmt->fetch()) {
                $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                         ->execute([$this->tenantId, $_SESSION['user_id'], $curr]);
            }
        }

        // Complete onboarding
        $this->pdo->prepare("UPDATE tenants SET onboarding_step = 8 WHERE id = ?")
             ->execute([$this->tenantId]);

        echo json_encode(['success' => true]);
    }

    public function processWizard()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $businessName = $_POST['business_name'] ?? 'My Business';
        $industry = $_POST['industry'] ?? 'Other';
        $currency = $_POST['currency'] ?? 'USD';
        
        $productName = $_POST['product_name'] ?? '';
        $productPrice = (float)($_POST['product_price'] ?? 0);

        // Update Tenant Info
        $stmt = $this->pdo->prepare("UPDATE tenants SET name = ?, currency = ? WHERE id = ?");
        $stmt->execute([$businessName, $currency, $this->tenantId]);

        $stmtUser = $this->pdo->prepare("UPDATE users SET currency = ? WHERE id = ?");
        $stmtUser->execute([$currency, $_SESSION['user_id']]);
        $_SESSION['currency'] = $currency;

        // Ensure Wallets
        $currencies = array_unique([$currency, 'USD']);
        foreach ($currencies as $curr) {
            $stmt = $this->pdo->prepare("SELECT id FROM cp_wallets WHERE tenant_id = ? AND currency = ?");
            $stmt->execute([$this->tenantId, $curr]);
            if (!$stmt->fetch()) {
                $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                         ->execute([$this->tenantId, $_SESSION['user_id'], $curr]);
            }
        }

        // Insert first product if provided
        if (!empty($productName)) {
            $sku = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $productName), 0, 3)) . rand(100, 999);
            $stmt = $this->pdo->prepare("INSERT INTO erp_products (tenant_id, name, sku, price, cost, stock_level) VALUES (?, ?, ?, ?, 0, 0)");
            $stmt->execute([$this->tenantId, $productName, $sku, $productPrice]);
        }

        // Complete onboarding
        $this->pdo->prepare("UPDATE tenants SET onboarding_step = 8 WHERE id = ?")
             ->execute([$this->tenantId]);

        echo json_encode(['success' => true]);
    }
}

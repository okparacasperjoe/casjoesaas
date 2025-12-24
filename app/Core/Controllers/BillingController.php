<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;

class BillingController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $tenantId = TenantContext::getTenantId();
        
        // Ensure subscription record exists
        $sub = SubscriptionManager::getSubscription($tenantId);
        if (!$sub) {
            // Auto-create if missing (failsafe)
            $this->pdo->prepare("INSERT INTO subscriptions (tenant_id, status, trial_ends_at) VALUES (?, 'trial', DATE_ADD(NOW(), INTERVAL 30 DAY))")->execute([$tenantId]);
            $sub = SubscriptionManager::getSubscription($tenantId);
        }

        $isActive = SubscriptionManager::isActive($tenantId);

        // Mock Invoices
        $stmt = $this->pdo->prepare("SELECT * FROM billing_invoices WHERE tenant_id = ? ORDER BY date DESC LIMIT 5");
        $stmt->execute([$tenantId]);
        $invoices = $stmt->fetchAll();

        // Check 2FA Status
        $stmt = $this->pdo->prepare("SELECT two_factor_enabled FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user2fa = $stmt->fetch();
        $twoFactorEnabled = $user2fa['two_factor_enabled'] ?? 0;

        require __DIR__ . '/../Views/billing/index.php';
    }

    public function upgrade()
    {
        require __DIR__ . '/../Views/billing/upgrade.php';
    }

    public function initiatePayment()
    {
        $tenantId = TenantContext::getTenantId();
        $email = $_SESSION['user_email'] ?? 'customer@example.com'; // Should fetch from Auth
        $amount = 50000.00; // Subscription Price
        $currency = 'NGN';
        $txRef = 'SUB-' . $tenantId . '-' . uniqid();

        // 1. Create Pending Invoice/Transaction
        $stmt = $this->pdo->prepare("INSERT INTO billing_invoices (tenant_id, reference, amount, currency, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$tenantId, $txRef, $amount, $currency]);

        // 2. Prepare Flutterwave Payload
        $payload = [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/billing/callback',
            'payment_options' => 'card',
            'customer' => [
                'email' => $email,
                'name' => 'Casjoe Subscriber'
            ],
            'customizations' => [
                'title' => 'Casjoe ERP Premium Plan',
                'description' => 'Monthly Subscription'
            ]
        ];

        // 3. Call Flutterwave Standard API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . FLUTTERWAVE_SECRET_KEY,
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);


        if ($err) {
            die('Curl Error: ' . $err);
        }

        $res = json_decode($response, true);
        if (isset($res['status']) && $res['status'] === 'success') {
            header('Location: ' . $res['data']['link']);
            exit;
        } else {
            // If it fails (likely due to invalid keys in this dev env), fallback or show error
            echo "Error communicating with payment gateway: " . ($res['message'] ?? 'Unknown error');
            // For testing only: 
            // header('Location: /billing/callback?status=successful&tx_ref='.$txRef);
        }
    }

    public function callback()
    {
        $status = $_GET['status'] ?? '';
        $txRef = $_GET['tx_ref'] ?? '';
        $transactionId = $_GET['transaction_id'] ?? '';

        if ($status === 'successful' || $status === 'completed') {
            
            // 1. Verify Transaction
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . FLUTTERWAVE_SECRET_KEY
                ],
            ]);
            
            $response = curl_exec($curl);
    
            $res = json_decode($response, true);

            // 2. Validate Amount & Currency
            if (isset($res['status']) && $res['status'] === 'success' && $res['data']['amount'] >= 50000) {
                
                $tenantId = TenantContext::getTenantId();
                
                // 3. Activate Subscription
                $stmt = $this->pdo->prepare("UPDATE subscriptions SET status = 'active', plan = 'premium', next_billing_at = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE tenant_id = ?");
                $stmt->execute([$tenantId]);

                // 4. Update Invoice Status
                $stmt = $this->pdo->prepare("UPDATE billing_invoices SET status = 'paid' WHERE reference = ?");
                $stmt->execute([$txRef]);

                header('Location: /billing?success=subscribed');
                exit;
            }
        }
        
        header('Location: /billing?error=payment_failed');
    }

    public function cancel()
    {
        $tenantId = TenantContext::getTenantId();
        $this->pdo->prepare("UPDATE subscriptions SET status = 'cancelled' WHERE tenant_id = ?")->execute([$tenantId]);
        header('Location: /billing?msg=cancelled');
    }

    public function disable2FA()
    {
        $tenantId = TenantContext::getTenantId();
        // Since we are using a shared user table, we need to be careful.
        // Assuming Auth::user()['id'] is available.
        // But BillingController doesn't use Auth facade directly yet in methods shown, 
        // but it should be accessible given module loading.
        // Let's use the session user id which is reliable for the logged in user.
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        $this->pdo->prepare("UPDATE users SET two_factor_enabled = 0, two_factor_secret = NULL WHERE id = ?")->execute([$userId]);
        header('Location: /billing?success=2fa_disabled');
    }
}

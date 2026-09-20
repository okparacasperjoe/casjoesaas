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
        if (!\App\Core\Auth::check()) {
            header('Location: /login');
            exit;
        }
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

        // Fetch AI Credits (from $sub array which is fetched from subscriptions table)
        $aiCredits = $sub['ai_credits'] ?? 0;
        $aiTokensUsed = $sub['ai_tokens_used'] ?? 0;
        $aiTokensLimit = $sub['ai_tokens_limit'] ?? 0;
        $totalTokens = max(0, $aiTokensLimit - $aiTokensUsed) + $aiCredits;

        // Fetch Subscription Modules - Order All Access Bundle first, Exclude Support module
        $stmt = $this->pdo->prepare("SELECT * FROM modules WHERE slug NOT IN ('support', 'casjoe-support', 'helpdesk') AND name NOT LIKE '%Support%' ORDER BY CASE WHEN slug = 'all-access-bundle' THEN 0 ELSE 1 END, id ASC");
        $stmt->execute();
        $plans = array_filter($stmt->fetchAll(), function($p) {
            return stripos($p['name'] ?? '', 'Support') === false && 
                   !in_array(strtolower($p['slug'] ?? ''), ['support', 'casjoe-support', 'helpdesk']);
        });

        // Remove duplicates by name
        $uniquePlans = [];
        foreach ($plans as $p) {
            $nameKey = trim(strtolower($p['name'] ?? ''));
            if (!isset($uniquePlans[$nameKey])) {
                $uniquePlans[$nameKey] = $p;
            }
        }
        $plans = array_values($uniquePlans);

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
        $selectedPlan = null;
        $slug = $_GET['slug'] ?? ($_GET['plan'] ?? '');
        if (!empty($slug)) {
            $stmt = $this->pdo->prepare("SELECT * FROM modules WHERE slug = ? OR id = ?");
            $stmt->execute([$slug, $slug]);
            $selectedPlan = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        require __DIR__ . '/../Views/billing/upgrade.php';
    }

    public function initiatePayment()
    {
        $tenantId = TenantContext::getTenantId();
        $email = $_SESSION['user_email'] ?? 'customer@example.com'; // Should fetch from Auth
        
        $type = $_POST['type'] ?? 'subscription';
        $gateway = $_POST['gateway'] ?? 'paystack'; // Default to Paystack
        
        $user = \App\Core\Auth::user();
        $tenant = TenantContext::getTenant();
        $currency = $_SESSION['currency'] ?? ($user['currency'] ?? ($tenant['currency'] ?? 'NGN'));
        if (empty($currency)) $currency = 'NGN';
        $isNaira = (strtoupper($currency) === 'NGN');
        
        if ($type === 'ai_topup') {
            $packKey = $_POST['pack'] ?? 'ai_5k';
            if ($packKey === 'ai_10k') {
                $amount = $isNaira ? 10000.00 : 10.00;
                $title = "Casjoe AI Engine - 10,000 Credits";
            } else {
                $packKey = 'ai_5k';
                $amount = $isNaira ? 5000.00 : 5.00;
                $title = "Casjoe AI Engine - 5,000 Credits";
            }
            $txRef = 'AI-' . $tenantId . '-' . $packKey . '-' . uniqid();
        } elseif ($type === 'ai_employee') {
            $amount = $isNaira ? AI_EMPLOYEE_PRICE_NGN : AI_EMPLOYEE_PRICE_USD;
            $roleKey = $_POST['pack'] ?? 'unknown';
            $title = "Casjoe AI Employee - " . ucfirst(str_replace('_', ' ', $roleKey));
            $txRef = 'AIE-' . $tenantId . '-' . $roleKey . '-' . uniqid();
        } else {
            $amount = $isNaira ? 28000.00 : 21.00;
            $title = "Casjoe ERP All Access Module";
            $txRef = 'SUB-' . $tenantId . '-all-access-' . uniqid();
        }
        
        $currencyCode = $isNaira ? 'NGN' : 'USD';

        // 1. Create Pending Invoice/Transaction
        $stmt = $this->pdo->prepare("INSERT INTO billing_invoices (tenant_id, reference, amount, currency, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$tenantId, $txRef, $amount, $currencyCode]);

        if ($gateway === 'bank_transfer') {
            header('Location: /billing/bank-transfer?amount=' . urlencode($amount) . '&currency=' . urlencode($currencyCode) . '&ref=' . urlencode($txRef));
            exit;
        }
        if ($gateway === 'coupon') {
            header('Location: /billing#couponSection');
            exit;
        }

        if ($gateway === 'paystack') {
            // Paystack transaction init
            $payload = [
                'email' => $email,
                'amount' => $amount * 100, // Paystack uses kobo/cents
                'currency' => $currencyCode,
                'reference' => $txRef,
                'callback_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/billing/callback'
            ];
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . (defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ($_ENV['PAYSTACK_SECRET_KEY'] ?? '')),
                    "Content-Type: application/json"
                ],
            ]);
            $response = curl_exec($curl);
            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === true) {
                header('Location: ' . $res['data']['authorization_url']);
                exit;
            } else {
                echo "Error communicating with Paystack: " . ($res['message'] ?? 'Unknown error');
                exit;
            }
        } else {
            // Flutterwave transaction init
            $payload = [
                'tx_ref' => $txRef,
                'amount' => $amount,
                'currency' => $currencyCode,
                'redirect_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/billing/callback',
                'payment_options' => 'card',
                'customer' => [
                    'email' => $email,
                    'name' => 'Casjoe Subscriber'
                ],
                'customizations' => [
                    'title' => $title,
                    'description' => $title
                ]
            ];

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
                    'Authorization: Bearer ' . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '')),
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
                echo "Error communicating with Flutterwave: " . ($res['message'] ?? 'Unknown error');
            }
        }
    }

    public function callback()
    {
        $status = $_GET['status'] ?? '';
        $txRef = $_GET['tx_ref'] ?? '';
        $transactionId = $_GET['transaction_id'] ?? '';
        $reference = $_GET['reference'] ?? ''; // Paystack reference

        $verified = false;
        $amountPaid = 0;
        $currencyPaid = 'NGN';
        $finalTxRef = '';

        if (!empty($reference)) {
            // Paystack verification
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . PAYSTACK_SECRET_KEY
                ],
            ]);
            $response = curl_exec($curl);
            $res = json_decode($response, true);

            if (isset($res['status']) && $res['status'] === true && $res['data']['status'] === 'success') {
                $verified = true;
                $amountPaid = (float)($res['data']['amount'] / 100); // convert kobo back
                $currencyPaid = $res['data']['currency'] ?? 'NGN';
                $finalTxRef = $reference;
            }
        } elseif (($status === 'successful' || $status === 'completed') && !empty($transactionId)) {
            // Flutterwave verification
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

            if (isset($res['status']) && $res['status'] === 'success') {
                $verified = true;
                $amountPaid = (float)$res['data']['amount'];
                $currencyPaid = $res['data']['currency'] ?? 'NGN';
                $finalTxRef = $txRef;
            }
        }

        if ($verified) {
            $tenantId = TenantContext::getTenantId();
            $userId = $_SESSION['user_id'] ?? 0;

            if (strpos($finalTxRef, 'AI-') === 0) {
                // It is an AI Top Up
                $parts = explode('-', $finalTxRef);
                $packKey = $parts[2] ?? 'ai_5k';
                
                $isTxnNaira = (strtoupper($currencyPaid) === 'NGN');
                $expectedAmount = $isTxnNaira ? (($packKey === 'ai_10k') ? 10000 : 5000) : (($packKey === 'ai_10k') ? 10 : 5);
                
                if ($amountPaid >= $expectedAmount) {
                    $aiService = new \App\Core\AI\AICreditService();
                    $aiService->topUp($tenantId, $userId, $packKey, $finalTxRef, $amountPaid, $currencyPaid);
                    header('Location: /billing?success=ai_topup');
                    exit;
                }
            } elseif (strpos($finalTxRef, 'INV-') === 0) {
                // It is an ERP Invoice Payment
                $parts = explode('-', $finalTxRef);
                $uuid = $parts[1] ?? '';
                
                $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE uuid = ? AND status != 'paid'");
                $stmt->execute([$uuid]);
                $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($invoice && $amountPaid >= (float)$invoice['total_amount']) {
                     $stmtUpdate = $this->pdo->prepare("UPDATE erp_invoices SET status = 'paid' WHERE uuid = ?");
                     $stmtUpdate->execute([$uuid]);
                     
                     // Also log transaction
                     $stmtTrans = $this->pdo->prepare("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES (?, ?, ?, 'income', CURDATE())");
                     $stmtTrans->execute([$invoice['tenant_id'], "Invoice Payment #" . $invoice['id'], $invoice['total_amount']]);
                     
                     header("Location: /invoice/$uuid?success=1");
                     exit;
                } else {
                     header("Location: /invoice/$uuid?error=payment_failed");
                     exit;
                }
            } else {
                $isTxnNaira = (strtoupper($currencyPaid) === 'NGN');
                $expectedAmount = $isTxnNaira ? 28000 : 21;

                if ($amountPaid >= $expectedAmount) {
                    // Activate Subscription
                    $stmt = $this->pdo->prepare("UPDATE subscriptions SET status = 'active', plan = 'all-access-bundle', next_billing_at = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE tenant_id = ?");
                    $stmt->execute([$tenantId]);

                    // Grant AI credits for the plan (all-access-bundle grants 15,000 tokens)
                    $aiService = new \App\Core\AI\AICreditService();
                    $aiService->grantPlanAllotment($tenantId, 'all-access-bundle');

                    // Update Invoice Status
                    $stmt = $this->pdo->prepare("UPDATE billing_invoices SET status = 'paid' WHERE reference = ?");
                    $stmt->execute([$finalTxRef]);

                    header('Location: /billing?success=subscribed');
                    exit;
                }
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

    public function redeemCoupon()
    {
        $tenantId = TenantContext::getTenantId();
        // Support both name="code" (from index.php) and name="coupon_code" via GET or POST
        $code = strtoupper(trim($_REQUEST['code'] ?? $_POST['code'] ?? $_POST['coupon_code'] ?? ''));

        if (empty($code)) {
            header('Location: /billing');
            exit;
        }

        // Search in system_billing_coupons
        $stmt = $this->pdo->prepare("SELECT * FROM system_billing_coupons WHERE code = ?");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$coupon) {
            header('Location: /billing?error=' . urlencode('Invalid coupon code: ' . $code));
            exit;
        }

        if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < time()) {
            header('Location: /billing?error=' . urlencode('This coupon code has expired.'));
            exit;
        }

        // Check usage limit
        $maxUses = intval($coupon['max_uses'] ?? 0);
        $usageCount = intval($coupon['usage_count'] ?? 0);
        if ($maxUses > 0 && $usageCount >= $maxUses) {
            header('Location: /billing?error=' . urlencode('This coupon code has reached its maximum usage limit.'));
            exit;
        }

        $durationMonths = intval($coupon['duration_months'] ?? 1);
        if ($durationMonths < 1) $durationMonths = 1;

        // Increment usage count
        $stmt = $this->pdo->prepare("UPDATE system_billing_coupons SET usage_count = usage_count + 1 WHERE id = ?");
        $stmt->execute([$coupon['id']]);

        // Check Context (e.g., ai_employee_sales_manager)
        $context = $_POST['context'] ?? '';
        if (strpos($context, 'ai_employee_') === 0) {
            $role = str_replace('ai_employee_', '', $context);
            $this->pdo->prepare(
                "INSERT INTO tenant_ai_employees (tenant_id, employee_type, status, created_at) 
                 VALUES (?, ?, 'active', NOW()) 
                 ON DUPLICATE KEY UPDATE status = 'active'"
            )->execute([$tenantId, $role]);
            
            header('Location: /billing?msg=' . urlencode("Coupon redeemed! AI Employee activated successfully."));
            exit;
        }

        // Activate or extend subscription WITH plan = all-access-bundle
        $stmt = $this->pdo->prepare("SELECT * FROM subscriptions WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        $sub = $stmt->fetch(\PDO::FETCH_ASSOC);

        $newEnds = null;
        if ($sub) {
            $currentEnds = !empty($sub['trial_ends_at']) && strtotime($sub['trial_ends_at']) > time()
                ? $sub['trial_ends_at']
                : date('Y-m-d H:i:s');
            $newEnds = date('Y-m-d H:i:s', strtotime("+$durationMonths months", strtotime($currentEnds)));
            $this->pdo->prepare(
                "UPDATE subscriptions SET status = 'active', plan = 'all-access-bundle', trial_ends_at = ?, next_billing_at = ? WHERE tenant_id = ?"
            )->execute([$newEnds, $newEnds, $tenantId]);
        } else {
            $newEnds = date('Y-m-d H:i:s', strtotime("+$durationMonths months"));
            $this->pdo->prepare(
                "INSERT INTO subscriptions (tenant_id, status, plan, trial_ends_at, next_billing_at) VALUES (?, 'active', 'all-access-bundle', ?, ?)"
            )->execute([$tenantId, $newEnds, $newEnds]);
        }

        // Grant ALL modules to the tenant
        $allModules = $this->pdo->query("SELECT id FROM modules")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($allModules as $moduleId) {
            $this->pdo->prepare(
                "INSERT INTO tenant_modules (tenant_id, module_id, status, expires_at)
                 VALUES (?, ?, 'active', ?)
                 ON DUPLICATE KEY UPDATE status = 'active', expires_at = ?"
            )->execute([$tenantId, $moduleId, $newEnds, $newEnds]);
        }

        // Grant AI credits (all-access-bundle allotment)
        try {
            if (class_exists('\App\Core\Services\AIService') && method_exists('\App\Core\Services\AIService', 'grantPlanAllotment')) {
                $aiService = new \App\Core\Services\AIService($tenantId);
                $aiService->grantPlanAllotment($tenantId, 'all-access-bundle');
            }
        } catch (\Throwable $e) {
            // Non-fatal: AI credits not critical to coupon activation
        }

        header('Location: /billing?msg=' . urlencode('Coupon code ' . $code . ' applied successfully! All Access activated for ' . $durationMonths . ' month(s).'));
        exit;
    }

    public function bankTransfer()
    {
        $amount = $_GET['amount'] ?? 28000;
        $currency = $_GET['currency'] ?? 'NGN';
        $ref = $_GET['ref'] ?? ('TRANSFER-' . TenantContext::getTenantId() . '-' . time());

        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $dbSettings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $settings = [
            'bank_name' => $dbSettings['offline_deposit_bank'] ?? ($dbSettings['bank_name'] ?? ($dbSettings['bank_details_bank'] ?? 'Casjoe Enterprise Apps Bank')),
            'account_name' => $dbSettings['offline_deposit_account_name'] ?? ($dbSettings['account_name'] ?? ($dbSettings['bank_details_account_name'] ?? ($dbSettings['bank_account_name'] ?? 'Casjoe Technologies Ltd'))),
            'account_number' => $dbSettings['offline_deposit_account_number'] ?? ($dbSettings['account_number'] ?? ($dbSettings['bank_details_number'] ?? ($dbSettings['bank_account_number'] ?? '1029384756'))),
            'bank_instructions' => $dbSettings['offline_deposit_instructions'] ?? ($dbSettings['bank_instructions'] ?? 'Please include your Reference Code in the transfer narration. Activation is completed within 15 minutes of receipt.')
        ];
        require __DIR__ . '/../Views/billing/bank_transfer.php';
    }

    public function validateCoupon()
    {
        header('Content-Type: application/json');
        $code = strtoupper(trim($_REQUEST['code'] ?? ($_POST['coupon_code'] ?? '')));
        if (empty($code)) {
            echo json_encode(['valid' => false, 'error' => 'Please enter a coupon code.']);
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM system_billing_coupons WHERE code = ?");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$coupon) {
            echo json_encode(['valid' => false, 'error' => 'Invalid coupon code: ' . $code]);
            exit;
        }

        if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < time()) {
            echo json_encode(['valid' => false, 'error' => 'This coupon code has expired.']);
            exit;
        }

        $durationMonths = intval($coupon['duration_months'] ?? 1);
        if ($durationMonths < 1) $durationMonths = 1;

        $type = $coupon['type'] ?? 'percent';
        $val = floatval($coupon['value'] ?? 0);

        $baseUsd = isset($_REQUEST['base_usd']) ? floatval($_REQUEST['base_usd']) : 21.00;
        $baseNgn = isset($_REQUEST['base_ngn']) ? floatval($_REQUEST['base_ngn']) : 28000.00;

        if ($type === 'percent') {
            $discountUsd = round(($baseUsd * $val) / 100, 2);
            $discountNgn = round(($baseNgn * $val) / 100, 2);
            $desc = "{$val}% Off Discount ({$durationMonths} Month(s) Access)";
        } else {
            $discountUsd = min($baseUsd, $val);
            $discountNgn = min($baseNgn, $val);
            $desc = "Fixed Discount ({$durationMonths} Month(s) Access)";
        }

        $finalUsd = max(0, $baseUsd - $discountUsd);
        $finalNgn = max(0, $baseNgn - $discountNgn);

        echo json_encode([
            'valid' => true,
            'code' => $coupon['code'],
            'type' => $type,
            'value' => $val,
            'duration_months' => $durationMonths,
            'base_usd' => $baseUsd,
            'base_ngn' => $baseNgn,
            'discount_usd' => $discountUsd,
            'discount_ngn' => $discountNgn,
            'final_usd' => $finalUsd,
            'final_ngn' => $finalNgn,
            'description' => $desc
        ]);
        exit;
    }
}

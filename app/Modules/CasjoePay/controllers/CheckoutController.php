<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class CheckoutController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Hosted checkout page
     * GET /pay/checkout
     */
    public function index()
    {
        $amount = floatval($_REQUEST['amount'] ?? 0);
        $currency = strtoupper(trim($_REQUEST['currency'] ?? 'NGN'));
        if (empty($currency)) $currency = 'NGN';

        $title = trim($_REQUEST['title'] ?? $_REQUEST['description'] ?? 'Product / Service');
        $description = $title;
        $callback = trim($_REQUEST['callback'] ?? '');
        $cancel = trim($_REQUEST['cancel'] ?? '');
        $ref = trim($_REQUEST['reference'] ?? $_REQUEST['ref'] ?? '');
        if (empty($ref)) {
            $ref = 'CHK-' . strtoupper(uniqid());
        }

        $email = trim($_REQUEST['email'] ?? '');
        $name = trim($_REQUEST['name'] ?? '');
        $ownerId = intval($_REQUEST['owner_id'] ?? 0);
        $subId = trim($_REQUEST['sub_id'] ?? '');

        // If reference is from a sales funnel (e.g. funnel_10), inspect funnel_payments
        if (strpos($ref, 'funnel_') === 0) {
            $paymentId = (int)str_replace('funnel_', '', $ref);
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM funnel_payments WHERE id = ?");
                $stmt->execute([$paymentId]);
                $payment = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($payment) {
                    if ($amount <= 0 && !empty($payment['amount'])) {
                        $amount = floatval($payment['amount']);
                    }
                    if (!empty($payment['currency'])) {
                        $currency = strtoupper($payment['currency']);
                    }
                    if (($title === 'Product / Service' || empty($title)) && !empty($payment['description'])) {
                        $title = $payment['description'];
                        $description = $title;
                    }

                    // Look up tenant and owner from sales_funnels
                    $stmtF = $this->pdo->prepare("SELECT tenant_id, user_id, name FROM sales_funnels WHERE id = ?");
                    $stmtF->execute([$payment['funnel_id']]);
                    $funnel = $stmtF->fetch(\PDO::FETCH_ASSOC);
                    if ($funnel) {
                        if ($ownerId <= 0 && !empty($funnel['user_id'])) {
                            $ownerId = (int)$funnel['user_id'];
                        }
                    }

                    // Look up contact details if empty
                    if (empty($email) && !empty($payment['contact_id'])) {
                        $stmtC = $this->pdo->prepare("SELECT email, name FROM funnel_contacts WHERE id = ?");
                        $stmtC->execute([$payment['contact_id']]);
                        $contact = $stmtC->fetch(\PDO::FETCH_ASSOC);
                        if ($contact) {
                            $email = $contact['email'] ?? '';
                            $name = $contact['name'] ?? '';
                        }
                    }
                    // Fallback to session contact data if present
                    if (empty($email) && !empty($_SESSION['funnel_form_data_' . $payment['funnel_id']]['email'])) {
                        $email = $_SESSION['funnel_form_data_' . $payment['funnel_id']]['email'];
                    }
                    if (empty($name) && !empty($_SESSION['funnel_form_data_' . $payment['funnel_id']]['name'])) {
                        $name = $_SESSION['funnel_form_data_' . $payment['funnel_id']]['name'];
                    }
                }
            } catch (\Throwable $e) {
                // Ignore DB lookup error and continue with request params
            }
        }

        // Render checkout view
        require __DIR__ . '/../Views/public_checkout.php';
    }

    /**
     * Process checkout form submission
     * POST /pay/checkout/process
     */
    public function process()
    {
        $amount = floatval($_POST['amount'] ?? 0);
        $currency = strtoupper(trim($_POST['currency'] ?? 'NGN'));
        if (empty($currency)) $currency = 'NGN';

        $ref = trim($_POST['ref'] ?? $_POST['reference'] ?? '');
        if (empty($ref)) {
            $ref = 'CHK-' . strtoupper(uniqid());
        }

        $title = trim($_POST['title'] ?? $_POST['description'] ?? 'Payment');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $name = trim($_POST['name'] ?? 'Customer');
        $callback = trim($_POST['callback'] ?? '');
        $cancel = trim($_POST['cancel'] ?? '');
        $ownerId = intval($_POST['owner_id'] ?? 0);
        $subId = trim($_POST['sub_id'] ?? '');

        if (!$email) {
            $error = "Please provide a valid email address.";
            require __DIR__ . '/../Views/public_checkout.php';
            return;
        }

        if ($amount <= 0) {
            $error = "Payment amount must be greater than zero.";
            require __DIR__ . '/../Views/public_checkout.php';
            return;
        }

        $tenantId = 1;
        $userId = $ownerId > 0 ? $ownerId : 1;

        // If reference is funnel, resolve tenant & user
        if (strpos($ref, 'funnel_') === 0) {
            $paymentId = (int)str_replace('funnel_', '', $ref);
            try {
                $stmt = $this->pdo->prepare("SELECT f.tenant_id, f.user_id FROM funnel_payments p JOIN sales_funnels f ON p.funnel_id = f.id WHERE p.id = ?");
                $stmt->execute([$paymentId]);
                $ownerInfo = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($ownerInfo) {
                    $tenantId = (int)$ownerInfo['tenant_id'];
                    $userId = (int)$ownerInfo['user_id'];
                }
            } catch (\Throwable $e) {}
        }

        // Unique gateway reference
        $gatewayRef = 'CHK-' . strtoupper(uniqid()) . '-' . preg_replace('/[^A-Za-z0-9_-]/', '', $ref);

        // Store pending transaction in cp_transactions
        $meta = [
            'orig_ref' => $ref,
            'gateway_ref' => $gatewayRef,
            'callback' => $callback,
            'cancel' => $cancel,
            'payer_name' => $name,
            'payer_email' => $email,
            'owner_id' => $userId,
            'tenant_id' => $tenantId,
            'sub_id' => $subId,
            'description' => $title
        ];

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) 
                 VALUES (?, ?, ?, 'credit', ?, ?, 'pending', ?, ?, NOW())"
            );
            $stmt->execute([$tenantId, $userId, $gatewayRef, $amount, $currency, $title, json_encode($meta)]);

            if (strpos($ref, 'funnel_') === 0) {
                $pId = (int)str_replace('funnel_', '', $ref);
                $this->pdo->prepare("UPDATE funnel_payments SET transaction_ref = ? WHERE id = ?")->execute([$gatewayRef, $pId]);
            }
        } catch (\Throwable $e) {
            // Silently continue if cp_transactions insert fails
        }

        $gatewayCallback = 'https://' . $_SERVER['HTTP_HOST'] . '/pay/checkout/callback?ref=' . urlencode($gatewayRef);

        // 1. Try Paystack for NGN or if Paystack is default
        $paystackKey = defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ($_ENV['PAYSTACK_SECRET_KEY'] ?? '');
        if (!empty($paystackKey) && $currency === 'NGN') {
            $payload = [
                'email' => $email,
                'amount' => round($amount * 100), // kobo
                'currency' => $currency,
                'reference' => $gatewayRef,
                'callback_url' => $gatewayCallback,
                'metadata' => [
                    'custom_fields' => [
                        ['display_name' => 'Order Reference', 'variable_name' => 'orig_ref', 'value' => $ref],
                        ['display_name' => 'Customer Name', 'variable_name' => 'payer_name', 'value' => $name]
                    ]
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $paystackKey,
                    "Content-Type: application/json"
                ],
            ]);
            $response = curl_exec($curl);
            $curlErr = curl_error($curl);
            curl_close($curl);

            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === true && !empty($res['data']['authorization_url'])) {
                header('Location: ' . $res['data']['authorization_url']);
                exit;
            }
        }

        // 2. Try Flutterwave (Multi-currency or fallback)
        $flwKey = defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '');
        if (!empty($flwKey)) {
            $payload = [
                'tx_ref' => $gatewayRef,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => $gatewayCallback,
                'customer' => ['email' => $email, 'name' => $name],
                'customizations' => ['title' => $title, 'description' => $title]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $flwKey,
                    'Content-Type: application/json'
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === 'success' && !empty($res['data']['link'])) {
                header('Location: ' . $res['data']['link']);
                exit;
            }
        }

        // If gateway communication failed
        $error = "Payment gateway is currently unavailable. Please check back shortly.";
        require __DIR__ . '/../Views/public_checkout.php';
    }

    /**
     * Handle payment gateway callback
     * GET /pay/checkout/callback
     */
    public function callback()
    {
        $gatewayRef = trim($_GET['ref'] ?? $_GET['reference'] ?? $_GET['trxref'] ?? $_GET['tx_ref'] ?? '');
        $status = strtolower(trim($_GET['status'] ?? ''));
        $transactionId = trim($_GET['transaction_id'] ?? '');

        // Fetch transaction from DB
        $txn = null;
        if (!empty($gatewayRef)) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE reference = ?");
            $stmt->execute([$gatewayRef]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$txn && !empty($_GET['reference'])) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE reference = ?");
            $stmt->execute([$_GET['reference']]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        $meta = $txn ? json_decode($txn['meta'] ?? '{}', true) : [];
        $callbackUrl = $meta['callback'] ?? '';
        $cancelUrl = $meta['cancel'] ?? '';
        $origRef = $meta['orig_ref'] ?? $gatewayRef;

        $verified = false;
        $amountPaid = $txn['amount'] ?? 0;
        $currencyPaid = $txn['currency'] ?? 'NGN';

        // 1. Verify Paystack
        $paystackKey = defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ($_ENV['PAYSTACK_SECRET_KEY'] ?? '');
        if (!empty($paystackKey) && (!empty($_GET['reference']) || !empty($_GET['trxref']) || !empty($gatewayRef))) {
            $queryRef = !empty($_GET['reference']) ? $_GET['reference'] : (!empty($_GET['trxref']) ? $_GET['trxref'] : $gatewayRef);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($queryRef),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $paystackKey],
            ]);
            $res = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (isset($res['status']) && $res['status'] === true && ($res['data']['status'] ?? '') === 'success') {
                $verified = true;
                $amountPaid = ($res['data']['amount'] ?? 0) / 100;
                $currencyPaid = $res['data']['currency'] ?? $currencyPaid;
            }
        }

        // 2. Verify Flutterwave
        $flwKey = defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '');
        if (!$verified && !empty($flwKey) && (!empty($transactionId) || $status === 'successful' || $status === 'completed')) {
            if (!empty($transactionId)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $flwKey],
                ]);
                $res = json_decode(curl_exec($curl), true);
                curl_close($curl);

                if (isset($res['status']) && $res['status'] === 'success' && ($res['data']['status'] ?? '') === 'successful') {
                    $verified = true;
                    $amountPaid = $res['data']['amount'] ?? $amountPaid;
                    $currencyPaid = $res['data']['currency'] ?? $currencyPaid;
                }
            }
        }

        if ($verified) {
            // Update transaction to successful
            if ($txn) {
                try {
                    $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE id = ?")->execute([$txn['id']]);

                    // Credit merchant's wallet
                    if (!empty($txn['user_id'])) {
                        $wStmt = $this->pdo->prepare("SELECT id FROM cp_wallets WHERE user_id = ? AND currency = ?");
                        $wStmt->execute([$txn['user_id'], $txn['currency']]);
                        if (!$wStmt->fetch()) {
                            $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                                      ->execute([$txn['tenant_id'] ?? 1, $txn['user_id'], $txn['currency']]);
                        }
                        $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?")
                                  ->execute([$amountPaid, $txn['user_id'], $txn['currency']]);
                    }
                } catch (\Throwable $e) {}
            }

            // Handle sales funnel payment completion if applicable
            if (strpos($origRef, 'funnel_') === 0) {
                $paymentId = (int)str_replace('funnel_', '', $origRef);
                try {
                    require_once dirname(__DIR__, 2) . '/CasjoeLinks/Services/PaymentProcessor.php';
                    $processor = new \App\Modules\CasjoeLinks\Services\PaymentProcessor();
                    $dealId = $_SESSION['funnel_deal_id_for_payment_' . ($payment['funnel_id'] ?? '')] ?? null;
                    $processor->handlePaymentSuccess($paymentId, $gatewayRef, $dealId);
                } catch (\Throwable $e) {}
            }

            // If callback URL was supplied, redirect customer back with success parameters
            if (!empty($callbackUrl)) {
                $sep = strpos($callbackUrl, '?') !== false ? '&' : '?';
                header('Location: ' . $callbackUrl . $sep . 'status=success&reference=' . urlencode($origRef) . '&trxref=' . urlencode($gatewayRef) . '&amount=' . urlencode($amountPaid));
                exit;
            }

            // Fallback: Display success screen
            $payerName = $meta['payer_name'] ?? 'Customer';
            require __DIR__ . '/../Views/public_pay_success.php';
            exit;
        } else {
            // Mark failed if transaction found
            if ($txn && $txn['status'] === 'pending') {
                try {
                    $this->pdo->prepare("UPDATE cp_transactions SET status = 'failed' WHERE id = ?")->execute([$txn['id']]);
                } catch (\Throwable $e) {}
            }

            if (!empty($cancelUrl)) {
                $sep = strpos($cancelUrl, '?') !== false ? '&' : '?';
                header('Location: ' . $cancelUrl . $sep . 'error=payment_failed&reference=' . urlencode($origRef));
                exit;
            }

            echo "<!DOCTYPE html><html><head><title>Payment Incomplete</title><style>body{font-family:sans-serif;background:#0b0f19;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;} .box{background:#1e293b;padding:40px;border-radius:12px;text-align:center;max-width:400px;} a{color:#ffa600;text-decoration:none;display:inline-block;margin-top:20px;}</style></head><body><div class='box'><h2>Payment Incomplete</h2><p>Your payment could not be confirmed or was cancelled.</p><a href='javascript:history.back()'>&larr; Try Again</a></div></body></html>";
            exit;
        }
    }
}

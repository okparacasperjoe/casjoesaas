<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;

class LinkController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // requireAuth
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_links WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->userId]);
        $links = $stmt->fetchAll();

        require __DIR__ . '/../Views/links/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $title = $_POST['title'];
        $currency = $_POST['currency'];
        $amount = $_POST['amount'] ?: 0.00; // 0 means open
        
        $customSlug = trim($_POST['slug'] ?? '');
        if (!empty($customSlug)) {
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $customSlug));
            // Check if slug exists to prevent duplicates
            $stmt = $this->pdo->prepare("SELECT id FROM cp_payment_links WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . uniqid();
            }
        } else {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . uniqid();
        }

        $is_recurring = isset($_POST['is_recurring']) && $_POST['is_recurring'] == 1 ? 1 : 0;
        $billing_interval = $_POST['billing_interval'] ?? null;

        $stmt = $this->pdo->prepare("INSERT INTO cp_payment_links (tenant_id, user_id, title, slug, amount, currency, is_recurring, billing_interval) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $this->userId, $title, $slug, $amount, $currency, $is_recurring, $billing_interval]);

        header("Location: /pay/links");
    }

    public function delete($params)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        $linkId = $params['id'];

        $stmt = $this->pdo->prepare("DELETE FROM cp_payment_links WHERE id = ? AND user_id = ?");
        $stmt->execute([$linkId, $this->userId]);

        header("Location: /pay/links");
    }

    public function pay($params)
    {
        $slug = $params['slug'];
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_links WHERE slug = ?");
        $stmt->execute([$slug]);
        $link = $stmt->fetch();

        if (!$link) {
            http_response_code(404);
            die("Payment Link Not Found");
        }

        // Increment Views
        $stmt = $this->pdo->prepare("UPDATE cp_payment_links SET views = views + 1 WHERE id = ?");
        $stmt->execute([$link['id']]);

        require __DIR__ . '/../Views/public_pay.php';
    }

    public function processPayment($params)
    {
        $slug = $params['slug'];
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_links WHERE slug = ?");
        $stmt->execute([$slug]);
        $link = $stmt->fetch();

        if (!$link) {
            http_response_code(404);
            die("Payment Link Not Found");
        }

        $amount = $_POST['amount'] ?? $link['amount'];
        $currency = strtoupper($link['currency']);
        $name = $_POST['name'] ?? 'Guest';
        $email = $_POST['email'] ?? 'guest@example.com';

        $amount = floatval($amount);
        if ($amount <= 0) {
            die("Invalid amount.");
        }

        $ref = 'PAYLINK-' . uniqid() . '-' . $link['id'];
        $description = 'Payment from ' . $name . ' via link: ' . $link['title'];
        $meta = json_encode(['link_id' => $link['id'], 'payer_name' => $name, 'payer_email' => $email]);
        
        $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'credit', ?, ?, 'pending', ?, ?)");
        $stmt->execute([$link['tenant_id'], $link['user_id'], $ref, $amount, $currency, $description, $meta]);

        $callbackUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/pay/link-callback';

        if ($currency === 'NGN') {
            // Paystack
            $payload = [
                'email' => $email,
                'amount' => $amount * 100, // kobo
                'currency' => $currency,
                'reference' => $ref,
                'callback_url' => $callbackUrl
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
                die("Error communicating with Paystack: " . ($res['message'] ?? 'Unknown error'));
            }
        } else {
            // Flutterwave
            $payload = [
                'tx_ref' => $ref,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => $callbackUrl,
                'customer' => ['email' => $email, 'name' => $name],
                'customizations' => ['title' => $link['title'], 'description' => $description]
            ];
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '')),
                    'Content-Type: application/json'
                ],
            ]);
            $response = curl_exec($curl);
            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === 'success') {
                header('Location: ' . $res['data']['link']);
                exit;
            } else {
                die("Error communicating with Flutterwave: " . ($res['message'] ?? 'Unknown error'));
            }
        }
    }

    public function callback()
    {
        $status = $_GET['status'] ?? '';
        $txRef = $_GET['tx_ref'] ?? '';
        $transactionId = $_GET['transaction_id'] ?? '';
        $reference = $_GET['reference'] ?? ''; // Paystack

        $verified = false;
        $amountPaid = 0;
        $currencyPaid = 'NGN';
        $finalRef = '';

        if (!empty($reference)) {
            // Paystack verify
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["Authorization: Bearer " . (defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ($_ENV['PAYSTACK_SECRET_KEY'] ?? ''))],
            ]);
            $res = json_decode(curl_exec($curl), true);
            if (isset($res['status']) && $res['status'] === true && $res['data']['status'] === 'success') {
                $verified = true;
                $amountPaid = $res['data']['amount'] / 100;
                $currencyPaid = $res['data']['currency'];
                $finalRef = $reference;
            }
        } elseif (($status === 'successful' || $status === 'completed') && !empty($transactionId)) {
            // Flutterwave verify
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["Authorization: Bearer " . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? ''))],
            ]);
            $res = json_decode(curl_exec($curl), true);
            if (isset($res['status']) && $res['status'] === 'success') {
                $verified = true;
                $amountPaid = $res['data']['amount'];
                $currencyPaid = $res['data']['currency'];
                $finalRef = $txRef;
            }
        }

        if ($verified && !empty($finalRef)) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE reference = ? AND status = 'pending'");
            $stmt->execute([$finalRef]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($txn) {
                // Mark successful
                $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE id = ?")->execute([$txn['id']]);

                // Fund wallet
                $stmt = $this->pdo->prepare("SELECT id FROM cp_wallets WHERE user_id = ? AND currency = ?");
                $stmt->execute([$txn['user_id'], $txn['currency']]);
                if (!$stmt->fetch()) {
                    $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                              ->execute([$txn['tenant_id'], $txn['user_id'], $txn['currency']]);
                }
                $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?")
                          ->execute([$amountPaid, $txn['user_id'], $txn['currency']]);

                $meta = json_decode($txn['meta'], true);
                $name = $meta['payer_name'] ?? 'Customer';

                echo "<!DOCTYPE html>
                <html>
                <head>
    <script src=\"/js/casjoe_theme.js\"></script>
    <link rel=\"icon\" type=\"image/png\" href=\"/favicon.png\">
    <link rel=\"apple-touch-icon\" href=\"/favicon.png\">
                    <title>Payment Successful</title>
                    <style>
                        body { background: #f5f7fa; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
                        .check { width: 80px; height: 80px; background: #10B981; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; }
                        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #000066; color: white; text-decoration: none; border-radius: 8px; }
                    </style>
                </head>
                <body>
                    <div class='card'>
                        <div class='check'>&#10003;</div>
                        <h2 style='margin:0 0 10px; color:#333;'>Payment Successful!</h2>
                        <p style='color:#666; margin:0;'>Thank you, " . htmlspecialchars($name) . ".</p>
                        <p style='color:#666; margin:10px 0 0;'>Your payment of <strong>" . htmlspecialchars($currencyPaid) . " " . number_format($amountPaid, 2) . "</strong> has been received.</p>
                        <a href='/' class='btn'>Return</a>
                    </div>
                </body>
                </html>";
                exit;
            } else {
                die("Transaction already processed or not found.");
            }
        } else {
            die("Payment verification failed. Please contact support if you were debited.");
        }
    }
}

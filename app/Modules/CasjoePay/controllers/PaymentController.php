<?php

namespace App\Modules\Payments\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;

class PaymentController
{
    // Demo keys - in production these should be in config/env
    private $publicKey = "FLWPUBK_TEST-SANDBOXDEMO-X"; // Replace with real key
    private $secretKey = "FLWSECK_TEST-SANDBOXDEMO-X";
    private $encryptionKey = "FLWSECK_TEST-SANDBOXDEMO-X";

    public function index()
    {
        if (!Auth::user()) {
            header("Location: /login");
            exit;
        }
        require __DIR__ . '/../Views/pay.php';
    }

    public function initiate()
    {
        $user = Auth::user();
        if (!$user) {
            header("Location: /login");
            exit;
        }

        $amount = $_POST['amount'];
        $email = "user@example.com"; // Fetch from DB based on user ID
        $reference = "TXN_" . uniqid();
        $tenantId = TenantContext::getTenantId();
        $currency = "NGN";

        // Save pending transaction
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO transactions (tenant_id, user_id, reference, amount, currency, status, provider) VALUES (?, ?, ?, ?, ?, 'pending', 'flutterwave')",
            [$tenantId, $user['id'], $reference, $amount, $currency]
        );

        // Call Flutterwave Standard
        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/pay/callback',
            'customer' => [
                'email' => $email,
                'name' => "Tenant User"
            ],
            'customizations' => [
                'title' => "Payment for Service"
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        $res = json_decode($response, true);

        if ($res['status'] == 'success') {
            header('Location: ' . $res['data']['link']);
        } else {
            echo "Error initiating payment";
        }
    }

    public function callback()
    {
        $status = $_GET['status'];
        $tx_ref = $_GET['tx_ref'];
        $transaction_id = $_GET['transaction_id'];

        if ($status == 'successful' || $status == 'completed') {
            // Verify
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->secretKey
                ),
            ));

            $response = curl_exec($curl);

            $res = json_decode($response, true);

            if ($res['status'] == 'success' && $res['data']['amount'] >= 100) { // Check amount
                $db = Database::getInstance();
                $db->query("UPDATE transactions SET status = 'successful' WHERE reference = ?", [$tx_ref]);
                echo "Payment Successful!";
            } else {
                echo "Payment Verification Failed";
            }
        } else {
            echo "Payment Cancelled";
        }
    }
}

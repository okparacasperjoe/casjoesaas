<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;

class WalletController
{
    private $secretKey = "FLWSECK_TEST-SANDBOXDEMO-X"; // Mock Key

    public function fund()
    {
        require __DIR__ . '/../views/fund_wallet.php';
    }

    public function initFund()
    {
        $user = Auth::user();
        $amount = $_POST['amount'];
        $ref = "CJP_" . uniqid();
        $tenantId = TenantContext::getTenantId();

        // Log Transaction
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO transactions (tenant_id, user_id, reference, amount, currency, status, type, provider) VALUES (?, ?, ?, ?, 'NGN', 'pending', 'deposit', 'flutterwave')",
            [$tenantId, $user['id'], $ref, $amount]
        );

        // Flutterwave Payload
        $payload = [
            'tx_ref' => $ref,
            'amount' => $amount,
            'currency' => 'NGN',
            'redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/casjoe-pay/wallet/callback',
            'customer' => ['email' => 'user@example.com', 'name' => 'User'],
            'customizations' => ['title' => 'Fund Wallet']
        ];

        // cURL request to FW... (Abbreviated for brevity, assuming standard call)
        // For simulation, redirect to callback directly if simulating
        header("Location: /casjoe-pay/wallet/callback?status=successful&tx_ref=$ref&transaction_id=12345");
    }

    public function fundCallback()
    {
        $status = $_GET['status'];
        $tx_ref = $_GET['tx_ref'];

        if ($status == 'successful') {
            $db = Database::getInstance();
            $tenantId = TenantContext::getTenantId();

            // Verify Transaction (Ensure not already processed)
            $stmt = $db->query("SELECT * FROM transactions WHERE reference = ? AND tenant_id = ?", [$tx_ref, $tenantId]);
            $txn = $stmt->fetch();

            if ($txn && $txn['status'] == 'pending') {
                // Update Txn
                $db->query("UPDATE transactions SET status = 'successful' WHERE id = ?", [$txn['id']]);

                // Credit Wallet
                $db->query("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ?", [$txn['amount'], $txn['user_id']]);

                echo "Wallet Funded Successfully! <a href='/casjoe-pay'>Go Back</a>";
            } else {
                echo "Transaction already processed or invalid.";
            }
        }
    }

    public function transfer()
    {
        require __DIR__ . '/../views/transfer.php';
    }

    public function processTransfer()
    {
        $user = Auth::user();
        $amount = $_POST['amount'];
        $recipientEmail = $_POST['email'];
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // 1. Check Balance
        $stmt = $db->query("SELECT balance FROM cp_wallets WHERE user_id = ?", [$user['id']]);
        $balance = $stmt->fetch()['balance'];

        if ($balance < $amount)
            die("Insufficient Funds");

        // 2. Find Recipient
        $stmt = $db->query("SELECT id FROM users WHERE email = ? AND tenant_id = ?", [$recipientEmail, $tenantId]);
        $recipient = $stmt->fetch();

        if (!$recipient)
            die("Recipient not found");

        // 3. Deduct Sender
        $db->query("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ?", [$amount, $user['id']]);

        // 4. Credit Recipient
        // Verify recipient wallet exists
        $stmt = $db->query("SELECT id FROM cp_wallets WHERE user_id = ?", [$recipient['id']]);
        if (!$stmt->fetch()) {
            $db->query("INSERT INTO cp_wallets (user_id, tenant_id, currency, balance) VALUES (?, ?, 'NGN', 0)", [$recipient['id'], $tenantId]);
        }
        $db->query("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ?", [$amount, $recipient['id']]);

        // 5. Log Transaction
        $ref = "TRF_" . uniqid();
        $db->query(
            "INSERT INTO transactions (tenant_id, user_id, reference, amount, currency, status, type, recipient_account) VALUES (?, ?, ?, ?, 'NGN', 'successful', 'transfer', ?)",
            [$tenantId, $user['id'], $ref, $amount, $recipientEmail]
        );

        header("Location: /casjoe-pay");
    }
}

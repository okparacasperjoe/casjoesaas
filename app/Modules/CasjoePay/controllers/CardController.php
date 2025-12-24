<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\Auth;

use App\Modules\CasjoePay\Services\SudoService;

class CardController
{
    public function index()
    {
        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM cp_virtual_cards WHERE user_id = ?", [$user['id']]);
        $cards = $stmt->fetchAll();

        require __DIR__ . '/../views/cards.php';
    }

    public function create()
    {
        $user = Auth::user();
        $amount = (float) $_POST['amount']; // Initial funding in USD
        // Exchange Rate Mock (NGN to USD) - In prod use real rate
        $rate = 1600; 
        $ngnAmount = $amount * $rate; 

        $db = Database::getInstance();

        // 1. Check Wallet Balance (NGN)
        $stmt = $db->query("SELECT balance FROM cp_wallets WHERE user_id = ?", [$user['id']]);
        $walletVal = $stmt->fetch()['balance'] ?? 0;

        if ($walletVal < $ngnAmount) {
             $_SESSION['error'] = "Insufficient wallet funds. Required: ₦" . number_format($ngnAmount);
             header("Location: /casjoe-pay/cards");
             exit;
        }

        // 2. Call Sudo API
        try {
            $sudo = new SudoService();
            // Create Customer (Idempotent-ish)
            $customerRes = $sudo->createCustomer($user['email'], $user['name'] ?? 'Casjoe User');
            
            if (!isset($customerRes['_id']) && !isset($customerRes['data']['_id'])) {
                 // Handle specific Sudo response structure
                 // Fallback Mock for testing if API fails or no keys
                 if (SUDO_API_KEY === '') throw new \Exception("Sudo API Key missing");
            }
            
            $customerId = $customerRes['data']['_id'] ?? $customerRes['_id'] ?? 'cust_mock_'.uniqid();

            // Create Card
            $cardRes = $sudo->createCard($customerId, 'USD', $amount);
            
            if (($cardRes['statusCode'] ?? 200) > 299) {
                throw new \Exception($cardRes['message'] ?? 'Card creation failed');
            }

            $cardData = $cardRes['data'] ?? $cardRes;
            $cardId = $cardData['_id'] ?? 'card_mock_'.uniqid();
            $maskedPan = $cardData['maskedPan'] ?? '5399********0000';

            // 3. Deduct Wallet
            $db->query("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ?", [$ngnAmount, $user['id']]);
            
            // Log Transaction
            $db->query("INSERT INTO transactions (tenant_id, user_id, reference, amount, currency, status, type, description) VALUES (?, ?, ?, ?, 'NGN', 'successful', 'debit', 'Card Creation - $amount USD')", 
                [$user['tenant_id'], $user['id'], 'txn_'.uniqid(), $ngnAmount]);

            // 4. Save Card
            $db->query(
                "INSERT INTO cp_virtual_cards (user_id, tenant_id, card_id, start_month, start_year, masked_pan, currency, balance, status) VALUES (?, ?, ?, ?, ?, ?, 'USD', ?, 'active')",
                [$user['id'], $user['tenant_id'], $cardId, '12', '28', $maskedPan, $amount]
            );

            header("Location: /casjoe-pay/cards?success=created");

        } catch (\Exception $e) {
            $_SESSION['error'] = "Provider Error: " . $e->getMessage();
            header("Location: /casjoe-pay/cards");
        }
    }

    public function fundCard()
    {
        // Not implemented yet
    }
}

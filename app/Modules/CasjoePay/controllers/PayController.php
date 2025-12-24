<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\View;
use PDO;

class PayController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct()
    {
    }

    public function index()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);

        if (!isset($_SESSION['user_id'])) { // Basic auth check
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        
        // Fetch Tenant ID
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        // 1. Get Wallet
        $stmt = $this->pdo->prepare("SELECT * FROM cp_wallets WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        // Create Wallet if not exists
        if (!$wallet) {
            $stmt = $this->pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, 'NGN', 0.00)");
            $stmt->execute([$this->tenantId, $this->userId]);
            $wallet = ['balance' => 0.00, 'currency' => 'NGN'];
        }

        // 2. Get Recent Transactions
        $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$this->userId]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/dashboard.php';
    }

    public function fund()
    {
        require __DIR__ . '/../views/fund.php';
    }

    public function processFund()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        // Re-fetch tenant ID as this is a new request, or better yet, assume tenant context is set or fetch simply
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $amount = $_POST['amount'];
        $ref = 'FLW-' . uniqid();
        
        // Create Pending Transaction
        $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description) VALUES (?, ?, ?, 'credit', ?, 'NGN', 'pending', 'Wallet Funding')");
        $stmt->execute([$this->tenantId, $this->userId, $ref, $amount]);
        
        header("Location: /pay/fund/verify?tx_ref=$ref&status=successful");
    }

    public function verifyFund($params)
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];

        $ref = $_GET['tx_ref'] ?? '';
        $status = $_GET['status'] ?? 'failed';

        if ($status == 'successful') {
            $stmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE reference = ? AND status = 'pending'");
            $stmt->execute([$ref]);
            
            if ($stmt->rowCount() > 0) {
                $stmt = $this->pdo->prepare("SELECT amount FROM cp_transactions WHERE reference = ?");
                $stmt->execute([$ref]);
                $amount = $stmt->fetchColumn();

                $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ?");
                $stmt->execute([$amount, $this->userId]);
            }
        }

        header('Location: /pay');
    }

    public function transfer()
    {
        require __DIR__ . '/../views/transfer.php';
    }

    public function processTransfer()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $email = $_POST['email'];
        $amount = $_POST['amount'];

        if ($amount <= 0) die("Invalid amount");

        $stmt = $this->pdo->prepare("SELECT balance FROM cp_wallets WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $balance = $stmt->fetchColumn();

        if ($balance < $amount) {
            die("Insufficient Funds");
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $recipientId = $stmt->fetchColumn();

        if (!$recipientId) {
            die("User not found");
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ?");
            $stmt->execute([$amount, $this->userId]);

            $ref = 'TRF-' . uniqid();
            $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, status, description) VALUES (?, ?, ?, 'debit', ?, 'successful', ?)");
            $stmt->execute([$this->tenantId, $this->userId, $ref, $amount, "Transfer to $email"]);

            $stmt = $this->pdo->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, 'NGN', 0.00)");
            $stmt->execute([$this->tenantId, $recipientId]);

            $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ?");
            $stmt->execute([$amount, $recipientId]);

            $ref2 = 'REC-' . uniqid();
            $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, status, description) VALUES (?, ?, ?, 'credit', ?, 'successful', ?)");
            $stmt->execute([$this->tenantId, $recipientId, $ref2, $amount, "Received from " . $_SESSION['user_id']]);

            $this->pdo->commit();
            header('Location: /pay');

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Transfer Failed: " . $e->getMessage());
        }
    }
}

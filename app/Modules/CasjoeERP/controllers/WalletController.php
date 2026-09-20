<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class WalletController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function checkAdmin()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    public function migrate()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        require __DIR__ . '/../../../../migrate_wallet_system.php';
        exit;
    }

    public function index()
    {
        $this->checkAdmin();

        // Ensure wallet exists
        $stmt = $this->pdo->prepare("SELECT * FROM erp_wallets WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$wallet) {
            $this->pdo->prepare("INSERT IGNORE INTO erp_wallets (tenant_id, balance) VALUES (?, 0.00)")->execute([$this->tenantId]);
            $wallet = ['balance' => 0.00];
        }

        // Get Transactions
        $stmtTx = $this->pdo->prepare("SELECT * FROM erp_wallet_transactions WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmtTx->execute([$this->tenantId]);
        $transactions = $stmtTx->fetchAll(\PDO::FETCH_ASSOC);

        // Get Withdrawals
        $stmtW = $this->pdo->prepare("SELECT * FROM erp_withdrawals WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 20");
        $stmtW->execute([$this->tenantId]);
        $withdrawals = $stmtW->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/finance/wallet.php';
    }

    public function withdraw()
    {
        $this->checkAdmin();
        $amount = floatval($_POST['amount'] ?? 0);
        $method = $_POST['withdrawal_method'] ?? 'bank';

        if ($amount <= 0) {
            header('Location: /erp/wallet?error=Invalid amount');
            exit;
        }

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_wallets WHERE tenant_id = ? FOR UPDATE");
            $stmt->execute([$this->tenantId]);
            $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$wallet || $wallet['balance'] < $amount) {
                $this->pdo->rollBack();
                header('Location: /erp/wallet?error=Insufficient balance');
                exit;
            }

            // Calculate 1.5% Fee
            $fee = $amount * 0.015;
            $netAmount = $amount - $fee;

            // Deduct from wallet
            $updWallet = $this->pdo->prepare("UPDATE erp_wallets SET balance = balance - ? WHERE tenant_id = ?");
            $updWallet->execute([$amount, $this->tenantId]);

            // Log Transaction
            $insTx = $this->pdo->prepare("INSERT INTO erp_wallet_transactions (tenant_id, type, amount, description) VALUES (?, 'debit', ?, ?)");
            $insTx->execute([$this->tenantId, $amount, "Withdrawal Request (Pending)"]);

            // Insert Withdrawal Record
            if ($method === 'bank') {
                $bankName = $_POST['bank_name'] ?? '';
                $accountName = $_POST['account_name'] ?? '';
                $accountNumber = $_POST['account_number'] ?? '';

                $insW = $this->pdo->prepare("INSERT INTO erp_withdrawals (tenant_id, amount, fee, net_amount, withdrawal_method, bank_name, account_name, account_number, status) VALUES (?, ?, ?, ?, 'bank', ?, ?, ?, 'pending')");
                $insW->execute([$this->tenantId, $amount, $fee, $netAmount, $bankName, $accountName, $accountNumber]);
            } else {
                $cryptoNetwork = $_POST['crypto_network'] ?? '';
                $cryptoAddress = $_POST['crypto_address'] ?? '';

                $insW = $this->pdo->prepare("INSERT INTO erp_withdrawals (tenant_id, amount, fee, net_amount, withdrawal_method, crypto_network, crypto_address, status) VALUES (?, ?, ?, ?, 'crypto', ?, ?, 'pending')");
                $insW->execute([$this->tenantId, $amount, $fee, $netAmount, $cryptoNetwork, $cryptoAddress]);
            }

            $this->pdo->commit();
            header('Location: /erp/wallet?success=Withdrawal requested successfully');
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            header('Location: /erp/wallet?error=An error occurred');
        }
    }
}

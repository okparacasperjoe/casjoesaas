<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;

class DashboardController
{
    public function index()
    {
        $user = Auth::user();
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Get Wallet Balance
        $stmt = $db->query("SELECT * FROM cp_wallets WHERE user_id = ? AND tenant_id = ?", [$user['id'], $tenantId]);
        $wallet = $stmt->fetch();

        if (!$wallet) {
            // Create Wallet if not exists
            $db->query("INSERT INTO cp_wallets (user_id, tenant_id, currency) VALUES (?, ?, 'NGN')", [$user['id'], $tenantId]);
            $wallet = ['balance' => 0.00, 'currency' => 'NGN'];
        }

        // Get Recent Transactions
        $stmt = $db->query("SELECT * FROM transactions WHERE user_id = ? AND tenant_id = ? ORDER BY created_at DESC LIMIT 5", [$user['id'], $tenantId]);
        $transactions = $stmt->fetchAll();

        // Get Cards Count
        $stmt = $db->query("SELECT COUNT(*) as count FROM cp_virtual_cards WHERE user_id = ?", [$user['id']]);
        $cardCount = $stmt->fetch()['count'];

        require __DIR__ . '/../views/dashboard.php';
    }
}

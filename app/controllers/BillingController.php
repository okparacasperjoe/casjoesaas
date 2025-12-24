<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class BillingController
{
    public function index()
    {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM subscriptions WHERE tenant_id = ?", [$tenantId]);
        $sub = $stmt->fetch();

        require __DIR__ . '/../views/billing/index.php';
    }

    public function subscribe()
    {
        $cardToken = $_POST['card_token']; // In reality, this comes from FW JS
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        // Mock Charge
        // Charge $50/month
        $amount = 50.00;

        // Update Subscription
        $nextBill = date('Y-m-d H:i:s', strtotime('+1 month'));

        $db->query(
            "UPDATE subscriptions SET status = 'active', card_token = ?, next_billing_at = ? WHERE tenant_id = ?",
            [$cardToken, $nextBill, $tenantId]
        );

        // Record Invoice
        $db->query(
            "INSERT INTO billing_invoices (tenant_id, reference, amount, status) VALUES (?, ?, ?, 'paid')",
            [$tenantId, 'INV_' . uniqid(), $amount]
        );

        header("Location: /dashboard");
    }
}

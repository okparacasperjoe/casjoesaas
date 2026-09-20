<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Auth;
use App\Core\Database;

class SubscriptionController
{
    // Customer: Show Billing Portal
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        // Fetch Current Subscription
        $stmt = $db->query("
            SELECT s.*, p.name as plan_name, p.price, p.billing_interval 
            FROM pay_subscriptions s
            JOIN pay_plans p ON s.plan_id = p.id
            WHERE s.user_id = ? AND s.status = 'active'
        ", [$user['id']]);
        $subscription = $stmt->fetch();

        // Fetch All Plans (for upgrading)
        $stmt = $db->query("SELECT * FROM pay_plans WHERE tenant_id = ? ORDER BY price ASC", [$user['tenant_id']]);
        $plans = $stmt->fetchAll();

        // Fetch Invoices
        if ($subscription) {
            $stmt = $db->query("SELECT * FROM pay_invoices WHERE subscription_id = ? ORDER BY issued_date DESC", [$subscription['id']]);
            $invoices = $stmt->fetchAll();
        } else {
            $invoices = [];
        }

        require __DIR__ . '/../Views/billing/index.php';
    }

    // Customer: Subscribe to a Plan
    public function subscribe($planId)
    {
        if (!Auth::check()) exit;
        $user = Auth::user();
        $db = Database::getInstance();

        // 1. Cancel existing active subscriptions
        $db->query("UPDATE pay_subscriptions SET status = 'cancelled' WHERE user_id = ? AND status = 'active'", [$user['id']]);

        // 2. Create new subscription
        // Mocking 'paid' immediately. In real world, redirect to Stripe/Flutterwave first.
        
        $planStmt = $db->query("SELECT * FROM pay_plans WHERE id = ?", [$planId]);
        $plan = $planStmt->fetch();

        if (!$plan) die("Plan not found");

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 ' . $plan['billing_interval']));

        $db->query("INSERT INTO pay_subscriptions (user_id, plan_id, status, current_period_start, current_period_end) VALUES (?, ?, 'active', ?, ?)", 
            [$user['id'], $planId, $startDate, $endDate]
        );
        $subId = $db->lastInsertId();

        // 3. Generate Invoice
        $db->query("INSERT INTO pay_invoices (subscription_id, amount, status, issued_date) VALUES (?, ?, 'paid', ?)", 
            [$subId, $plan['price'], $startDate]
        );

        header('Location: /billing');
    }

    // Customer: Cancel Subscription
    public function cancel($subId)
    {
        if (!Auth::check()) exit;
        $db = Database::getInstance();
        
        // Verify ownership
        $stmt = $db->query("SELECT user_id FROM pay_subscriptions WHERE id = ?", [$subId]);
        $sub = $stmt->fetch();
        
        if ($sub && $sub['user_id'] == Auth::user()['id']) {
            $db->query("UPDATE pay_subscriptions SET status = 'cancelled' WHERE id = ?", [$subId]);
        }
        
        header('Location: /billing');
    }
}


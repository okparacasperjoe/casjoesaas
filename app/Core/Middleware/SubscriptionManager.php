<?php

namespace App\Core\Middleware;

use App\Core\Database;
use App\Core\TenantContext;

class SubscriptionManager
{
    public static function checkSubscription()
    {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM subscriptions WHERE tenant_id = ?", [$tenantId]);
        $sub = $stmt->fetch();

        if (!$sub) {
            // No subscription found? Create a trial automatically for new tenants
            // In a real app this happens on registration. Here we auto-seed for demo.
            $trialEnd = date('Y-m-d H:i:s', strtotime('+30 days'));
            $db->query("INSERT INTO subscriptions (tenant_id, status, trial_ends_at) VALUES (?, 'trial', ?)", [$tenantId, $trialEnd]);
            return true;
        }

        if ($sub['status'] === 'active') {
            return true;
        }

        if ($sub['status'] === 'trial') {
            if (strtotime($sub['trial_ends_at']) > time()) {
                return true;
            } else {
                // Trial expired
                self::redirectBilling("Trial Expired. Please upgrade.");
            }
        }

        if ($sub['status'] === 'past_due' || $sub['status'] === 'cancelled') {
            self::redirectBilling("Subscription inactive.");
        }
    }

    private static function redirectBilling($msg)
    {
        // Allow access to billing pages so they can pay
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (strpos($path, '/billing') === 0 || strpos($path, '/logout') === 0) {
            return;
        }

        echo "<h1>Access Denied</h1>";
        echo "<p>$msg</p>";
        echo "<a href='/billing'>Go to Billing</a>";
        exit;
    }
}

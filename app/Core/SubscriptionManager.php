<?php

namespace App\Core;

use PDO;

class SubscriptionManager
{
    private static $pdo;

    private static function getPdo()
    {
        if (!self::$pdo) {
            self::$pdo = Database::getInstance()->getConnection();
        }
        return self::$pdo;
    }

    public static function getSubscription($tenantId)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function isActive($tenantId)
    {
        $sub = self::getSubscription($tenantId);
        
        // If no subscription, assume trial or inactive? Default schema says 'trial'.
        // Let's implement logic: 'active' or 'trial' (within date) = TRUE
        
        if (!$sub) return false; // Should exist via register logic or triggers

        if ($sub['status'] === 'active') return true;
        
        if ($sub['status'] === 'trial') {
            $trialEnds = strtotime($sub['trial_ends_at']);
            return time() < $trialEnds;
        }

        return false;
    }

    public static function requireActive($tenantId)
    {
        if (!self::isActive($tenantId)) {
            header('Location: /billing?error=subscription_required');
            exit;
        }
    }
}

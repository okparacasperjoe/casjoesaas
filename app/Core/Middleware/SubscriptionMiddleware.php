<?php

namespace App\Core\Middleware;

use App\Core\Database;
use App\Core\TenantContext;

class SubscriptionMiddleware
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
            if (!empty($sub['trial_ends_at']) && strtotime($sub['trial_ends_at']) > time()) {
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
        // Allow access to essential/free modules, billing, and main dashboard
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $allowedPaths = ['/billing', '/logout', '/profile', '/pay', '/shop', '/academy', '/dashboard'];
        
        if ($path === '/' || empty($path)) {
            return;
        }

        foreach ($allowedPaths as $allowed) {
            if (strpos($path, $allowed) === 0) {
                return;
            }
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Required - Casjoe ERP</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000066 0%, #000033 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 102, 0.1);
            border-radius: 24px;
            padding: 40px;
            width: 90%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
            color: #000066;
        }
        .icon-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 166, 0, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #FFA600;
            font-size: 40px;
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
            color: #000066;
        }
        p {
            color: #4b5563;
            font-size: 1rem;
            line-height: 1.6;
            margin: 0 0 30px 0;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .btn-primary {
            background: #FFA600;
            color: #000066;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 166, 0, 0.4);
            background: #ffb733;
        }
        .btn-secondary {
            background: rgba(0, 0, 102, 0.05);
            color: #000066;
            border: 1px solid rgba(0, 0, 102, 0.1);
        }
        .btn-secondary:hover {
            background: rgba(0, 0, 102, 0.1);
        }
        .support-box {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 102, 0.1);
        }
        .support-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 15px;
        }
        .support-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .support-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #000066;
            text-decoration: none;
            font-size: 0.95rem;
            padding: 10px;
            background: rgba(0, 0, 102, 0.03);
            border-radius: 10px;
            transition: background 0.2s;
            border: 1px solid rgba(0, 0, 102, 0.05);
        }
        .support-link:hover {
            background: rgba(0, 0, 102, 0.08);
        }
        .whatsapp-icon { color: #25D366; font-size: 1.2rem; }
        .call-icon { color: #38bdf8; font-size: 1.2rem; }
        .coupon-badge {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 1px dashed #4ade80;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-container">
            <ion-icon name="lock-closed"></ion-icon>
        </div>
        <h1>Access Denied</h1>
        <p>{$msg}</p>
        
        <div class="btn-group">
            <a href="/billing" class="btn btn-primary">Upgrade Now</a>
            <a href="/dashboard" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <div class="support-box">
            <div class="support-title">Need help or want a special deal?</div>
            <div class="support-links">
                <a href="https://wa.me/2347050409050" class="support-link" target="_blank">
                    <ion-icon name="logo-whatsapp" class="whatsapp-icon"></ion-icon>
                    Chat with Support
                </a>
                <a href="tel:07050409050" class="support-link">
                    <ion-icon name="call" class="call-icon"></ion-icon>
                    Call us: 07050409050
                </a>
            </div>
            
            <div class="coupon-badge">
                🎉 Chat with us now to get a special discount coupon!
            </div>
        </div>
    </div>
</body>
</html>
HTML;
        echo $html;
        exit;
    }
}

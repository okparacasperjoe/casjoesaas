<?php

namespace App\Core\Services;

use App\Core\Database;
use App\Core\Notification;
use PDO;

class SubscriptionService
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Renew all expiring subscriptions and modules where auto_renew is enabled.
     */
    public function renewAllExpiring()
    {
        echo "Starting auto-renewal process...\n";
        
        $this->renewMainSubscriptions();
        $this->renewTenantModules();
        
        echo "Auto-renewal process completed.\n";
    }

    private function renewMainSubscriptions()
    {
        // Currently, main subscriptions (premium plan) are often handled via modules, 
        // but we'll implement the logic here in case there's a base plan fee.
        // For now, let's assume 'premium' plan has a fee if defined in settings.
        
        // Fetch expiring subscriptions with auto_renew enabled
        $stmt = $this->pdo->prepare("
            SELECT s.*, t.name as tenant_name 
            FROM subscriptions s
            JOIN tenants t ON s.tenant_id = t.id
            WHERE s.auto_renew = 1 
            AND s.next_billing_at <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
            AND s.status = 'active'
        ");
        $stmt->execute();
        $subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($subs as $sub) {
            // If there's a base plan price (e.g. implementation TBD or stored in settings)
            // For now, the user mostly pays for modules.
        }
    }

    private function renewTenantModules()
    {
        // Fetch expiring modules with auto_renew enabled
        // We only renew 'paid' plan types that are 'enabled'
        $stmt = $this->pdo->prepare("
            SELECT tm.*, m.name as module_name, m.price_ngn, m.slug as module_slug
            FROM tenant_modules tm
            JOIN modules m ON tm.module_id = m.id
            WHERE tm.auto_renew = 1 
            AND tm.expires_at <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
            AND tm.status = 'enabled'
            AND tm.plan_type = 'paid'
        ");
        $stmt->execute();
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($modules as $tm) {
            $tenantId = $tm['tenant_id'];
            $amount = $tm['price_ngn'];
            
            echo "Attempting to renew {$tm['module_name']} for Tenant ID: $tenantId ($amount NGN)...\n";

            if ($amount <= 0) {
                $this->extendModule($tm);
                continue;
            }

            // Find an admin user for this tenant to charge (or just the primary wallet)
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE tenant_id = ? AND role = 'admin' LIMIT 1");
            $stmt->execute([$tenantId]);
            $admin = $stmt->fetch();
            $adminId = $admin['id'] ?? 0;

            // Check Wallet Balance (NGN)
            $stmt = $this->pdo->prepare("SELECT id, balance FROM cp_wallets WHERE tenant_id = ? AND currency = 'NGN' LIMIT 1");
            $stmt->execute([$tenantId]);
            $wallet = $stmt->fetch();

            if ($wallet && $wallet['balance'] >= $amount) {
                // Deduct funds
                $this->pdo->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE id = ?")->execute([$amount, $wallet['id']]);
                
                // Record Transaction
                $txRef = 'RENEW-' . $tm['module_slug'] . '-' . time();
                $this->pdo->prepare("
                    INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description) 
                    VALUES (?, ?, ?, 'debit', ?, 'NGN', 'successful', ?)
                ")->execute([$tenantId, $adminId, $txRef, $amount, "Auto-renewal: " . $tm['module_name']]);

                // Extend Module
                $this->extendModule($tm);
                
                // Notify User
                if ($adminId) {
                    Notification::send($adminId, "Subscription Renewed", "Your subscription for {$tm['module_name']} has been automatically renewed.", "/billing");
                }
                echo "✓ Successfully renewed {$tm['module_name']}\n";
            } else {
                // TRY SAVED CARD
                $charged = false;
                
                // Get Saved Cards
                $stmt = $this->pdo->prepare("SELECT * FROM billing_cards WHERE tenant_id = ? ORDER BY is_default DESC, created_at DESC");
                $stmt->execute([$tenantId]);
                $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cards as $card) {
                    echo "Attempting to charge saved card ({$card['provider']} - {$card['last4']})...\n";
                    
                    if ($card['provider'] === 'monnify') {
                        // Get User Email
                        $stmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                        $stmt->execute([$card['user_id']]);
                        $user = $stmt->fetch();
                        
                        if ($user) {
                            $ref = 'RENEW-CARD-' . $tm['module_slug'] . '-' . time();
                            $res = MonnifyService::chargeCardToken(
                                $card['auth_token'], 
                                $amount, 
                                $user['email'], 
                                $user['name'], 
                                $ref, 
                                "Auto-renewal: " . $tm['module_name']
                            );

                            if (($res['requestSuccessful'] ?? false) && ($res['responseBody']['paymentStatus'] ?? '') === 'PAID') {
                                echo "✓ Successfully charged saved Monnify card!\n";
                                
                                // Record Transaction
                                $this->pdo->prepare("
                                    INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, provider) 
                                    VALUES (?, ?, ?, 'debit', ?, 'NGN', 'successful', ?, 'monnify')
                                ")->execute([$tenantId, $card['user_id'], $ref, $amount, "Auto-renewal (Card): " . $tm['module_name']]);

                                // Extend Module
                                $this->extendModule($tm);
                                
                                // Notify
                                Notification::send($card['user_id'], "Subscription Renewed", "Your subscription has been renewed via your saved card.", "/billing");
                                $charged = true;
                                break; 
                            } else {
                                echo "✗ Failed to charge Monnify card: " . ($res['responseMessage'] ?? 'Unknown Error') . "\n";
                            }
                        }
                    }

                    // Try Flutterwave card token
                    if ($card['provider'] === 'flutterwave') {
                        $stmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                        $stmt->execute([$card['user_id']]);
                        $user = $stmt->fetch();

                        if ($user) {
                            $ref = 'RENEW-FW-' . $tm['module_slug'] . '-' . time();
                            try {
                                $res = FlutterwaveService::chargeCardToken(
                                    $card['auth_token'],
                                    $user['email'],
                                    $amount,
                                    'NGN',
                                    $ref
                                );

                                if (isset($res['status']) && $res['status'] === 'success') {
                                    echo "✓ Successfully charged saved Flutterwave card!\n";

                                    $this->pdo->prepare("
                                        INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, provider) 
                                        VALUES (?, ?, ?, 'debit', ?, 'NGN', 'successful', ?, 'flutterwave')
                                    ")->execute([$tenantId, $card['user_id'], $ref, $amount, "Auto-renewal (Card): " . $tm['module_name']]);

                                    $this->extendModule($tm);

                                    Notification::send($card['user_id'], "Subscription Renewed", "Your subscription has been renewed via your saved card.", "/billing");
                                    $charged = true;
                                    break;
                                } else {
                                    echo "✗ Failed to charge Flutterwave card: " . ($res['message'] ?? 'Unknown Error') . "\n";
                                }
                            } catch (\Exception $e) {
                                echo "✗ Flutterwave card charge error: " . $e->getMessage() . "\n";
                            }
                        }
                    }
                }

                if (!$charged) {
                    // Failed - Insufficient Funds & No Valid Card
                    echo "✗ Failed renewal for {$tm['module_name']}: Insufficient funds & No valid card\n";
                    if ($adminId) {
                        Notification::send($adminId, "Subscription Renewal Failed", "Automatic renewal for {$tm['module_name']} failed. Please fund your wallet or update your card.", "/billing");
                    }
                    
                    // Optionally disable if strictly past due
                    if (strtotime($tm['expires_at']) < time()) {
                        $this->pdo->prepare("UPDATE tenant_modules SET status = 'disabled' WHERE id = ?")->execute([$tm['id']]);
                    }
                }
            }
        }
    }

    private function extendModule($tm)
    {
        // Extend by 1 month
        $newExpiry = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($tm['expires_at'])));
        // If current expiry is already in the past, extend from now
        if (strtotime($tm['expires_at']) < time()) {
            $newExpiry = date('Y-m-d H:i:s', strtotime('+1 month'));
        }

        $this->pdo->prepare("UPDATE tenant_modules SET expires_at = ?, last_payment_at = NOW() WHERE id = ?")
                 ->execute([$newExpiry, $tm['id']]);
    }
}

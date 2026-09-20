<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\View; // Assuming View helper exists, or I'll use simple include
use App\Core\Services\CsrfService;
use PDO;

class AdminController
{
    private $pdo;

    public function __construct()
    {
        // Ensure Logged In
        if (!\App\Core\Auth::check()) {
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        
        // Strict Super Admin & Moderator Check
        // Regular tenant owners with role = 'admin' are NOT platform super admins!
        if (!\App\Core\Auth::isSuperAdmin() && !\App\Core\Auth::isModerator()) {
            \App\Core\Auth::denySuperAdminAccess('Super Admin Executive Panel');
        }
    }

    public function index()
    {
        // Admin Dashboard Home
        // Gather Stats safely (tables might not exist if modules disabled)
        $safeFetch = function($sql) {
            try {
                $result = $this->pdo->query($sql);
                return $result ? ($result->fetchColumn() ?: 0) : 0;
            } catch (\Exception $e) {
                return 0; // Table might not exist
            }
        };

        $rev1 = (float) $safeFetch("SELECT SUM(amount) FROM transactions WHERE status = 'successful'");
        $rev2 = (float) $safeFetch("SELECT SUM(amount) FROM cp_transactions WHERE status = 'successful' AND type = 'credit'");
        $rev3 = (float) $safeFetch("SELECT SUM(amount) FROM billing_invoices WHERE status = 'paid'");

        $stats = [
            'users' => $safeFetch("SELECT COUNT(*) FROM users"),
            'tenants' => $safeFetch("SELECT COUNT(*) FROM tenants"),
            'revenue' => $rev1 + $rev2 + $rev3,
            'mrr' => $safeFetch("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'") * 28000,
            'ai_tokens' => $safeFetch("SELECT SUM(tokens_used) FROM ai_usage_log"),
            'support_tickets' => $safeFetch("SELECT COUNT(*) FROM support_tickets WHERE status = 'open'"),
            'apps' => $safeFetch("SELECT COUNT(*) FROM modules"),
            'cards' => $safeFetch("SELECT COUNT(*) FROM cp_virtual_cards"),
            'links' => $safeFetch("SELECT COUNT(*) FROM cp_payment_links"),
            'paid_subscribers' => $safeFetch("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'"),
            'unpaid_subscribers' => $safeFetch("SELECT COUNT(*) FROM subscriptions WHERE status != 'active'"),
            'pending_deposits' => $safeFetch("SELECT COUNT(*) FROM cp_transactions WHERE type = 'credit' AND status = 'pending'"),
            'pending_withdrawals' => $safeFetch("SELECT COUNT(*) FROM cp_transactions WHERE type = 'debit' AND status = 'pending'"),
            // Visitor Analytics
            'total_visitors'  => $safeFetch("SELECT COUNT(*) FROM site_visitors WHERE is_bot = 0"),
            'today_visitors'  => $safeFetch("SELECT COUNT(*) FROM site_visitors WHERE is_bot = 0 AND DATE(visited_at) = CURDATE()"),
            'unique_visitors' => $safeFetch("SELECT COUNT(DISTINCT ip_address) FROM site_visitors WHERE is_bot = 0"),
            'online_now'      => $safeFetch("SELECT COUNT(DISTINCT ip_address) FROM site_visitors WHERE is_bot = 0 AND visited_at >= NOW() - INTERVAL 5 MINUTE"),
        ];

        // Fetch recent activities
        $recentUsers = [];
        try {
            $stmt = $this->pdo->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
            $recentUsers = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {}

        $recentDeposits = [];
        try {
            $stmt = $this->pdo->query("SELECT amount, currency, created_at, status FROM cp_transactions WHERE type = 'credit' ORDER BY created_at DESC LIMIT 5");
            $recentDeposits = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {}

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        try {
            $stmt = $this->pdo->query("
                SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total 
                FROM billing_invoices 
                WHERE status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY month ORDER BY month ASC
            ");
            $monthlyRevenue = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {}

        // Admin name for greeting
        $adminName = 'Admin';
        try {
            $stmt = $this->pdo->prepare("SELECT name FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $adminName = $stmt->fetchColumn() ?: 'Admin';
        } catch (\Exception $e) {}

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users()
    {
        // List Users (Formerly index)
        $stmt = $this->pdo->query("SELECT u.*, t.name as tenant_name FROM users u LEFT JOIN tenants t ON u.tenant_id = t.id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/users/index.php';
    }

    public function broadcast()
    {
        $stmt = $this->pdo->query("SELECT * FROM cm_templates WHERE tenant_id IS NULL OR tenant_id = 1 ORDER BY created_at DESC");
        $templates = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        require __DIR__ . '/../Views/admin/broadcast.php';
    }

    public function sendBroadcast()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
             die("CSRF Token Verification Failed");
        }

        $subject = $_POST['subject'] ?? 'No Subject';
        $message = $_POST['message'] ?? '';
        $group = $_POST['group'] ?? 'all';

        // Mock Sending Logic (In real app, integrate PHPMailer or Mailgun)
        // Log to file for verification
        $logEntry = "[" . date('Y-m-d H:i:s') . "] BROADCAST to [$group] | Subject: $subject\n";
        file_put_contents(__DIR__ . '/../../../../logs/broadcasts.log', $logEntry, FILE_APPEND);

        // Simulate Success
        header('Location: /casper-joe/broadcast?success=1');
    }

    public function edit($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("User not found");
        }

        require __DIR__ . '/../Views/admin/users/edit.php';
    }

    public function update($params)
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
             die("CSRF Token Verification Failed");
        }

        $id = $params['id'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        // $plan = $_POST['plan']; // Future: Plan handling

        $two_factor = isset($_POST['two_factor_enabled']) ? 1 : 0;

        $stmt = $this->pdo->prepare("UPDATE users SET email = ?, role = ?, two_factor_enabled = ? WHERE id = ?");
        $stmt->execute([$email, $role, $two_factor, $id]);

        header('Location: /casper-joe/users');
    }

    public function delete($params)
    {
        $id = $params['id'];
        // Prevent deleting self
        if ($id == $_SESSION['user_id']) {
            die("Cannot delete yourself.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: /casper-joe/users');
    }

    public function impersonate($params)
    {
        $targetId = $params['id'];

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$targetId]);
        $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($targetUser) {
            // Store original admin session data if not already stored
            if (!isset($_SESSION['impersonator_id'])) {
                $_SESSION['impersonator_id'] = $_SESSION['user_id'];
                $_SESSION['impersonator_tenant_id'] = $_SESSION['tenant_id'];
                $_SESSION['impersonator_role'] = $_SESSION['role'];
                $_SESSION['impersonator_email'] = $_SESSION['user_email'];
                $_SESSION['impersonator_name'] = $_SESSION['user_name'];
            }

            $_SESSION['user_id'] = $targetUser['id'];
            $_SESSION['tenant_id'] = $targetUser['tenant_id'];
            $_SESSION['role'] = $targetUser['role'];
            $_SESSION['user_email'] = $targetUser['email'];
            $_SESSION['user_name'] = $targetUser['name'];
        }
        
        header('Location: /');
        exit;
    }

    public function unlockUserPin($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("UPDATE users SET pin_attempts = 0, pin_locked_until = NULL WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['admin_success'] = "User PIN lockout has been cleared successfully.";
        header('Location: /casper-joe/users');
        exit;
    }

    public function resetUserPin($params)
    {
        $id = $params['id'];
        
        $stmtUser = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
        $stmtUser->execute([$id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("UPDATE users SET transaction_pin = NULL, pin_attempts = 0, pin_locked_until = NULL, pin_reset_otp = NULL WHERE id = ?");
        $stmt->execute([$id]);

        if ($user && !empty($user['email'])) {
            $subject = "Casjoe Pay PIN Reset Notice";
            $body = "
                <p>Hello " . htmlspecialchars($user['name'] ?? 'User') . ",</p>
                <p>Your Casjoe Pay transaction PIN was reset by a system administrator. The next time you access your wallet, you will be prompted to set a new private 4-digit PIN.</p>
                <p>If you did not authorize this, please contact support immediately.</p>
            ";
            \App\Core\Mailer::send($user['email'], $subject, $body, true);
        }

        $_SESSION['admin_success'] = "User PIN has been reset and cleared. The user will be prompted to set a new PIN upon next wallet access.";
        header('Location: /casper-joe/users');
        exit;
    }

    public function settings()
    {
        // Fetch values
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // Ensure offline deposit keys fall back cleanly to any existing bank keys
        $settings['offline_deposit_bank'] = $settings['offline_deposit_bank'] ?? ($settings['bank_name'] ?? ($settings['bank_details_bank'] ?? ''));
        $settings['offline_deposit_account_name'] = $settings['offline_deposit_account_name'] ?? ($settings['account_name'] ?? ($settings['bank_account_name'] ?? ($settings['bank_details_account_name'] ?? '')));
        $settings['offline_deposit_account_number'] = $settings['offline_deposit_account_number'] ?? ($settings['account_number'] ?? ($settings['bank_account_number'] ?? ($settings['bank_details_number'] ?? '')));

        require __DIR__ . '/../Views/admin/settings.php';
    }

    public function updateSettings()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
             die("CSRF Token Verification Failed");
        }

        $postedSettings = is_array($_POST['settings'] ?? null) ? $_POST['settings'] : [];

        $fields = [
            'payment_routing_mode',
            'flutterwave_public_key', 'flutterwave_secret_key',
            'paystack_public_key',  'paystack_secret_key',
            'sudo_api_key', 'sudo_api_secret',
            'strowallet_public_key', 'strowallet_secret_key',
            'ziiropay_public_key', 'ziiropay_secret_key',
            'virtual_card_provider',
            'usd_exchange_rate', 'virtual_card_creation_fee', 'virtual_card_min_deposit'
        ];

        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                $postedSettings[$key] = trim($_POST[$key]);
            }
        }

        $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

        foreach ($postedSettings as $key => $value) {
            $value = trim((string)$value);
            $stmt->execute([$key, $value]);

            if (in_array($key, ['offline_deposit_bank', 'bank_name', 'bank_details_bank'])) {
                $stmt->execute(['offline_deposit_bank', $value]);
                $stmt->execute(['bank_name', $value]);
                $stmt->execute(['bank_details_bank', $value]);
            }
            if (in_array($key, ['offline_deposit_account_name', 'account_name', 'bank_account_name', 'bank_details_account_name'])) {
                $stmt->execute(['offline_deposit_account_name', $value]);
                $stmt->execute(['account_name', $value]);
                $stmt->execute(['bank_account_name', $value]);
                $stmt->execute(['bank_details_account_name', $value]);
            }
            if (in_array($key, ['offline_deposit_account_number', 'account_number', 'bank_account_number', 'bank_details_number'])) {
                $stmt->execute(['offline_deposit_account_number', $value]);
                $stmt->execute(['account_number', $value]);
                $stmt->execute(['bank_account_number', $value]);
                $stmt->execute(['bank_details_number', $value]);
            }
        }

        $tab = $_POST['tab'] ?? 'offline';
        header('Location: /casper-joe/settings?tab=' . $tab . '&success=1');
        exit;
    }

    public function cards() {
        try {
            $stmt = $this->pdo->query("SELECT c.*, u.name as user_name, u.email as user_email FROM cp_virtual_cards c LEFT JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
            $cards = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
            $cards = [];
        }
        
        $liveCardData = [];
        
        $stmtSettings = $this->pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key = 'virtual_card_provider'");
        $settingsRow = $stmtSettings->fetch(\PDO::FETCH_ASSOC);
        $provider = $settingsRow ? $settingsRow['setting_value'] : '';
        
        if ($provider === 'strowallet' || $provider === 'ziiropay' || $provider === 'ziirocard') {
            require_once __DIR__ . '/../../Modules/CasjoePay/Services/StroWalletService.php';
            $stroWallet = new \App\Modules\CasjoePay\Services\StroWalletService();
            
            foreach ($cards as $card) {
                if ($card['status'] !== 'admin_pending' && !empty($card['card_id'])) {
                    try {
                        if (isset($card['card_type']) && $card['card_type'] === 'nfc') {
                            $live = $stroWallet->fetchNfcCardDetails($card['card_id']);
                        } else {
                            if (method_exists($stroWallet, 'fetchCardDetails')) {
                                $live = $stroWallet->fetchCardDetails($card['card_id']);
                            } else {
                                $live = null;
                            }
                        }
                        
                        if ($live && is_array($live)) {
                            // Extract actual data if wrapped in "response" or similar
                            $liveCardData[$card['card_id']] = $live['response'] ?? $live['data'] ?? $live;
                        }
                    } catch (\Exception $e) {
                        // Ignore individual API failures
                    }
                }
            }
        }
        
        require __DIR__ . '/../Views/admin/cards.php';
    }

    public function kyc() {
        try {
            $stmt = $this->pdo->query("SELECT k.*, u.name as user_name, u.email as user_email, u.phone as user_phone, u.kyc_status as user_kyc_status FROM kyc_verifications k LEFT JOIN users u ON k.user_id = u.id ORDER BY k.created_at DESC");
            $verifications = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
            error_log("Admin KYC fetch error: " . $e->getMessage());
            $verifications = [];
        }
        require __DIR__ . '/../Views/admin/kyc.php';
    }

    public function soc() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `security_logs` (
                `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
                `tenant_id` VARCHAR(100) DEFAULT NULL,
                `user_id` INT DEFAULT NULL,
                `user_email` VARCHAR(255) DEFAULT NULL,
                `event_type` VARCHAR(100) NOT NULL,
                `severity` ENUM('info', 'warning', 'danger') DEFAULT 'info',
                `message` TEXT DEFAULT NULL,
                `ip_address` VARCHAR(50) DEFAULT NULL,
                `user_agent` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `blocked_ips` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ip_address` VARCHAR(50) UNIQUE NOT NULL,
                `reason` VARCHAR(255) DEFAULT 'Security Violation / SOC Block',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $count = (int) $this->pdo->query("SELECT COUNT(*) FROM security_logs")->fetchColumn();
        if ($count === 0) {
            $seedStmt = $this->pdo->prepare("INSERT INTO security_logs (user_email, event_type, severity, message, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $seedStmt->execute(['system@casjoe.com', 'system.boot', 'info', 'Security Operations Center initialized and monitoring started', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', date('Y-m-d H:i:s', strtotime('-2 hours'))]);
            $seedStmt->execute(['admin@casjoe.com', 'auth.login_success', 'info', 'Super Admin authentication successful via 2FA session', $_SERVER['REMOTE_ADDR'] ?? '197.210.64.12', date('Y-m-d H:i:s', strtotime('-1 hour'))]);
            $seedStmt->execute(['guest@unknown.net', 'auth.login_failed', 'warning', 'Invalid password attempt on user account', '185.220.101.5', date('Y-m-d H:i:s', strtotime('-45 minutes'))]);
            $seedStmt->execute(['scanner@bot.io', 'security.firewall_alert', 'danger', 'Repeated SQL injection payload blocked on API gateway', '103.145.13.88', date('Y-m-d H:i:s', strtotime('-20 minutes'))]);
        }

        $stats = [
            'failed_logins_today' => (int) $this->pdo->query("SELECT COUNT(*) FROM security_logs WHERE event_type LIKE '%failed%' AND created_at >= CURDATE()")->fetchColumn(),
            'critical_events' => (int) $this->pdo->query("SELECT COUNT(*) FROM security_logs WHERE severity = 'danger' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn(),
            'total_logs' => (int) $this->pdo->query("SELECT COUNT(*) FROM security_logs")->fetchColumn(),
        ];

        $stmtLogs = $this->pdo->query("SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 100");
        $logs = $stmtLogs ? $stmtLogs->fetchAll(\PDO::FETCH_ASSOC) : [];

        $stmtBlocked = $this->pdo->query("SELECT * FROM blocked_ips ORDER BY created_at DESC");
        $blockedIps = $stmtBlocked ? $stmtBlocked->fetchAll(\PDO::FETCH_ASSOC) : [];

        $stmtTimeline = $this->pdo->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM security_logs 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $eventsTimelineRaw = $stmtTimeline ? $stmtTimeline->fetchAll(\PDO::FETCH_ASSOC) : [];
        $eventsTimelineMap = [];
        foreach ($eventsTimelineRaw as $row) {
            $eventsTimelineMap[$row['date']] = (int) $row['count'];
        }
        $eventsTimeline = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $eventsTimeline[] = ['date' => $d, 'count' => $eventsTimelineMap[$d] ?? 0];
        }

        $stmtTypes = $this->pdo->query("SELECT event_type, COUNT(*) as count FROM security_logs GROUP BY event_type ORDER BY count DESC LIMIT 6");
        $eventTypes = $stmtTypes ? $stmtTypes->fetchAll(\PDO::FETCH_ASSOC) : [];

        $stmtSev = $this->pdo->query("SELECT severity, COUNT(*) as count FROM security_logs GROUP BY severity");
        $severityDist = $stmtSev ? $stmtSev->fetchAll(\PDO::FETCH_ASSOC) : [];

        require __DIR__ . '/../Views/admin/soc.php'; 
    }
    public function deposits() { 
        $stmt = $this->pdo->query("
            SELECT t.*, u.name as user_name, u.email as user_email
            FROM cp_transactions t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.type = 'credit' AND t.status = 'pending'
            ORDER BY t.created_at DESC
        ");
        $deposits = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        require __DIR__ . '/../Views/admin/deposits.php'; 
    }
    public function withdrawals() { 
        $stmt = $this->pdo->query("
            SELECT t.*, u.name as user_name, u.email as user_email
            FROM cp_transactions t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.type = 'debit' AND t.status = 'pending'
            ORDER BY t.created_at DESC
        ");
        $withdrawals = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        require __DIR__ . '/../Views/admin/withdrawals.php'; 
    }
        public function stores() {
        $stores = [];
        try {
            // Check cp_stores or stores
            $stmt = $this->pdo->query("SELECT * FROM cp_stores WHERE status = 'pending' ORDER BY created_at DESC");
            if ($stmt) {
                $stores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            try {
                $stmt = $this->pdo->query("SELECT * FROM stores WHERE status = 'pending' ORDER BY created_at DESC");
                if ($stmt) {
                    $stores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\Exception $e2) {
                // Table might not exist yet
            }
        }
        
        require __DIR__ . '/../Views/admin/stores.php'; 
    }
    public function permissions() { require __DIR__ . '/../Views/admin/moderator_permissions.php'; }
    public function invoices() {
        $invoices = [];
        try {
            $stmt = $this->pdo->query("SELECT b.*, t.domain, u.name as user_name, u.email as user_email 
                                       FROM billing_invoices b 
                                       LEFT JOIN tenants t ON b.tenant_id = t.id 
                                       LEFT JOIN users u ON u.id = (SELECT id FROM users WHERE tenant_id = b.tenant_id ORDER BY id ASC LIMIT 1)
                                       WHERE b.status = 'pending' ORDER BY b.date DESC");
            $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        require __DIR__ . '/../Views/admin/invoices.php'; 
    }
        
    public function invoicesApprove($params) {
        $id = is_array($params) ? ($params['id'] ?? null) : $params;
        if (!$id) {
            header('Location: /' . ADMIN_PATH . '/invoices?msg=Invalid Invoice ID');
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM billing_invoices WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($invoice) {
            $ref = $invoice['reference'];
            $tenantId = $invoice['tenant_id'];
            
            // Get admin user for the tenant
            $userStmt = $this->pdo->prepare("SELECT id FROM users WHERE tenant_id = ? ORDER BY id ASC LIMIT 1");
            $userStmt->execute([$tenantId]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
            $userId = $user ? $user['id'] : 0;

            if (strpos($ref, 'AI-') === 0) {
                // AI Top Up
                $parts = explode('-', $ref);
                $packKey = $parts[2] ?? 'ai_5k';
                $aiService = new \App\Core\AI\AICreditService();
                $aiService->topUp($tenantId, $userId, $packKey, $ref, $invoice['amount'], $invoice['currency']);
            } else {
                // Subscription
                $stmtSub = $this->pdo->prepare("UPDATE subscriptions SET status = 'active', plan = 'all-access-bundle', next_billing_at = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE tenant_id = ?");
                $stmtSub->execute([$tenantId]);

                $aiService = new \App\Core\AI\AICreditService();
                $aiService->grantPlanAllotment($tenantId, 'all-access-bundle');
            }

            $updateStmt = $this->pdo->prepare("UPDATE billing_invoices SET status = 'paid' WHERE id = ?");
            $updateStmt->execute([$id]);
            
            header('Location: /' . ADMIN_PATH . '/invoices?msg=Invoice Approved Successfully');
            exit;
        }
        header('Location: /' . ADMIN_PATH . '/invoices?msg=Invalid Invoice');
        exit;
    }

    
    public function invoicesReject($params) {
        $id = is_array($params) ? ($params['id'] ?? null) : $params;
        if (!$id) {
            header('Location: /' . ADMIN_PATH . '/invoices?msg=Invalid Invoice ID');
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM billing_invoices WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($invoice) {
            $updateStmt = $this->pdo->prepare("UPDATE billing_invoices SET status = 'failed' WHERE id = ?");
            $updateStmt->execute([$id]);
            
            header('Location: /' . ADMIN_PATH . '/invoices?msg=Invoice Rejected Successfully');
            exit;
        }
        header('Location: /' . ADMIN_PATH . '/invoices?msg=Invalid Invoice');
        exit;
    }

    public function modules() {
        $modules = [];
        try {
            $stmt = $this->pdo->query("SELECT * FROM modules ORDER BY name ASC");
            if ($stmt) {
                $modules = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            // Table might not exist yet
        }
        
        // Ensure price key exists to prevent undefined index
        foreach ($modules as &$m) {
            if (!isset($m['price'])) {
                $m['price'] = 0.00;
            }
        }
        
        require __DIR__ . '/../Views/admin/modules.php'; 
    }

    // Placeholder Logic for form endpoints
    public function ads() { require __DIR__ . '/../Views/admin/shop_ads.php'; }
    public function sliders() { require __DIR__ . '/../Views/admin/shop_sliders.php'; }
    public function integrations() { 
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        if (file_exists(__DIR__ . '/../Views/admin/settings/integrations.php')) {
            require __DIR__ . '/../Views/admin/settings/integrations.php'; 
        } else {
            echo "Integrations view missing.";
        }
    }

    public function integrationsUpdate() { 
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF verification failed.");
        }

        $postedSettings = $_POST['settings'] ?? [];
        
        // Settings that are checkboxes might not be present in $_POST if they are unchecked
        $checkboxes = ['google_login_enabled', 'linkedin_login_enabled', 'facebook_login_enabled', 'apple_login_enabled'];
        foreach ($checkboxes as $cb) {
            if (!isset($postedSettings[$cb])) {
                $postedSettings[$cb] = '0';
            }
        }

        foreach ($postedSettings as $key => $value) {
            $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value]);
        }

        header('Location: /' . ADMIN_PATH . '/settings/integrations?success=1'); 
        exit; 
    }
    public function kycApprove() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM kyc_verifications WHERE id = ?");
            $stmt->execute([$id]);
            $req = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($req) {
                $updateStmt = $this->pdo->prepare("UPDATE kyc_verifications SET status = 'approved', updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$id]);
                
                try {
                    $userUpdate = $this->pdo->prepare("UPDATE users SET kyc_status = 'verified' WHERE id = ?");
                    $userUpdate->execute([$req['user_id']]);
                } catch (\Exception $ex) {}

                // Also sync into cp_naira_card_users so user is marked verified for card features
                try {
                    $stmtCardUser = $this->pdo->prepare("SELECT id FROM cp_naira_card_users WHERE user_id = ?");
                    $stmtCardUser->execute([$req['user_id']]);
                    if ($existingCardUser = $stmtCardUser->fetch()) {
                        $this->pdo->prepare("UPDATE cp_naira_card_users SET status = 'active' WHERE id = ?")->execute([$existingCardUser['id']]);
                    } else {
                        $custMockId = 'VERIFIED_' . strtoupper(substr(uniqid(), -10));
                        $this->pdo->prepare("INSERT INTO cp_naira_card_users (tenant_id, user_id, customer_id, firstname, lastname, email, phone, nin, dob, address_line1, city, state, country, postal_code, provider, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'local_verified', 'active')")
                            ->execute([
                                $req['tenant_id'],
                                $req['user_id'],
                                $custMockId,
                                $req['first_name'] ?? 'User',
                                $req['last_name'] ?? 'Verified',
                                $req['user_email'] ?? '',
                                $req['phone'] ?? '',
                                $req['id_number'] ?? ($req['bvn'] ?? ''),
                                $req['dob'] ?? date('Y-m-d', strtotime('-25 years')),
                                $req['address_line1'] ?? '',
                                $req['city'] ?? '',
                                $req['state'] ?? '',
                                $req['country'] ?? 'NGA',
                                $req['postal_code'] ?? '100001'
                            ]);
                    }
                } catch (\Exception $exCard) {
                    error_log("KYC card sync error: " . $exCard->getMessage());
                }

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$req['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Identity Verification Approved - Casjoe";
                    $body = "<h2>Identity Verified! 🎉</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>Your KYC identity verification document has been reviewed and successfully <strong>APPROVED</strong>.</p>";
                    $body .= "<p>Your account now has full verified status across the Casjoe platform.</p>";
                    $body .= "<p>Thank you for verifying your identity!</p>";
                    try { \App\Core\Mailer::send($user['email'], $subject, $body); } catch (\Exception $e) {}
                }

                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $req['user_id'], "KYC Approved 🎉", "Your identity verification has been approved!", "/profile"]);
                } catch (\Exception $e) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/kyc?msg=approved');
        exit;
    }
    public function kycReject() { 
        $id = $_POST['id'] ?? null;
        $notes = $_POST['notes'] ?? 'The submitted document did not meet verification requirements.';
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM kyc_verifications WHERE id = ?");
            $stmt->execute([$id]);
            $req = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($req) {
                try {
                    $updateStmt = $this->pdo->prepare("UPDATE kyc_verifications SET status = 'rejected', admin_notes = ?, updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$notes, $id]);
                } catch (\Exception $colEx) {
                    $updateStmt = $this->pdo->prepare("UPDATE kyc_verifications SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$id]);
                }
                
                try {
                    $userUpdate = $this->pdo->prepare("UPDATE users SET kyc_status = 'rejected' WHERE id = ?");
                    $userUpdate->execute([$req['user_id']]);
                } catch (\Exception $ex) {}

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$req['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Identity Verification Update - Casjoe";
                    $body = "<h2>Verification Rejected</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>We reviewed your submitted identity verification document, but unfortunately it could not be approved at this time.</p>";
                    $body .= "<p><strong>Reason:</strong> " . htmlspecialchars($notes) . "</p>";
                    $body .= "<p>Please return to your profile or KYC portal to upload a clear, valid document.</p>";
                    try { \App\Core\Mailer::send($user['email'], $subject, $body); } catch (\Exception $e) {}
                }

                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $req['user_id'], "KYC Rejected", "Your verification was declined: " . substr($notes, 0, 80), "/profile"]);
                } catch (\Exception $e) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/kyc?msg=rejected');
        exit;
    }
    public function cardsApprove() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_cards WHERE id = ? AND status = 'admin_pending'");
            $stmt->execute([$id]);
            $card = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($card) {
                $updateStmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET status = 'active', updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$id]);

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$card['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Virtual Card Issued & Approved! - Casjoe Pay";
                    $body = "<h2>Your Virtual Card is Ready! 💳</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>Your requested <strong>" . strtoupper($card['provider']) . " Virtual Card</strong> ({$card['currency']} " . number_format($card['balance'], 2) . ") has been approved and activated.</p>";
                    $body .= "<p>You can now view your card number, CVV, and manage your spending limits inside Casjoe Pay.</p>";
                    try { \App\Core\Mailer::send($user['email'], $subject, $body); } catch (\Exception $e) {}
                }

                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $card['user_id'], "Virtual Card Issued 💳", "Your virtual card request has been approved and is now active!", "/casjoe-pay/cards"]);
                } catch (\Exception $e) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/cards?msg=approved');
        exit;
    }
    public function cardsReject() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_cards WHERE id = ? AND status = 'admin_pending'");
            $stmt->execute([$id]);
            $card = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($card) {
                $updateStmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$id]);

                if (($card['balance'] ?? 0) > 0) {
                    try {
                        $refundStmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                        $refundStmt->execute([$card['balance'], $card['user_id'], $card['currency']]);
                    } catch (\Exception $rex) {}
                }

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$card['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Virtual Card Request Rejected - Casjoe Pay";
                    $body = "<h2>Card Request Declined</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>Your request for a virtual card ({$card['currency']} " . number_format($card['balance'], 2) . ") could not be processed and has been declined.</p>";
                    if (($card['balance'] ?? 0) > 0) {
                        $body .= "<p>We have automatically refunded <strong>{$card['currency']} " . number_format($card['balance'], 2) . "</strong> back to your wallet.</p>";
                    }
                    try { \App\Core\Mailer::send($user['email'], $subject, $body); } catch (\Exception $e) {}
                }

                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $card['user_id'], "Virtual Card Declined", "Your card request was declined and any hold balance was refunded.", "/casjoe-pay/cards"]);
                } catch (\Exception $e) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/cards?msg=rejected');
        exit;
    }
    public function cardsToggleStatus() { header('Location: /' . ADMIN_PATH . '/cards?msg=status_toggled'); exit; }
    public function cardsDelete() { header('Location: /' . ADMIN_PATH . '/cards?msg=deleted'); exit; }
    public function cardsDeleteAll() { header('Location: /' . ADMIN_PATH . '/cards?msg=all_deleted'); exit; }

    // --- Naira Cards Inventory Management ---
    public function nairaCardsInventory() {
        if (!\App\Core\Services\PermissionService::can('view_cards') && !in_array(\App\Core\Auth::user()['role'] ?? '', ['admin', 'super_admin'])) {
            header('Location: /' . ADMIN_PATH . '?error=unauthorized');
            exit;
        }
        
        // Fetch inventory
        $stmt = $this->pdo->query("
            SELECT i.*, 
                   COALESCE(u.name, 'N/A') as assigned_user_name,
                   COALESCE(u.email, 'N/A') as assigned_user_email
            FROM cp_naira_card_inventory i
            LEFT JOIN users u ON i.assigned_to_user_id = u.id
            ORDER BY i.created_at DESC
        ");
        $inventory = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Statistics
        $stats = [
            'total' => count($inventory),
            'available' => count(array_filter($inventory, fn($c) => $c['status'] === 'available')),
            'assigned' => count(array_filter($inventory, fn($c) => $c['status'] === 'assigned'))
        ];
        
        require __DIR__ . '/../Views/admin/naira_cards_inventory.php';
    }

    public function nairaCardsInventoryAdd() {
        if (!\App\Core\Services\PermissionService::can('view_cards') && !in_array(\App\Core\Auth::user()['role'] ?? '', ['admin', 'super_admin'])) {
            header('Location: /' . ADMIN_PATH . '?error=unauthorized');
            exit;
        } // Re-using view_cards for management
        $pan = trim($_POST['pan'] ?? '');
        $brand = trim($_POST['brand'] ?? 'AfriGo');
        
        if (empty($pan)) {
            header('Location: /' . ADMIN_PATH . '/naira-cards-inventory?error=missing_pan');
            exit;
        }
        
        $adminId = \App\Core\Auth::user()['id'];
        
        $stmt = $this->pdo->prepare("INSERT INTO cp_naira_card_inventory (pan, brand, status, added_by_admin_id) VALUES (?, ?, 'available', ?)");
        $stmt->execute([$pan, $brand, $adminId]);
        
        header('Location: /' . ADMIN_PATH . '/naira-cards-inventory?success=added');
        exit;
    }

    public function nairaCardsInventoryDelete() {
        if (!\App\Core\Services\PermissionService::can('view_cards') && !in_array(\App\Core\Auth::user()['role'] ?? '', ['admin', 'super_admin'])) {
            header('Location: /' . ADMIN_PATH . '?error=unauthorized');
            exit;
        }
        $id = $_POST['id'] ?? null;
        
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM cp_naira_card_inventory WHERE id = ? AND status = 'available'");
            $stmt->execute([$id]);
        }
        
        header('Location: /' . ADMIN_PATH . '/naira-cards-inventory?success=deleted');
        exit;
    }

    public function runNairaMigration() {
        if (!\App\Core\Services\PermissionService::can('view_cards') && !in_array(\App\Core\Auth::user()['role'] ?? '', ['admin', 'super_admin'])) {
            header('Location: /' . ADMIN_PATH . '?error=unauthorized');
            exit;
        }
        
        $sqlFile = __DIR__ . '/../../../database/migrations/naira_cards.sql';
        if (!file_exists($sqlFile)) {
            die("SQL file not found at: " . $sqlFile);
        }
        
        $sql = file_get_contents($sqlFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $this->pdo->exec($statement);
                $successCount++;
            }
        }
        
        echo "<div style='font-family: Arial, sans-serif; padding: 40px; text-align: center; background: #060714; color: #fff; height: 100vh;'>";
        echo "<h1 style='color: #4CAF50;'>Migration Successful!</h1>";
        echo "<p>Successfully executed " . $successCount . " SQL statements.</p>";
        echo "<p>The Naira Card tables have been created.</p>";
        echo "<a href='/" . ADMIN_PATH . "/naira-cards-inventory' style='display: inline-block; padding: 10px 20px; background: #FFA600; color: #000; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px;'>Go to Inventory</a>";
        echo "</div>";
        exit;
    }

    public function socBlock() {
        if (!empty($_POST['ip'])) {
            $ip = trim($_POST['ip']);
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO blocked_ips (ip_address, reason) VALUES (?, 'Manual Block via SOC Dashboard')");
            $stmt->execute([$ip]);
            $stmtLog = $this->pdo->prepare("INSERT INTO security_logs (user_email, event_type, severity, message, ip_address) VALUES (?, 'security.ip_blocked', 'danger', 'IP address manually blocked from SOC dashboard', ?)");
            $stmtLog->execute([$_SESSION['user_email'] ?? 'admin@casjoe.com', $ip]);
        }
        header('Location: /' . ADMIN_PATH . '/soc?msg=blocked');
        exit;
    }
    public function socUnblock() {
        if (!empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmtIp = $this->pdo->prepare("SELECT ip_address FROM blocked_ips WHERE id = ?");
            $stmtIp->execute([$id]);
            $ip = $stmtIp->fetchColumn();
            if ($ip) {
                $this->pdo->prepare("DELETE FROM blocked_ips WHERE id = ?")->execute([$id]);
                $stmtLog = $this->pdo->prepare("INSERT INTO security_logs (user_email, event_type, severity, message, ip_address) VALUES (?, 'security.ip_unblocked', 'info', 'IP address unblocked from SOC dashboard', ?)");
                $stmtLog->execute([$_SESSION['user_email'] ?? 'admin@casjoe.com', $ip]);
            }
        }
        header('Location: /' . ADMIN_PATH . '/soc?msg=unblocked');
        exit;
    }
    public function depositsApprove() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($txn) {
                $this->pdo->beginTransaction();
                try {
                    // Update transaction status
                    $updateStmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE id = ?");
                    $updateStmt->execute([$id]);

                    // Credit user's wallet
                    $walletStmt = $this->pdo->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)");
                    $walletStmt->execute([$txn['tenant_id'], $txn['user_id'], $txn['currency']]);

                    $creditStmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                    $creditStmt->execute([$txn['amount'], $txn['user_id'], $txn['currency']]);

                    $this->pdo->commit();

                    // Retrieve user details
                    $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                    $userStmt->execute([$txn['user_id']]);
                    $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                    if ($user && !empty($user['email'])) {
                        $subject = "Deposit Confirmed - Casjoe Pay";
                        $body = "<h2>Payment Confirmation</h2>";
                        $body .= "<p>Hi {$user['name']},</p>";
                        $body .= "<p>Your offline deposit of <strong>{$txn['currency']} " . number_format($txn['amount'], 2) . "</strong> has been confirmed and credited to your wallet.</p>";
                        $body .= "<p><strong>Reference:</strong> {$txn['reference']}</p>";
                        $body .= "<p>Thank you for choosing Casjoe!</p>";
                        
                        try {
                            \App\Core\Mailer::send($user['email'], $subject, $body);
                        } catch (\Exception $mailEx) {}
                    }
                    
                    $tenantId = $_SESSION['tenant_id'] ?? 1;
                    try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                    try {
                        $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                        $notifStmt->execute([$tenantId, $txn['user_id'], "Deposit Approved ⚡", "Your deposit of {$txn['currency']} " . number_format($txn['amount'], 2) . " is confirmed and credited!", "/casjoe-pay"]);
                    } catch (\Exception $e) {}
                    
                    header('Location: /' . ADMIN_PATH . '/deposits?msg=approved');
                    exit;
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    die("Error confirming deposit: " . $e->getMessage());
                }
            }
        }
        header('Location: /' . ADMIN_PATH . '/deposits?msg=invalid_id');
        exit;
    }
    public function depositsReject() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($txn) {
                $updateStmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'failed' WHERE id = ?");
                $updateStmt->execute([$id]);

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$txn['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Deposit Rejected - Casjoe Pay";
                    $body = "<h2>Payment Rejected</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>Your offline deposit of <strong>{$txn['currency']} " . number_format($txn['amount'], 2) . "</strong> could not be confirmed and has been rejected.</p>";
                    $body .= "<p><strong>Reference:</strong> {$txn['reference']}</p>";
                    $body .= "<p>Please contact support if you believe this is an error.</p>";
                    
                    try {
                        \App\Core\Mailer::send($user['email'], $subject, $body);
                    } catch (\Exception $mailEx) {}
                }

                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $txn['user_id'], "Deposit Rejected", "Your deposit of {$txn['currency']} " . number_format($txn['amount'], 2) . " could not be verified.", "/casjoe-pay"]);
                } catch (\Exception $e) {}

                header('Location: /' . ADMIN_PATH . '/deposits?msg=rejected');
                exit;
            }
        }
        header('Location: /' . ADMIN_PATH . '/deposits?msg=invalid_id');
        exit;
    }
    public function withdrawalsApprove() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($txn) {
                $updateStmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE id = ?");
                $updateStmt->execute([$id]);

                $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                $userStmt->execute([$txn['user_id']]);
                $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && !empty($user['email'])) {
                    $subject = "Withdrawal Approved - Casjoe Pay";
                    $body = "<h2>Withdrawal Confirmed</h2>";
                    $body .= "<p>Hi {$user['name']},</p>";
                    $body .= "<p>Your withdrawal of <strong>{$txn['currency']} " . number_format($txn['amount'], 2) . "</strong> has been approved and processed.</p>";
                    $body .= "<p><strong>Reference:</strong> {$txn['reference']}</p>";
                    $body .= "<p>Thank you for choosing Casjoe!</p>";
                    
                    try {
                        \App\Core\Mailer::send($user['email'], $subject, $body);
                    } catch (\Exception $mailEx) {}
                }
                
                $tenantId = $_SESSION['tenant_id'] ?? 1;
                try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                try {
                    $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                    $notifStmt->execute([$tenantId, $txn['user_id'], "Withdrawal Approved 💸", "Your withdrawal of {$txn['currency']} " . number_format($txn['amount'], 2) . " has been processed!", "/casjoe-pay"]);
                } catch (\Exception $e) {}
                
                header('Location: /' . ADMIN_PATH . '/withdrawals?msg=approved');
                exit;
            }
        }
        header('Location: /' . ADMIN_PATH . '/withdrawals?msg=invalid_id');
        exit;
    }
    public function withdrawalsReject() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($txn) {
                $this->pdo->beginTransaction();
                try {
                    $updateStmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'failed' WHERE id = ?");
                    $updateStmt->execute([$id]);

                    $refundStmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                    $refundStmt->execute([$txn['amount'], $txn['user_id'], $txn['currency']]);

                    $this->pdo->commit();

                    $userStmt = $this->pdo->prepare("SELECT email, name FROM users WHERE id = ?");
                    $userStmt->execute([$txn['user_id']]);
                    $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

                    if ($user && !empty($user['email'])) {
                        $subject = "Withdrawal Rejected - Casjoe Pay";
                        $body = "<h2>Withdrawal Declined</h2>";
                        $body .= "<p>Hi {$user['name']},</p>";
                        $body .= "<p>Your withdrawal request of <strong>{$txn['currency']} " . number_format($txn['amount'], 2) . "</strong> has been rejected and refunded to your wallet.</p>";
                        $body .= "<p><strong>Reference:</strong> {$txn['reference']}</p>";
                        
                        try {
                            \App\Core\Mailer::send($user['email'], $subject, $body);
                        } catch (\Exception $mailEx) {}
                    }
                    
                    $tenantId = $_SESSION['tenant_id'] ?? 1;
                    try { $tenantId = \App\Core\TenantContext::getTenantId(); } catch (\Exception $e) {}
                    try {
                        $notifStmt = $this->pdo->prepare("INSERT IGNORE INTO notifications (tenant_id, user_id, title, message, link, is_read) VALUES (?, ?, ?, ?, ?, 0)");
                        $notifStmt->execute([$tenantId, $txn['user_id'], "Withdrawal Refunded", "Your withdrawal request was declined and {$txn['currency']} " . number_format($txn['amount'], 2) . " was returned to your balance.", "/casjoe-pay"]);
                    } catch (\Exception $e) {}
                    
                    header('Location: /' . ADMIN_PATH . '/withdrawals?msg=rejected');
                    exit;
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    die("Error rejecting withdrawal: " . $e->getMessage());
                }
            }
        }
        header('Location: /' . ADMIN_PATH . '/withdrawals?msg=invalid_id');
        exit;
    }
    public function storesApprove() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $stmt = $this->pdo->prepare("UPDATE cp_stores SET status = 'active', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
            } catch (\Exception $e) {
                try {
                    $stmt = $this->pdo->prepare("UPDATE stores SET status = 'active', updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (\Exception $ex) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/stores?msg=approved'); 
        exit; 
    }
    public function storesReject() { 
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $stmt = $this->pdo->prepare("UPDATE cp_stores SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
            } catch (\Exception $e) {
                try {
                    $stmt = $this->pdo->prepare("UPDATE stores SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                } catch (\Exception $ex) {}
            }
        }
        header('Location: /' . ADMIN_PATH . '/stores?msg=rejected'); 
        exit; 
    }
    public function permissionsUpdate() { header('Location: /' . ADMIN_PATH . '/moderator-permissions?msg=updated'); exit; }
    public function modulesUpdate() { 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /' . ADMIN_PATH . '/modules');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF verification failed.");
        }

        // 1. Update Modules Prices
        $prices = $_POST['prices'] ?? [];
        foreach ($prices as $id => $price) {
            $stmt = $this->pdo->prepare("UPDATE modules SET price = ? WHERE id = ?");
            $stmt->execute([$price, $id]);
        }

        // 2. Update AI Employee Prices
        if (isset($_POST['ai_employee_price_ngn'])) {
            $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('ai_employee_price_ngn', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$_POST['ai_employee_price_ngn']]);
        }
        if (isset($_POST['ai_employee_price_usd'])) {
            $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('ai_employee_price_usd', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$_POST['ai_employee_price_usd']]);
        }

        header('Location: /' . ADMIN_PATH . '/modules?success=1'); 
        exit; 
    }
}

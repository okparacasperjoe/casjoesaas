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
        // Ensure Admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        
        // Simple Admin Check (Role based)
        // Fetch user role
        $stmt = $this->pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $role = $stmt->fetchColumn();

        if ($role !== 'admin') {
            die("Access Denied: Admin privileges required.");
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

        $stats = [
            'users' => $safeFetch("SELECT COUNT(*) FROM users"),
            'revenue' => $safeFetch("SELECT SUM(amount) FROM transactions WHERE status = 'successful'"),
            'apps' => $safeFetch("SELECT COUNT(*) FROM modules"),
            'cards' => $safeFetch("SELECT COUNT(*) FROM cp_virtual_cards"),
            'links' => $safeFetch("SELECT COUNT(*) FROM cp_payment_links"),
        ];

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
        header('Location: /admin/broadcast?success=1');
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

        header('Location: /admin/users');
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

        header('Location: /admin/users');
    }

    public function impersonate($params)
    {
        $targetId = $params['id'];

        // Store original admin id if not already stored
        if (!isset($_SESSION['impersonator_id'])) {
            $_SESSION['impersonator_id'] = $_SESSION['user_id'];
        }

        $_SESSION['user_id'] = $targetId;
        
        header('Location: /');
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

        require __DIR__ . '/../Views/admin/settings.php';
    }

    public function updateSettings()
    {
        $fields = [
            'payment_routing_mode',
            'flutterwave_public_key', 'flutterwave_secret_key',
            'paystack_public_key',  'paystack_secret_key',
            'sudo_api_key', 'sudo_api_secret'
        ];

        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                // CSRF Check once
                if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
                     die("CSRF Token Verification Failed");
                }

                $value = trim($_POST[$key]);
                
                // Insert or Update
                $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                $stmt->execute([$key, $value]);
            }
        }

        $tab = $_POST['tab'] ?? 'general';
        header('Location: /admin/settings?tab=' . $tab . '&success=1');
    }
}

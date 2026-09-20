<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Auth;
use App\Core\TenantContext;

class AdminSettingsController
{
    private $pdo;

    public function __construct()
    {
        // Ensure Admin or Moderator
        $user = Auth::user();
        if (!$user || !in_array($user['role'], ['admin', 'moderator']) || $user['tenant_id'] != 1) {
            die("<h1>Access Denied</h1><p>You do not have permission to access the Settings Admin Panel.</p><p><a href='/dashboard'>Return to Dashboard</a></p>");
        }
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // 1. Fetch All Modules
        $stmt = $this->pdo->query("SELECT * FROM modules ORDER BY name ASC");
        $modules = $stmt->fetchAll();

        // 2. Fetch All Settings
        $stmt = $this->pdo->query("SELECT * FROM system_settings");
        $settingsRaw = $stmt->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }

        $title = 'System Configuration';
        require __DIR__ . '/../Views/admin/settings/index.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /casper-joe/settings');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        // 1. Update Settings
        $settings = $_POST['settings'] ?? [];
        foreach ($settings as $key => $value) {
            $group = 'general';
            if (strpos($key, 'bank_') === 0) $group = 'billing';
            if (strpos($key, 'smtp_') === 0) $group = 'email';
            if (strpos($key, 'recaptcha_') === 0) $group = 'security';

            $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value, $group]);
        }
        
        // Handle checkbox logic for reCAPTCHA (if unchecked, it won't be in POST)
        if (!isset($settings['recaptcha_enabled'])) {
             $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
             $stmt->execute(['recaptcha_enabled', '0', 'security']);
        }

        // 2. Update Modules
        $modules = $_POST['modules'] ?? [];
        foreach ($modules as $id => $data) {
            $stmt = $this->pdo->prepare("UPDATE modules SET price = ?, free_limit = ?, paid_limit = ? WHERE id = ?");
            // CAST to ensure NULL if empty
            $freeLimit = $data['free_limit'] === '' ? null : $data['free_limit'];
            
            $stmt->execute([
                $data['price'], 
                $freeLimit, 
                $data['paid_limit'],
                $id
            ]);
        }
        
        header('Location: /casper-joe/settings?success=1');
        exit;
    }

    public function integrations()
    {
        // Fetch All Settings
        $stmt = $this->pdo->query("SELECT * FROM system_settings");
        $settingsRaw = $stmt->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }

        $title = 'Third-Party Integrations';
        require __DIR__ . '/../Views/admin/settings/integrations.php';
    }

    public function updateIntegrations()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /casper-joe/settings/integrations');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $settings = $_POST['settings'] ?? [];
        
        // Define all checkboxes to handle the "unchecked" state
        $checkboxes = [
            'stripe_enabled', 'paypal_enabled', 'razorpay_enabled', 'paystack_enabled',
            'flutterwave_enabled', 'google_login_enabled', 'apple_login_enabled'
        ];

        foreach ($checkboxes as $cb) {
            if (!isset($settings[$cb])) $settings[$cb] = '0';
        }

        foreach ($settings as $key => $value) {
            if (strpos($key, 'ai_') === 0 || strpos($key, 'gemini_') === 0 || strpos($key, 'openai_') === 0 || strpos($key, 'huggingface_') === 0) {
                $group = 'ai';
            }
            
            $stmt = $this->pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value, $group]);
        }

        header('Location: /casper-joe/settings/integrations?success=1');
        exit;
    }
}

<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Auth;
use App\Core\Mailer;
use App\Core\EmailTemplate;

class AdminEmailSettingsController
{
    private $pdo;

    public function __construct()
    {
        // Ensure Super Admin or Moderator
        if (!Auth::isSuperAdmin() && !Auth::isModerator()) {
            Auth::denySuperAdminAccess('Email Infrastructure Settings');
        }
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Fetch all email/SMTP/social settings
        $stmt = $this->pdo->query("SELECT * FROM system_settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_%' OR setting_key LIKE 'social_%'");
        $settingsRaw = $stmt->fetchAll();
        
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }

        require __DIR__ . '/../Views/admin/email_settings.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /' . ADMIN_PATH . '/email-settings');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        // Update each setting
        $allowedKeys = [
            'smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_username', 
            'smtp_password', 'smtp_encryption', 'smtp_from_address', 'smtp_from_name',
            'social_facebook', 'social_twitter', 'social_instagram', 
            'social_linkedin', 'social_youtube',
            'email_logo_url', 'email_brand_color', 'email_support_email'
        ];

        foreach ($_POST as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO system_settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute([$key, $value]);
            }
        }

        // Handle SMTP enabled checkbox (unchecked = not in POST)
        if (!isset($_POST['smtp_enabled'])) {
            $stmt = $this->pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->execute(['smtp_enabled', '0']);
        }

        header('Location: /' . ADMIN_PATH . '/email-settings?success=1');
        exit;
    }

    public function test()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }

        try {
            // Send test email using template
            $emailContent = EmailTemplate::render('welcome', [
                'name' => 'Admin',
                'dashboardUrl' => 'https://app.casjoe.com/dashboard',
                'title' => 'Test Email'
           ]);

            $subject = 'Test Email from Casjoe - Email Settings';
            
            $result = Mailer::send($email, $subject, $emailContent);

            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Test email sent successfully! Check your inbox.'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to send email. Please check your SMTP configuration.'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}

<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use App\Core\Services\GeminiService;
use PDO;

class MailController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        // Basic Init
    }

    private function init() {
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $this->init();
        
        // Dashboard Stats
        $stats = [
            'campaigns' => $this->pdo->query("SELECT COUNT(*) FROM cm_campaigns WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
            'subscribers' => $this->pdo->query("SELECT COUNT(*) FROM cm_subscribers WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
            'emails_sent' => $this->pdo->query("SELECT COALESCE(SUM(sent_count), 0) FROM cm_campaigns WHERE tenant_id = {$this->tenantId}")->fetchColumn(),
        ];

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function forms()
    {
        $this->init();
        $stmt = $this->pdo->prepare("SELECT * FROM cm_lists WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/forms/index.php';
    }

    public function campaigns()
    {
        $this->init();
        $stmt = $this->pdo->query("SELECT * FROM cm_campaigns WHERE tenant_id = {$this->tenantId} ORDER BY created_at DESC");
        $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/campaigns/index.php';
    }

    public function templates()
    {
        $this->init();
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE tenant_id = ? OR tenant_id IS NULL ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/templates/index.php'; // Updated path to standard structure
    }

    public function viewTemplate()
    {
        $this->init();
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE id = ? AND (tenant_id = ? OR tenant_id IS NULL)");
        $stmt->execute([$id, $this->tenantId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            header('Location: /mail/templates');
            exit;
        }

        require __DIR__ . '/../Views/templates/show.php';
    }

    public function createCampaign()
    {
        $this->init();
        // Fetch User and System Templates
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE tenant_id = ? OR tenant_id IS NULL");
        $stmt->execute([$this->tenantId]);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Lists
        $stmt = $this->pdo->prepare("SELECT * FROM cm_lists WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/campaigns/create.php';
    }

    public function generateAiContent()
    {
        header('Content-Type: application/json');
        
        // Basic Security
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $prompt = $_POST['prompt'] ?? '';
        $tone = $_POST['tone'] ?? 'professional';

        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt is required']);
            exit;
        }

        $gemini = new GeminiService();
        $content = $gemini->generateEmailContent($prompt, $tone);

        echo json_encode(['content' => $content]);
        exit;
    }

    public function templateContent($id)
    {
        $this->init();
        // Return raw content for AJAX
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT content FROM cm_templates WHERE id = ? AND (tenant_id = ? OR tenant_id IS NULL)");
        $stmt->execute([$id, $this->tenantId]);
        echo $stmt->fetchColumn();
        exit;
    }

    public function getIntegrations()
    {
        $this->init();
        $type = $_GET['type'] ?? 'smart_forms';
        $items = [];

        if ($type === 'smart_forms') {
            try {
                $stmt = $this->pdo->prepare("SELECT id, title as name FROM smart_forms WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $items[] = ['name' => $row['name'], 'url' => 'https://app.casjoe.com/forms/' . $row['id']];
                }
            } catch (\Exception $e) {}
        } elseif ($type === 'cloud') {
            try {
                $stmt = $this->pdo->prepare("SELECT id, file_name as name, file_path, file_type FROM cc_files WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $items[] = [
                        'name' => $row['name'], 
                        'url' => 'https://app.casjoe.com/' . ltrim($row['file_path'], '/'),
                        'meta_type' => $row['file_type'] // Used to know if it's an image/video/doc
                    ];
                }
            } catch (\Exception $e) {}
        } elseif ($type === 'academy') {
            try {
                $stmt = $this->pdo->prepare("SELECT id, title as name, slug FROM academy_courses WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $items[] = ['name' => $row['name'], 'url' => 'https://app.casjoe.com/academy/course/' . $row['slug']];
                }
            } catch (\Exception $e) {}
        } elseif ($type === 'shop') {
            try {
                $stmt = $this->pdo->prepare("SELECT id, name, slug FROM shop_products WHERE tenant_id = ? OR vendor_id IN (SELECT id FROM shop_vendors WHERE tenant_id = ?)");
                $stmt->execute([$this->tenantId, $this->tenantId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $items[] = ['name' => $row['name'], 'url' => 'https://app.casjoe.com/shop/product/' . $row['slug']];
                }
            } catch (\Exception $e) {}
        } elseif ($type === 'links') {
            try {
                $stmt = $this->pdo->prepare("SELECT id, short_code as name FROM links_short_urls WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $items[] = ['name' => 'Short URL: ' . $row['name'], 'url' => 'https://app.casjoe.com/l/' . $row['name']];
                }
                
                $stmt = $this->pdo->prepare("SELECT id, title as name, slug FROM links_bio_pages WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                $rows2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows2 as $row) {
                    $items[] = ['name' => 'Bio Page: ' . $row['name'], 'url' => 'https://app.casjoe.com/bio/' . $row['slug']];
                }
            } catch (\Exception $e) {}
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'items' => $items]);
        exit;
    }

    public function sendCampaign()
    {
        $this->init();
        $id = $_POST['id'] ?? 0;
        
        // Fetch Campaign
        $stmt = $this->pdo->prepare("SELECT * FROM cm_campaigns WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $campaign = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$campaign) {
            header('Location: /mail/campaigns?error=Campaign not found');
            exit;
        }

        // Fetch List
        $stmt = $this->pdo->prepare("SELECT * FROM cm_subscribers WHERE list_id = ? AND tenant_id = ?");
        $stmt->execute([$campaign['list_id'], $this->tenantId]);
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Settings
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM cm_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tenantSettings = [];
        foreach ($rows as $row) $tenantSettings[$row['setting_key']] = $row['setting_value'];

        // Load System Config
        $systemConfig = require __DIR__ . '/../../../../config/mail.php';

        // Merge: System SMTP + Tenant Sender Info
        $settings = array_merge($systemConfig, [
            'from_email' => $tenantSettings['from_email'] ?? $systemConfig['from_email'],
            'from_name' => $tenantSettings['from_name'] ?? $systemConfig['from_name']
        ]);

        // Override SMTP settings if the tenant provided a custom SMTP host
        if (!empty($tenantSettings['smtp_host'])) {
            $settings['host'] = $tenantSettings['smtp_host'];
            $settings['port'] = !empty($tenantSettings['smtp_port']) ? $tenantSettings['smtp_port'] : 587;
            $settings['username'] = $tenantSettings['smtp_username'] ?? '';
            $settings['password'] = $tenantSettings['smtp_password'] ?? '';
            $settings['encryption'] = $tenantSettings['smtp_encryption'] ?? '';
        }

        require_once __DIR__ . '/../../../Core/Services/SmtpService.php';

        try {
            $smtp = new \App\Core\Services\SmtpService($settings);
            
            $sentCount = 0;
            foreach ($subscribers as $sub) {
                // Simple template replacement
                $body = str_replace(
                    ['{{subscriber_name}}', '{{company_name}}'], 
                    [$sub['first_name'], 'Your Company'], 
                    $campaign['content']
                );
                
                try {
                    $smtp->send($sub['email'], $campaign['subject'], $body);
                    $sentCount++;
                } catch (\Exception $e) {
                    // Log error but continue
                    error_log("Mail Send Error: " . $e->getMessage());
                }
            }

            // Update Campaign Status
            $stmt = $this->pdo->prepare("UPDATE cm_campaigns SET status = 'sent', sent_count = ?, sent_at = NOW() WHERE id = ?");
            $stmt->execute([$sentCount, $id]);

            header('Location: /mail/campaigns?status=sent');

        } catch (\Exception $e) {
            header('Location: /mail/campaigns?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function deleteCampaign()
    {
        $this->init();
        $id = $_POST['id'] ?? 0;
        $stmt = $this->pdo->prepare("DELETE FROM cm_campaigns WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /mail/campaigns');
        exit;
    }

    public function deleteTemplate()
    {
        $this->init();
        $id = $_POST['id'] ?? 0;
        $stmt = $this->pdo->prepare("DELETE FROM cm_templates WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /mail/templates');
        exit;
    }

    public function editCampaign()
    {
        $this->init();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM cm_campaigns WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $campaign = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$campaign) {
            header('Location: /mail/campaigns');
            exit;
        }

        $stmtLists = $this->pdo->prepare("SELECT * FROM cm_lists WHERE tenant_id = ?");
        $stmtLists->execute([$this->tenantId]);
        $lists = $stmtLists->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/campaigns/edit.php';
    }

    public function updateCampaign()
    {
        $this->init();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['title'] ?? ($_POST['name'] ?? ''));
        $subject = trim($_POST['subject'] ?? '');
        $listId = (int)($_POST['list_id'] ?? 0);
        $content = $_POST['content'] ?? '';

        $stmt = $this->pdo->prepare("UPDATE cm_campaigns SET name = ?, subject = ?, list_id = ?, content = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $subject, $listId, $content, $id, $this->tenantId]);

        header('Location: /mail/campaigns?updated=1');
        exit;
    }

    public function saveVisualCampaign()
    {
        $this->init();
        header('Content-Type: application/json');

        $name = trim($_POST['name'] ?? 'Visual Campaign');
        $subject = trim($_POST['subject'] ?? '');
        $content = $_POST['content'] ?? '';
        $listId = (int)($_POST['list_id'] ?? 0);

        try {
            $stmt = $this->pdo->prepare("INSERT INTO cm_campaigns (tenant_id, name, subject, content, list_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'draft', NOW())");
            $stmt->execute([$this->tenantId, $name, $subject, $content, $listId]);
            echo json_encode(['status' => 'success', 'id' => $this->pdo->lastInsertId()]);
        } catch (\Throwable $t) {
            echo json_encode(['status' => 'error', 'message' => $t->getMessage()]);
        }
        exit;
    }

    public function createTemplate()
    {
        $this->init();
        require __DIR__ . '/../Views/templates/create.php';
    }

    public function saveTemplate()
    {
        $this->init();
        $name = trim($_POST['name'] ?? 'New Template');
        $content = $_POST['content'] ?? '';

        $stmt = $this->pdo->prepare("INSERT INTO cm_templates (tenant_id, name, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$this->tenantId, $name, $content]);

        header('Location: /mail/templates?saved=1');
        exit;
    }

    public function editTemplate()
    {
        $this->init();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE id = ? AND (tenant_id = ? OR tenant_id IS NULL)");
        $stmt->execute([$id, $this->tenantId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            header('Location: /mail/templates');
            exit;
        }

        require __DIR__ . '/../Views/templates/edit.php';
    }

    public function updateTemplate()
    {
        $this->init();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $content = $_POST['content'] ?? '';

        $stmt = $this->pdo->prepare("UPDATE cm_templates SET name = ?, content = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $content, $id, $this->tenantId]);

        header('Location: /mail/templates?updated=1');
        exit;
    }

    public function compose()
    {
        $this->init();
        require __DIR__ . '/../Views/compose.php';
    }

    public function send()
    {
        $this->init();
        $to = trim($_POST['to'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = $_POST['message'] ?? '';

        if (!empty($to) && !empty($subject)) {
            try {
                if (class_exists('\\App\\Core\\Services\\EmailService')) {
                    \App\Core\Services\EmailService::send($to, $subject, $message);
                }
            } catch (\Throwable $t) {
                error_log("Mail send error: " . $t->getMessage());
            }
        }

        header('Location: /mail?sent=1');
        exit;
    }

    public function subscribe()
    {
        $listId = (int)($_GET['list_id'] ?? 0);
        require __DIR__ . '/../Views/public/subscribe.php';
    }

    public function subscribeStore()
    {
        $listId = (int)($_POST['list_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');

        if (!empty($email) && $listId > 0) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT tenant_id FROM cm_lists WHERE id = ?");
            $stmt->execute([$listId]);
            $tenantId = $stmt->fetchColumn() ?: 1;

            try {
                $insert = $db->prepare("INSERT INTO cm_subscribers (tenant_id, list_id, email, first_name, status, created_at) VALUES (?, ?, ?, ?, 'subscribed', NOW()) ON DUPLICATE KEY UPDATE first_name = VALUES(first_name)");
                $insert->execute([$tenantId, $listId, $email, $firstName]);
            } catch (\Throwable $t) {}
        }

        header("Location: /mail/subscribe?list_id={$listId}&status=success");
        exit;
    }
}

<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\Services\PermissionService;
use PDO;

class AdminEmailTemplateController
{
    private $pdo;

    public function __construct()
    {
        $user = Auth::user();
        if (!$user || (!in_array($user['role'], ['admin', 'super_admin']) && !PermissionService::can('manage_settings'))) {
            die("Access Denied");
        }
        $this->pdo = Database::getInstance()->getConnection();

        // Auto-create cm_templates if not exists
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cm_templates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                name VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                category VARCHAR(100) DEFAULT NULL,
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed initial global templates if table is empty
            $stmtCount = $this->pdo->query("SELECT COUNT(*) FROM cm_templates");
            if ($stmtCount && (int)$stmtCount->fetchColumn() === 0) {
                $welcomeHtml = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #090a0f; color: #f1f5f9; padding: 30px; border-radius: 12px; border: 1px solid #FFA600;'><h1 style='color: #FFA600;'>Welcome to Casjoe!</h1><p>We are thrilled to have you onboard. Get started by setting up your AI Employees or funding your wallet.</p><a href='https://app.casjoe.com/dashboard' style='display: inline-block; background: #FFA600; color: #090a0f; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; margin-top: 15px;'>Go to Dashboard</a></div>";
                $resetHtml = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #090a0f; color: #f1f5f9; padding: 30px; border-radius: 12px; border: 1px solid #3b82f6;'><h1 style='color: #3b82f6;'>Password Reset Request</h1><p>We received a request to reset your Casjoe account password. If you did not make this request, ignore this email.</p><p style='margin-top: 20px;'>Click below to verify your identity:</p></div>";
                $invoiceHtml = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #090a0f; color: #f1f5f9; padding: 30px; border-radius: 12px; border: 1px solid #10b981;'><h1 style='color: #10b981;'>Invoice Summary</h1><p>Thank you for your business. Your recent Casjoe SaaS subscription invoice is ready.</p></div>";

                $stmtSeed = $this->pdo->prepare("INSERT INTO cm_templates (tenant_id, name, subject, category, content) VALUES (?, ?, ?, ?, ?)");
                $stmtSeed->execute([null, 'Welcome Onboarding Email', 'Welcome to Casjoe - Get Started!', 'Onboarding', $welcomeHtml]);
                $stmtSeed->execute([null, 'Password Reset Notification', 'Reset your Casjoe Password', 'Security', $resetHtml]);
                $stmtSeed->execute([null, 'Subscription Invoice Receipt', 'Your Casjoe Invoice Statement', 'Billing', $invoiceHtml]);
            }
        } catch (\Exception $e) { /* Ignore */ }
    }

    public function index()
    {
        // Auto-seed system templates if missing to ensure columns exist
        try { $this->pdo->exec("ALTER TABLE cm_templates ADD COLUMN IF NOT EXISTS category VARCHAR(100) DEFAULT NULL"); } catch (\Exception $e) {}

        // Fetch Global Templates (tenant_id IS NULL or tenant_id = 1)
        // We will assume tenant_id = NULL means "System Template accessible by all users"
        // and tenant_id = 1 means "Admin private template". For the master admin, we fetch both.
        $stmt = $this->pdo->query("SELECT * FROM cm_templates WHERE tenant_id IS NULL OR tenant_id = 1 ORDER BY created_at DESC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($templates as $t) {
            $cat = $t['category'] ?: 'Uncategorised';
            $grouped[$cat][] = $t;
        }

        require __DIR__ . '/../Views/admin/email_templates/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/email_templates/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Token Failed");

        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = $_POST['content'] ?? '';
        
        // Check if global
        $isGlobal = isset($_POST['is_global']) && $_POST['is_global'] == '1';
        $tenantId = $isGlobal ? null : 1;

        $stmt = $this->pdo->prepare("INSERT INTO cm_templates (tenant_id, name, subject, category, content, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$tenantId, $name, $subject, $category, $content]);

        header('Location: /' . ADMIN_PATH . '/email-templates?message=Template Created Successfully');
    }

    public function edit($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cm_templates WHERE id = ? AND (tenant_id IS NULL OR tenant_id = 1)");
        $stmt->execute([$id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            die("Template not found or access denied.");
        }

        require __DIR__ . '/../Views/admin/email_templates/edit.php';
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Token Failed");

        $id = $params['id'];
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = $_POST['content'] ?? '';
        
        $isGlobal = isset($_POST['is_global']) && $_POST['is_global'] == '1';
        $tenantId = $isGlobal ? null : 1;

        $stmt = $this->pdo->prepare("UPDATE cm_templates SET tenant_id = ?, name = ?, subject = ?, category = ?, content = ? WHERE id = ? AND (tenant_id IS NULL OR tenant_id = 1)");
        $stmt->execute([$tenantId, $name, $subject, $category, $content, $id]);

        header('Location: /' . ADMIN_PATH . '/email-templates?message=Template Updated Successfully');
    }

    public function delete($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("DELETE FROM cm_templates WHERE id = ? AND (tenant_id IS NULL OR tenant_id = 1)");
        $stmt->execute([$id]);
        
        header('Location: /' . ADMIN_PATH . '/email-templates?message=Template Deleted');
    }
}

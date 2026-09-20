<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\Services\PermissionService;
use App\Core\Mailer;
use PDO;

class AdminEmailCampaignController
{
    private $pdo;

    public function __construct()
    {
        $user = Auth::user();
        if (!$user || (!in_array($user['role'], ['admin', 'super_admin']) && !PermissionService::can('manage_settings'))) {
            die("Access Denied");
        }
        $this->pdo = Database::getInstance()->getConnection();

        // Auto-create cm_campaigns if not exists
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cm_campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                name VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                content TEXT,
                list_id INT NULL,
                status VARCHAR(50) DEFAULT 'draft',
                sent_count INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Exception $e) { /* Ignore */ }
    }

    public function index()
    {
        // Admin campaigns are stored with tenant_id = 1 or IS NULL (though usually 1 for the admin's own campaigns)
        $stmt = $this->pdo->query("SELECT * FROM cm_campaigns WHERE tenant_id = 1 OR tenant_id IS NULL ORDER BY created_at DESC");
        $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/email_campaigns/index.php';
    }

    public function create()
    {
        // Fetch Admin templates for selection
        $stmt = $this->pdo->query("SELECT * FROM cm_templates WHERE tenant_id IS NULL OR tenant_id = 1 ORDER BY name ASC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/email_campaigns/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Token Failed");

        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $content = $_POST['content'] ?? '';
        $listId = $_POST['list_id'] ?? null; // For admin, this could be null to mean "All Users" or a specific cm_list. We will default to system broadcast behavior if null.
        
        $stmt = $this->pdo->prepare("INSERT INTO cm_campaigns (tenant_id, name, subject, content, list_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'draft', NOW())");
        $stmt->execute([1, $name, $subject, $content, empty($listId) ? null : $listId]);

        header('Location: /' . ADMIN_PATH . '/email-campaigns?message=Campaign Draft Saved');
    }

    public function edit($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cm_campaigns WHERE id = ? AND (tenant_id = 1 OR tenant_id IS NULL)");
        $stmt->execute([$id]);
        $campaign = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$campaign) die("Campaign not found.");

        $stmt = $this->pdo->query("SELECT * FROM cm_templates WHERE tenant_id IS NULL OR tenant_id = 1 ORDER BY name ASC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/email_campaigns/edit.php';
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) die("CSRF Token Failed");

        $id = $params['id'];
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $content = $_POST['content'] ?? '';

        $stmt = $this->pdo->prepare("UPDATE cm_campaigns SET name = ?, subject = ?, content = ? WHERE id = ? AND (tenant_id = 1 OR tenant_id IS NULL)");
        $stmt->execute([$name, $subject, $content, $id]);

        header('Location: /' . ADMIN_PATH . '/email-campaigns?message=Campaign Updated');
    }

    public function delete($params)
    {
        $id = $params['id'];
        $stmt = $this->pdo->prepare("DELETE FROM cm_campaigns WHERE id = ? AND (tenant_id = 1 OR tenant_id IS NULL)");
        $stmt->execute([$id]);
        
        header('Location: /' . ADMIN_PATH . '/email-campaigns?message=Campaign Deleted');
    }
}

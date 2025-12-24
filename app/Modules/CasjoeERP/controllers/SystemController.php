<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class SystemController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function settings()
    {
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        require __DIR__ . '/../views/system/settings.php';
    }

    public function updateSettings()
    {
        foreach ($_POST as $key => $value) {
            // Simple upsert logic
            $sql = "INSERT INTO erp_settings (tenant_id, setting_key, setting_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->tenantId, $key, $value, $value]);
        }
        
        header('Location: /erp/settings');
        exit;
    }

    public function activity()
    {
        // Join with users table if it exists, otherwise just show raw ID
        $stmt = $this->pdo->prepare("SELECT * FROM erp_activity_log WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$this->tenantId]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/system/activity.php';
    }

    public function announcements()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_announcements WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/system/announcements.php';
    }

    public function createAnnouncement()
    {
        require __DIR__ . '/../views/system/create_announcement.php';
    }

    public function storeAnnouncement()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $createdBy = 1; // Default user for now

        $stmt = $this->pdo->prepare("INSERT INTO erp_announcements (tenant_id, title, content, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $content, $createdBy]);

        header('Location: /erp/announcements');
        exit;
    }

    public function roles()
    {
        // Placeholder data or detailed implementation
        $roles = [
            ['name' => 'Admin', 'description' => 'Full System Access'],
            ['name' => 'Manager', 'description' => 'Department Access'],
            ['name' => 'Employee', 'description' => 'Self Service Only'],
        ];
        require __DIR__ . '/../views/system/roles.php';
    }

    public function permissions()
    {
        $permissions = [
            'hrm_read' => 'View HR Data',
            'hrm_write' => 'Manage HR Data',
            'fin_read' => 'View Finance',
            'fin_write' => 'Manage Finance',
        ];
        require __DIR__ . '/../views/system/permissions.php';
    }

    public function reports()
    {
        require __DIR__ . '/../views/system/reports.php';
    }

    public function audit_log()
    {
        // Re-use activity log or separate
        $this->activity();
    }

    public function support()
    {
        require __DIR__ . '/../views/system/support.php';
    }

    public function company()
    {
        // Fetch company details from settings
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        require __DIR__ . '/../views/system/company.php';
    }
}

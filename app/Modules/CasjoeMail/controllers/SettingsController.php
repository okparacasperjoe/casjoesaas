<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class SettingsController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Fetch all settings for tenant
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM cm_settings WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        require __DIR__ . '/../Views/settings.php';
    }

    public function save()
    {
        $fields = ['from_email', 'from_name', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption'];
        
        foreach ($fields as $field) {
            $value = $_POST[$field] ?? '';
            // Upsert setting
            $stmt = $this->pdo->prepare("
                INSERT INTO cm_settings (tenant_id, setting_key, setting_value) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$this->tenantId, $field, $value, $value]);
        }

        header('Location: /mail/settings?status=saved');
        exit;
    }
}

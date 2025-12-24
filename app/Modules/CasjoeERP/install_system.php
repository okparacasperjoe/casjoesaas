<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/system_install.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "System Module installed successfully.\n";

    // Seed some data if empty
    $count = $db->query("SELECT COUNT(*) FROM erp_settings")->fetchColumn();
    if ($count == 0) {
        echo "Seeding default system data...\n";
        $tenantId = 1; 
        
        $db->exec("INSERT INTO erp_settings (tenant_id, setting_key, setting_value) VALUES 
            ($tenantId, 'company_name', 'My SaaS Company'),
            ($tenantId, 'timezone', 'UTC')
        ");
        
        $db->exec("INSERT INTO erp_announcements (tenant_id, title, content) VALUES 
            ($tenantId, 'Welcome to Casjoe ERP', 'We are excited to launch the new modules!')
        ");
        
        $db->exec("INSERT INTO erp_activity_log (tenant_id, user_id, action, details) VALUES 
            ($tenantId, 1, 'system_init', 'System module initialized.')
        ");
        
        echo "Seeding complete.\n";
    }

} catch (Exception $e) {
    die("Error installing System module: " . $e->getMessage() . "\n");
}

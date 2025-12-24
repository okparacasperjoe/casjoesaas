<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/projects_install.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "Projects Module installed successfully.\n";

    // Seed some data if empty
    $count = $db->query("SELECT COUNT(*) FROM erp_projects")->fetchColumn();
    if ($count == 0) {
        echo "Seeding default projects...\n";
        $tenantId = 1; 
        
        $db->exec("INSERT INTO erp_projects (tenant_id, name, description, status) VALUES 
            ($tenantId, 'Website Redesign', 'Revamp corporate website', 'in_progress'),
            ($tenantId, 'Mobile App Launch', 'Launch Android version', 'not_started')
        ");
        
        $projId = $db->lastInsertId();
        
        $db->exec("INSERT INTO erp_tasks (tenant_id, project_id, title, status) VALUES 
            ($tenantId, $projId, 'Design Mockups', 'done'),
            ($tenantId, $projId, 'Develop API', 'in_progress')
        ");
        
        echo "Seeding complete.\n";
    }

} catch (Exception $e) {
    die("Error installing Projects module: " . $e->getMessage() . "\n");
}

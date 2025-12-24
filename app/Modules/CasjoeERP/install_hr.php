<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/hr_install.sql');
    
    // Split by semicolon to execute one by one if necessary, or just execute batch if driver supports it.
    // PDO can usually handle multiple queries if emulate prepares is on, but safer to split.
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "HR Modules installed successfully.\n";

    // Seed some data if empty
    $count = $db->query("SELECT COUNT(*) FROM erp_departments")->fetchColumn();
    if ($count == 0) {
        echo "Seeding default departments and employees...\n";
        $tenantId = 1; // Default tenant
        
        $db->exec("INSERT INTO erp_departments (tenant_id, name, description) VALUES 
            ($tenantId, 'Human Resources', 'Handles employee relations and payroll'),
            ($tenantId, 'IT & Engineering', 'Software development and infrastructure'),
            ($tenantId, 'Sales & Marketing', 'Driving revenue and brand awareness')
        ");
        
        $deptId = $db->lastInsertId(); // Should get one of them
        
        $db->exec("INSERT INTO erp_employees (tenant_id, department_id, first_name, last_name, email, job_title, salary, status) VALUES 
            ($tenantId, $deptId, 'John', 'Doe', 'john@example.com', 'Sales Manager', 50000.00, 'active'),
            ($tenantId, $deptId, 'Jane', 'Smith', 'jane@example.com', 'Marketing Specialist', 45000.00, 'active')
        ");
        
        echo "Seeding complete.\n";
    }

} catch (Exception $e) {
    die("Error installing HR module: " . $e->getMessage() . "\n");
}

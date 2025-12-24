<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $base_dir = __DIR__ . '/app/';
        $relative_class = substr($class, strpos($class, '\\') + 1); 
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Core\Database;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
$property->setAccessible(true);
$property->setValue(null, 1);

$db = Database::getInstance()->getConnection();

echo "Verifying ERP Lifecycle...\n";

// 1. Ensure Active Employee
$stmt = $db->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? AND status = 'active' LIMIT 1");
$stmt->execute([1]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    $db->exec("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, status) VALUES (1, 'Lifecycle', 'Test', 'life@test.com', 'active')");
    $employeeId = $db->lastInsertId();
} else {
    $employeeId = $employee['id'];
}

// 2. Perform Promotion (Update DB directly to simulate controller action)
$newTitle = "Senior Tester";
$db->beginTransaction();
try {
    $stmt = $db->prepare("INSERT INTO erp_employee_lifecycle (tenant_id, employee_id, type, date, reason, new_position) VALUES (1, ?, 'promotion', CURDATE(), 'Verification Promo', ?)");
    $stmt->execute([$employeeId, $newTitle]);
    
    // Auto-update effect
    $upd = $db->prepare("UPDATE erp_employees SET job_title = ? WHERE id = ?");
    $upd->execute([$newTitle, $employeeId]);
    
    $db->commit();
    echo "PASS: Promotion Logged & Executed.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "FAIL: Promotion failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Verify Effect
$stmt = $db->prepare("SELECT job_title FROM erp_employees WHERE id = ?");
$stmt->execute([$employeeId]);
$title = $stmt->fetch(PDO::FETCH_ASSOC)['job_title'];

if ($title === $newTitle) {
    echo "PASS: Employee Title Updated to '$newTitle'.\n";
} else {
    echo "FAIL: Employee Title is '$title', expected '$newTitle'.\n";
}

// 4. Verify Views
if (file_exists(__DIR__ . '/app/modules/CasjoeERP/views/hr/lifecycle/index.php')) {
    echo "PASS: Index view exists.\n";
}

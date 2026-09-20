<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    // ... same autoloader ...
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;
use App\Modules\CasjoeERP\Controllers\DepartmentController;
use App\Modules\CasjoeERP\Controllers\EmployeeController;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');

$property->setValue(null, 1);

$step = $argv[1] ?? 'all';
$db = Database::getInstance()->getConnection();

if ($step === 'init' || $step === 'all') {
    $db->exec("DELETE FROM erp_employees WHERE email = 'test_emp@example.com'");
    $db->exec("DELETE FROM erp_departments WHERE name = 'Test Dept'");
    echo "Cleaned DB.\n";
    if ($step === 'init') exit;
}

if ($step === 'dept' || $step === 'all') {
    echo "Creating Department...\n";
    $_POST = ['name' => 'Test Dept', 'description' => 'A test department'];
    $deptController = new DepartmentController();
    $deptController->store(); // Will exit
}

if ($step === 'emp' || $step === 'all') {
    // Need dept ID
    $dept = $db->query("SELECT id FROM erp_departments WHERE name = 'Test Dept'")->fetch(PDO::FETCH_ASSOC);
    if (!$dept) die("Dept not found for emp creation.\n");
    
    echo "Creating Employee...\n";
    $_POST = [
        'first_name' => 'Test', 
        'last_name' => 'Employee', 
        'email' => 'test_emp@example.com',
        'department_id' => $dept['id'],
        'job_title' => 'Tester',
        'salary' => 60000,
        'status' => 'active',
        'hire_date' => date('Y-m-d')
    ];
    $empController = new EmployeeController();
    $empController->store(); // Will exit
}

if ($step === 'check') {
    echo "Checking Results...\n";
    $dept = $db->query("SELECT * FROM erp_departments WHERE name = 'Test Dept'")->fetch(PDO::FETCH_ASSOC);
    if ($dept) echo "PASS: Department created.\n";
    else echo "FAIL: Department missing.\n";
    
    $emp = $db->query("SELECT * FROM erp_employees WHERE email = 'test_emp@example.com'")->fetch(PDO::FETCH_ASSOC);
    if ($emp) echo "PASS: Employee created.\n";
    else echo "FAIL: Employee missing.\n";
}

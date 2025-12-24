<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;
use App\Modules\CasjoeERP\Controllers\DepartmentController;
use App\Modules\CasjoeERP\Controllers\EmployeeController;

function capture($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
}

$db = Database::getInstance()->getConnection();

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
$property->setAccessible(true);
$property->setValue(null, 1); // Set tenantId to 1

echo "Starting HR Verification...\n";

// Clear previous test data
$db->exec("DELETE FROM erp_employees WHERE email = 'test_emp@example.com'");
$db->exec("DELETE FROM erp_departments WHERE name = 'Test Dept'");

// 1. Create Department
echo "Creating Department...\n";
$_POST = ['name' => 'Test Dept', 'description' => 'A test department'];
// We need to suppress header redirect or mock it
// Since controller uses header(), we can't easily trap it without runkit or specialized tools.
// For this simple script, we will modify the controller behavior? No, that modifies code.
// We will just replicate the logic or rely on the fact that if it redirects, it finished?
// PHP CLI doesn't really redirect. It just sets a header. We can continue.

try {
    $deptController = new DepartmentController();
    $deptController->store(); 
} catch (Exception $e) { /* Ignore exit if store() exits */ }

// Check DB
$dept = $db->query("SELECT * FROM erp_departments WHERE name = 'Test Dept'")->fetch(PDO::FETCH_ASSOC);
if ($dept) {
    echo "PASS: Department 'Test Dept' created. ID: " . $dept['id'] . "\n";
} else {
    echo "FAIL: Department creation failed.\n";
    exit;
}

// 2. Create Employee
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

try {
    $empController = new EmployeeController();
    $empController->store();
} catch (Exception $e) { /* Ignore exit */ }

// Check DB
$emp = $db->query("SELECT * FROM erp_employees WHERE email = 'test_emp@example.com'")->fetch(PDO::FETCH_ASSOC);
if ($emp) {
    echo "PASS: Employee 'Test Employee' created. ID: " . $emp['id'] . "\n";
} else {
    echo "FAIL: Employee creation failed.\n";
}

// 3. Check List View
echo "Checking Employee List View...\n";
$output = capture(function() use ($empController) {
    $empController->index();
});

if (strpos($output, 'Test Employee') !== false) {
    echo "PASS: Employee found in list view.\n";
} else {
    echo "FAIL: Employee NOT found in list view.\n";
}

if (strpos($output, 'Test Dept') !== false) {
    echo "PASS: Department name found in list view.\n";
} else {
    echo "FAIL: Department name NOT found in list view.\n";
}

echo "HR Verification Finished.\n";

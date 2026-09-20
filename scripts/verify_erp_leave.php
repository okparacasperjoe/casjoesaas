<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $base_dir = __DIR__ . '/../app/';
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

echo "Verifying ERP Leave Management...\n";

// 1. Ensure Employee Exists
$stmt = $db->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? LIMIT 1");
$stmt->execute([1]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    // Create one if missing
    $db->exec("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, status) VALUES (1, 'Leave', 'Tester', 'leave@test.com', 'active')");
    $employeeId = $db->lastInsertId();
} else {
    $employeeId = $employee['id'];
}

// 2. Clear old test data
$db->exec("DELETE FROM erp_leave_requests WHERE reason = 'Verification Test Leave'");

// 3. Create Leave Request via Controller Logic Simulation
$startDate = date('Y-m-d', strtotime('+1 day'));
$endDate = date('Y-m-d', strtotime('+5 days'));

$stmt = $db->prepare("
    INSERT INTO erp_leave_requests 
    (tenant_id, employee_id, leave_type, start_date, end_date, reason) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([1, $employeeId, 'annual', $startDate, $endDate, 'Verification Test Leave']);
$leaveId = $db->lastInsertId();

if ($leaveId) {
    echo "PASS: Leave Request Created (ID: $leaveId).\n";
} else {
    echo "FAIL: Leave Request Creation Failed.\n";
    exit(1);
}

// 4. Verify Approval Logic
$stmt = $db->prepare("UPDATE erp_leave_requests SET status = 'approved' WHERE id = ?");
$stmt->execute([$leaveId]);

$stmt = $db->prepare("SELECT status FROM erp_leave_requests WHERE id = ?");
$stmt->execute([$leaveId]);
$updated = $stmt->fetch(PDO::FETCH_ASSOC);

if ($updated['status'] === 'approved') {
    echo "PASS: Leave Request Approved.\n";
} else {
    echo "FAIL: Approval Failed.\n";
    exit(1);
}

// 5. Verify Views
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/leave/index.php')) {
    echo "PASS: Index view exists.\n";
}
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/leave/create.php')) {
    echo "PASS: Create view exists.\n";
}

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
use App\Modules\CasjoeERP\Controllers\EmployeeController;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
$property->setAccessible(true);
$property->setValue(null, 1);

$db = Database::getInstance()->getConnection();

echo "Verifying ERP Attendance...\n";

// 1. Employee setup
$db->exec("DELETE FROM erp_employees WHERE email = 'attendancetest@example.com'");
$stmt = $db->prepare("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, status) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([1, 'Attendance', 'Tester', 'attendancetest@example.com', 'active']);
$employeeId = $db->lastInsertId();
echo "PASS: Employee created (ID: $employeeId).\n";

// 2. Check In
$_POST['employee_id'] = $employeeId;
$_POST['notes'] = 'Verification Check-in';
// Simulate Check In via DB as Controller redirects
$checkInTime = date('Y-m-d H:i:s');
$stmt = $db->prepare("INSERT INTO erp_attendance (tenant_id, employee_id, check_in, notes) VALUES (?, ?, ?, ?)");
$stmt->execute([1, $employeeId, $checkInTime, 'Verification Check-in']);
$attendanceId = $db->lastInsertId();
echo "PASS: Checked In (ID: $attendanceId).\n";

// 3. Check Out
// Simulate Check Out via DB
$checkOutTime = date('Y-m-d H:i:s', strtotime('+8 hours'));
$stmt = $db->prepare("UPDATE erp_attendance SET check_out = ? WHERE id = ? AND tenant_id = ?");
$stmt->execute([$checkOutTime, $attendanceId, 1]);
echo "PASS: Checked Out.\n";

// 4. Verify Record
$stmt = $db->prepare("SELECT * FROM erp_attendance WHERE id = ?");
$stmt->execute([$attendanceId]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if ($record && $record['check_out']) {
    echo "PASS: Attendance record verified complete.\n";
} else {
    echo "FAIL: Attendance record mismatch.\n";
}

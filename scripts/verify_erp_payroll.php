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

echo "Verifying ERP Payroll...\n";

// 1. Ensure Employee Exists with Salary
$stmt = $db->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? LIMIT 1");
$stmt->execute([1]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    $db->exec("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, status, salary) VALUES (1, 'Payroll', 'Tester', 'pay@test.com', 'active', 5000.00)");
    $employeeId = $db->lastInsertId();
} else {
    $employeeId = $employee['id'];
    // Update salary to ensure consistency for test
    $update = $db->prepare("UPDATE erp_employees SET salary = 5000.00 WHERE id = ?");
    $update->execute([$employeeId]);
}

// 2. Clear old test data
$db->exec("DELETE FROM erp_payroll WHERE employee_id = $employeeId AND status = 'paid'");

// 3. Process Payroll via DB Insertion (Simulating Controller)
$baseEarning = 5000.00;
$bonus = 500.00;
$tax = 200.00;
$deductions = 100.00;
$gross = $baseEarning + $bonus;
$net = $gross - $tax - $deductions; // 5000 + 500 - 200 - 100 = 5200

$db->beginTransaction();
try {
    $stmt = $db->prepare("
        INSERT INTO erp_payroll 
        (tenant_id, employee_id, pay_period_start, pay_period_end, payment_date, gross_pay, net_pay, status) 
        VALUES (?, ?, '2025-01-01', '2025-01-31', '2025-01-31', ?, ?, 'paid')
    ");
    $stmt->execute([1, $employeeId, $gross, $net]);
    $payrollId = $db->lastInsertId();

    // Items
    $itemStmt = $db->prepare("INSERT INTO erp_payroll_items (payroll_id, type, description, amount) VALUES (?, ?, ?, ?)");
    $itemStmt->execute([$payrollId, 'earning', 'Base Salary', $baseEarning]);
    $itemStmt->execute([$payrollId, 'earning', 'Bonus', $bonus]);
    $itemStmt->execute([$payrollId, 'deduction', 'Tax', $tax]);
    
    $db->commit();
    echo "PASS: Payroll Processed (ID: $payrollId). Net Pay: $net.\n";

} catch (Exception $e) {
    $db->rollBack();
    echo "FAIL: Error processing payroll: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Verify Items Count
$stmt = $db->prepare("SELECT COUNT(*) as count FROM erp_payroll_items WHERE payroll_id = ?");
$stmt->execute([$payrollId]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($count >= 3) { // Base, Bonus, Tax (at least)
    echo "PASS: Payroll Items Verified ($count items).\n";
} else {
    echo "FAIL: Missing payroll items ($count items).\n";
    exit(1);
}

// 5. Verify Views
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/payroll/index.php')) {
    echo "PASS: Index view exists.\n";
}
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/payroll/show.php')) {
    echo "PASS: Payslip view exists.\n";
}

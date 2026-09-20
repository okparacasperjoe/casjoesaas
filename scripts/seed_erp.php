<?php
require_once __DIR__ . '/../app/core/Database.php';
// Mock Tenant Context via Session or hardcode
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding ERP Data...\n";

// 1. Seed GL Accounts
$pdo->prepare("INSERT IGNORE INTO erp_gl_accounts (tenant_id, code, name, type, balance) VALUES 
(?, '1000', 'Cash', 'asset', 50000.00),
(?, '4000', 'Sales Revenue', 'income', 120000.00),
(?, '6000', 'Salaries Expense', 'expense', 45000.00)
")->execute([$tenantId, $tenantId, $tenantId]);

// 2. Seed Employees
$pdo->prepare("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, department, position, salary, hired_date, status) VALUES 
(?, 'Alice', 'Smith', 'alice@casjoe.com', 'Engineering', 'Senior Dev', 150000, '2023-01-15', 'active'),
(?, 'Bob', 'Jones', 'bob@casjoe.com', 'Sales', 'Account Manager', 90000, '2023-03-10', 'active')
")->execute([$tenantId, $tenantId]);

// 3. Seed Customers
$pdo->prepare("INSERT INTO erp_customers (tenant_id, name, email, phone, company, status) VALUES 
(?, 'Tech Solutions Ltd', 'contact@techsol.com', '080-1234-5678', 'Tech Solutions', 'customer'),
(?, 'Jane Doe', 'jane.doe@gmail.com', '090-9876-5432', 'Freelancer', 'lead')
")->execute([$tenantId, $tenantId]);

echo "ERP Core Data Seeding Complete.\n";

// 4. Extended Seeding for Self-Service Demo
$stmt = $pdo->prepare("SELECT id FROM erp_employees WHERE email = 'alice@casjoe.com' AND tenant_id = ?");
$stmt->execute([$tenantId]);
$aliceId = $stmt->fetchColumn();

if ($aliceId) {
    echo "Seeding Payroll & Leave for Alice...\n";

    // Payroll for last month
    $start = date('Y-m-01', strtotime('last month'));
    $end = date('Y-m-t', strtotime('last month'));
    $payDate = date('Y-m-25', strtotime('last month'));
    
    // Check if payroll exists
    $check = $pdo->prepare("SELECT id FROM erp_payroll WHERE employee_id = ? AND pay_period_start = ?");
    $check->execute([$aliceId, $start]);
    
    if (!$check->fetch()) {
        $pdo->prepare("
            INSERT INTO erp_payroll (tenant_id, employee_id, pay_period_start, pay_period_end, payment_date, gross_pay, net_pay, status) 
            VALUES (?, ?, ?, ?, ?, 150000, 135000, 'paid')
        ")->execute([$tenantId, $aliceId, $start, $end, $payDate]);
        $payrollId = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO erp_payroll_items (payroll_id, type, description, amount) VALUES (?, 'earning', 'Base Salary', 150000)")->execute([$payrollId]);
        $pdo->prepare("INSERT INTO erp_payroll_items (payroll_id, type, description, amount) VALUES (?, 'deduction', 'Tax (PAYE)', 15000)")->execute([$payrollId]);
    }

    // Leave Request
    $pdo->prepare("INSERT INTO erp_leave_requests (tenant_id, employee_id, leave_type, start_date, end_date, reason, status) VALUES 
    (?, ?, 'annual', ?, ?, 'Vacation', 'approved')
    ")->execute([$tenantId, $aliceId, date('Y-m-20'), date('Y-m-25')]);
}

echo "Full Demo Data Seeding Complete.\n";

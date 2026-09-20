<?php

require_once 'app/core/bootstrap.php';
use App\Core\TenantContext;
use App\Core\Database;

// Fetch Tenant 1 or creates if not exists logic simulation
// Assuming TenantContext has logic, but here we run CLI.
// We'll hardcode Tenant ID 1 for testing as per previous patterns.
$tenantId = 1;

$pdo = Database::getInstance()->getConnection();

echo "Seeding ERP Data for Tenant $tenantId...\n";

// 1. Seed Departments
echo "Seeding Departments...\n";
$depts = ['Human Resources', 'Finance', 'Engineering', 'Sales', 'Marketing'];
$deptIds = [];
$pdo->exec("DELETE FROM erp_departments WHERE tenant_id = $tenantId"); // Cleanup
$stmt = $pdo->prepare("INSERT INTO erp_departments (tenant_id, name) VALUES (?, ?)");
foreach ($depts as $d) {
    if (!$stmt->execute([$tenantId, $d])) {
        // Table might not exist yet? check install logs.
        echo "Error seeding department $d\n";
    }
}

// 2. Seed Employees
echo "Seeding Employees...\n";
$pdo->exec("DELETE FROM erp_employees WHERE tenant_id = $tenantId");
$employees = [
    ['John', 'Doe', 'john.doe@example.com', 'Engineering', 'Software Engineer', 85000],
    ['Jane', 'Smith', 'jane.smith@example.com', 'Human Resources', 'HR Manager', 70000],
    ['Robert', 'Brown', 'bob.brown@example.com', 'Sales', 'Sales Exec', 60000],
    ['Emily', 'White', 'emily.white@example.com', 'Finance', 'Accountant', 65000],
    ['Michael', 'Green', 'mike.green@example.com', 'Marketing', 'CMO', 95000],
];
$stmt = $pdo->prepare("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, department, position, salary, hired_date) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())");
foreach ($employees as $e) {
    try {
        $stmt->execute([$tenantId, $e[0], $e[1], $e[2], $e[3], $e[4], $e[5]]);
    } catch (PDOException $ex) {
        echo "Failed to insert employee " . $e[0] . ": " . $ex->getMessage() . "\n";
    }
}

// 3. Seed GL Accounts
echo "Seeding GL Accounts...\n";
$pdo->exec("DELETE FROM erp_gl_accounts WHERE tenant_id = $tenantId");
$accounts = [
    ['1000', 'Cash on Hand', 'asset'],
    ['1010', 'Bank Account', 'asset'],
    ['2000', 'Accounts Payable', 'liability'],
    ['4000', 'Sales Revenue', 'income'],
    ['5000', 'Office Expense', 'expense'],
];
$stmt = $pdo->prepare("INSERT INTO erp_gl_accounts (tenant_id, code, name, type) VALUES (?, ?, ?, ?)");
foreach ($accounts as $a) {
    $stmt->execute([$tenantId, $a[0], $a[1], $a[2]]);
}

// 4. Seed Products
echo "Seeding Products...\n";
$pdo->exec("DELETE FROM erp_products WHERE tenant_id = $tenantId");
$products = [
    ['Macbook Pro M1', 'TECH-001', 1200.00, 950.00, 10],
    ['Office Chair', 'OFF-002', 150.00, 80.00, 50],
    ['Consulting Hour', 'SVC-001', 100.00, 0.00, 999],
];
$stmt = $pdo->prepare("INSERT INTO erp_products (tenant_id, name, sku, price, cost, stock_level) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($products as $p) {
    $stmt->execute([$tenantId, $p[0], $p[1], $p[2], $p[3], $p[4]]);
}

// 5. Seed Customers
echo "Seeding Customers...\n";
$pdo->exec("DELETE FROM erp_customers WHERE tenant_id = $tenantId");
$customers = [
    ['Acme Corp', 'contact@acme.com', 'customer'],
    ['Globex Corp', 'hank@globex.com', 'customer'],
    ['Soylent Corp', 'admin@soylent.com', 'lead'],
];
$stmt = $pdo->prepare("INSERT INTO erp_customers (tenant_id, name, email, status) VALUES (?, ?, ?, ?)");
foreach ($customers as $c) {
    $stmt->execute([$tenantId, $c[0], $c[1], $c[2]]);
}

echo "ERP Seeding Complete!\n";

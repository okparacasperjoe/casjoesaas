<?php
require_once __DIR__ . '/app/core/Database.php';
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

echo "ERP Seeding Complete.\n";

<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class EmployeeController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function checkHrPermission()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin' && $role !== 'hr') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    public function index()
    {
        $this->checkHrPermission();
        $stmt = $this->pdo->prepare("
            SELECT e.*, d.name as department_name 
            FROM erp_employees e 
            LEFT JOIN erp_departments d ON e.department_id = d.id 
            WHERE e.tenant_id = ? 
            ORDER BY e.last_name ASC
        ");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/employees/index.php';
    }

    public function create()
    {
        $this->checkHrPermission();
        // Fetch departments for dropdown
        $stmt = $this->pdo->prepare("SELECT id, name FROM erp_departments WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/employees/create.php';
    }

    public function store()
    {
        $this->checkHrPermission();
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $deptId = $_POST['department_id'] ?? null;
        $jobTitle = $_POST['job_title'] ?? '';
        $salary = $_POST['salary'] ?? 0;
        $status = $_POST['status'] ?? 'active';
        $hireDate = $_POST['hire_date'] ?? date('Y-m-d');

        if (empty($firstName) || empty($lastName) || empty($email)) {
            die("Required fields missing");
        }

        // Create Employee Record
        $stmt = $this->pdo->prepare("
            INSERT INTO erp_employees 
            (tenant_id, department_id, first_name, last_name, email, job_title, salary, status, hire_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $this->tenantId, 
            $deptId ?: null, 
            $firstName, 
            $lastName, 
            $email, 
            $jobTitle, 
            $salary, 
            $status, 
            $hireDate
        ]);

        // Create User Account if requested
        if (isset($_POST['create_login']) && $_POST['create_login'] == '1') {
            $password = $_POST['password'] ?? '';
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $fullName = trim("$firstName $lastName");
                
                // Check if user exists
                $check = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND tenant_id = ?");
                $check->execute([$email, $this->tenantId]);
                
                if (!$check->fetch()) {
                    $role = $_POST['role'] ?? 'user';
                    // Validate role to prevent arbitrary injection if needed, though enum/app logic usually handles it.
                    if (!in_array($role, ['user', 'hr', 'admin'])) $role = 'user';

                    $uStmt = $this->pdo->prepare("
                        INSERT INTO users (tenant_id, name, email, password, role, is_verified, created_at) 
                        VALUES (?, ?, ?, ?, ?, 1, NOW())
                    ");
                    $uStmt->execute([$this->tenantId, $fullName, $email, $hash, $role]);
                }
            }
        }

        header('Location: /erp/hr'); // Redirect to employees list
        exit;
    }
    public function attendance()
    {
        $this->checkHrPermission();
        $stmt = $this->pdo->prepare("
            SELECT a.*, e.first_name, e.last_name 
            FROM erp_attendance a 
            JOIN erp_employees e ON a.employee_id = e.id 
            WHERE a.tenant_id = ? 
            ORDER BY a.check_in DESC
        ");
        $stmt->execute([$this->tenantId]);
        $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get employees for manual check-in if needed
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/attendance/index.php';
    }

    public function checkIn()
    {
        $this->checkHrPermission();
        $employeeId = $_POST['employee_id'];
        $notes = $_POST['notes'] ?? '';
        $checkInTime = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("INSERT INTO erp_attendance (tenant_id, employee_id, check_in, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $employeeId, $checkInTime, $notes]);

        header('Location: /erp/attendance');
        exit;
    }

    public function checkOut()
    {
        $this->checkHrPermission();
        $id = $_POST['id'];
        $checkOutTime = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("UPDATE erp_attendance SET check_out = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$checkOutTime, $id, $this->tenantId]);

        header('Location: /erp/attendance');
        exit;
    }
    public function delete()
    {
        $this->checkHrPermission();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /erp/hr');
            exit;
        }

        // Get Employee Email to delete associated User
        $stmt = $this->pdo->prepare("SELECT email FROM erp_employees WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($emp) {
            // Delete User Account if exists
            $uStmt = $this->pdo->prepare("DELETE FROM users WHERE email = ? AND tenant_id = ?");
            $uStmt->execute([$emp['email'], $this->tenantId]);

            // Delete Employee Record
            $delStmt = $this->pdo->prepare("DELETE FROM erp_employees WHERE id = ? AND tenant_id = ?");
            $delStmt->execute([$id, $this->tenantId]);
        }

        header('Location: /erp/hr');
        exit;
    }
}

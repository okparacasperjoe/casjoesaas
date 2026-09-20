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
        $this->ensureAccessibleModulesColumn();
    }

    private function ensureAccessibleModulesColumn()
    {
        try {
            $check1 = $this->pdo->query("SHOW COLUMNS FROM erp_employees LIKE 'accessible_modules'");
            if (!$check1->fetch()) {
                $this->pdo->exec("ALTER TABLE erp_employees ADD COLUMN accessible_modules TEXT NULL");
            }
            $check2 = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'accessible_modules'");
            if (!$check2->fetch()) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN accessible_modules TEXT NULL");
            }
        } catch (\Exception $e) {
            // Ignore if already exists or restricted
        }
    }

    private function getAvailableModules()
    {
        return [
            ['slug' => 'hr', 'name' => 'Human Resources (Attendance, Leave, Payroll, Recruitment, SOPs)'],
            ['slug' => 'crm', 'name' => 'CRM & Sales (Clients, Leads, Pipeline, Sales, Booking Scheduler, WhatsApp)'],
            ['slug' => 'projects', 'name' => 'Projects & Tasks (Projects, Kanban Board, Timesheets, Calendar)'],
            ['slug' => 'finance', 'name' => 'Finance & Assets (Estimates, Invoices, Expenses, Vendors, Stock, Wallet)'],
            ['slug' => 'admin', 'name' => 'System Administration (Team Chat, Announcements, Users, Settings)']
        ];
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

        // Fetch departments for inline modal
        $deptStmt = $this->pdo->prepare("SELECT id, name FROM erp_departments WHERE tenant_id = ? ORDER BY name ASC");
        $deptStmt->execute([$this->tenantId]);
        $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/employees/index.php';
    }

    public function create()
    {
        $this->checkHrPermission();
        // Fetch departments for dropdown
        $stmt = $this->pdo->prepare("SELECT id, name FROM erp_departments WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $modules = $this->getAvailableModules();

        require __DIR__ . '/../Views/hr/employees/create.php';
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

        $modulesSelected = $_POST['accessible_modules'] ?? $_POST['module_access'] ?? [];
        $modulesJson = !empty($modulesSelected) && is_array($modulesSelected) ? json_encode(array_values($modulesSelected)) : null;

        if (empty($firstName) || empty($lastName) || empty($email)) {
            die("Required fields missing");
        }

        // Create Employee Record
        $stmt = $this->pdo->prepare("
            INSERT INTO erp_employees 
            (tenant_id, department_id, first_name, last_name, email, job_title, salary, status, hire_date, accessible_modules) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            $hireDate,
            $modulesJson
        ]);

        // Create User Account if requested
        $accountCreated = false;
        $plainPassword = '';
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
                    if (!in_array($role, ['user', 'hr', 'admin'])) $role = 'user';

                    $uStmt = $this->pdo->prepare("
                        INSERT INTO users (tenant_id, name, email, password, role, is_verified, created_at, accessible_modules) 
                        VALUES (?, ?, ?, ?, ?, 1, NOW(), ?)
                    ");
                    $uStmt->execute([$this->tenantId, $fullName, $email, $hash, $role, $modulesJson]);
                    $accountCreated = true;
                    $plainPassword = $password;
                }
            }
        }

        // Send Email Notification
        try {
            $subject = "Welcome to Casjoe ERP";
            $message = "<h3>Hello $firstName $lastName,</h3>";
            $message .= "<p>You have been added to the Casjoe ERP system as a staff member.</p>";
            $message .= "<p><strong>Job Title:</strong> " . htmlspecialchars($jobTitle) . "</p>";
            
            if ($accountCreated) {
                $loginUrl = "https://" . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com') . "/login";
                $message .= "<p>An account has been created for you to access the portal. You can log in at <a href='" . htmlspecialchars($loginUrl) . "'>" . htmlspecialchars($loginUrl) . "</a> using the credentials below:</p>";
                $message .= "<ul>";
                $message .= "<li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>";
                $message .= "<li><strong>Password:</strong> " . htmlspecialchars($plainPassword) . "</li>";
                $message .= "</ul>";
                $message .= "<p>Please ensure you change your password after logging in.</p>";
            }
            
            $message .= "<p>Best Regards,<br>Casjoe Admin</p>";

            \App\Core\Mailer::send($email, $subject, $message, false);
        } catch (\Exception $e) {
            // Log email sending error but do not block employee creation
            error_log("Failed to send staff welcome email: " . $e->getMessage());
        }

        header('Location: /erp/hr'); // Redirect to employees list
        exit;
    }

    public function edit()
    {
        $this->checkHrPermission();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /erp/hr');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_employees WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$employee) {
            header('Location: /erp/hr');
            exit;
        }

        $deptStmt = $this->pdo->prepare("SELECT id, name FROM erp_departments WHERE tenant_id = ? ORDER BY name ASC");
        $deptStmt->execute([$this->tenantId]);
        $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

        $modules = $this->getAvailableModules();

        // Check if there is a corresponding user login account
        $uStmt = $this->pdo->prepare("SELECT id, role, accessible_modules FROM users WHERE email = ? AND tenant_id = ?");
        $uStmt->execute([$employee['email'], $this->tenantId]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        $userModules = [];
        $rawModules = $user['accessible_modules'] ?? $employee['accessible_modules'] ?? null;
        if (!empty($rawModules)) {
            $decoded = is_string($rawModules) ? json_decode($rawModules, true) : $rawModules;
            if (is_array($decoded)) {
                $userModules = $decoded;
            } elseif (is_string($rawModules)) {
                $userModules = explode(',', $rawModules);
            }
        }

        require __DIR__ . '/../Views/hr/employees/edit.php';
    }

    public function update()
    {
        $this->checkHrPermission();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /erp/hr');
            exit;
        }

        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $deptId = $_POST['department_id'] ?? null;
        $jobTitle = $_POST['job_title'] ?? '';
        $salary = $_POST['salary'] ?? 0;
        $status = $_POST['status'] ?? 'active';
        $hireDate = $_POST['hire_date'] ?? date('Y-m-d');

        $modulesSelected = $_POST['accessible_modules'] ?? $_POST['module_access'] ?? [];
        $modulesJson = !empty($modulesSelected) && is_array($modulesSelected) ? json_encode(array_values($modulesSelected)) : null;

        // Get old email before update
        $oldStmt = $this->pdo->prepare("SELECT email FROM erp_employees WHERE id = ? AND tenant_id = ?");
        $oldStmt->execute([$id, $this->tenantId]);
        $oldEmp = $oldStmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("
            UPDATE erp_employees 
            SET department_id = ?, first_name = ?, last_name = ?, email = ?, job_title = ?, salary = ?, status = ?, hire_date = ?, accessible_modules = ? 
            WHERE id = ? AND tenant_id = ?
        ");
        $stmt->execute([
            $deptId ?: null, 
            $firstName, 
            $lastName, 
            $email, 
            $jobTitle, 
            $salary, 
            $status, 
            $hireDate, 
            $modulesJson, 
            $id, 
            $this->tenantId
        ]);

        if ($oldEmp) {
            $newRole = $_POST['role'] ?? null;
            $updateUserSql = "UPDATE users SET name = ?, email = ?, accessible_modules = ?";
            $params = [trim("$firstName $lastName"), $email, $modulesJson];
            if ($newRole && in_array($newRole, ['user', 'hr', 'admin'])) {
                $updateUserSql .= ", role = ?";
                $params[] = $newRole;
            }
            $updateUserSql .= " WHERE email = ? AND tenant_id = ?";
            $params[] = $oldEmp['email'];
            $params[] = $this->tenantId;

            $uStmt = $this->pdo->prepare($updateUserSql);
            $uStmt->execute($params);
        }

        header('Location: /erp/hr');
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

        require __DIR__ . '/../Views/hr/attendance/index.php';
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

    public function profile()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            header("Location: /erp/employees/edit?id=" . urlencode($id));
        } else {
            header("Location: /erp/my-portal/profile");
        }
        exit;
    }

    public function family()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            header("Location: /erp/employees/edit?id=" . urlencode($id));
        } else {
            header("Location: /erp/employees");
        }
        exit;
    }

    public function bank()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            header("Location: /erp/employees/edit?id=" . urlencode($id));
        } else {
            header("Location: /erp/employees");
        }
        exit;
    }

    public function documents()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            header("Location: /erp/employees/edit?id=" . urlencode($id));
        } else {
            header("Location: /erp/documents");
        }
        exit;
    }

    public function assets()
    {
        header("Location: /erp/assets");
        exit;
    }

    public function attendanceReport()
    {
        $this->checkHrPermission();
        $month = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        
        $stmt = $this->pdo->prepare("
            SELECT a.*, e.first_name, e.last_name, e.job_title, d.name as department_name 
            FROM erp_attendance a 
            JOIN erp_employees e ON a.employee_id = e.id 
            LEFT JOIN erp_departments d ON e.department_id = d.id
            WHERE a.tenant_id = ? AND a.check_in LIKE ?
            ORDER BY a.check_in DESC
        ");
        $stmt->execute([$this->tenantId, $month . '%']);
        $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group into chart data by day of month
        $daysCount = [];
        foreach ($attendance as $att) {
            $day = date('j', strtotime($att['check_in']));
            $daysCount[$day] = ($daysCount[$day] ?? 0) + 1;
        }
        $chartLabels = array_keys($daysCount);
        $chartData = array_values($daysCount);

        require __DIR__ . '/../Views/hr/attendance/report.php';
    }
}

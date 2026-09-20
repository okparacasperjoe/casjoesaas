<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class SelfServiceController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        
        $user = Auth::user();
        if ($user && !empty($user['tenant_id'])) {
            $this->tenantId = $user['tenant_id'];
        } else {
            $this->tenantId = TenantContext::getTenantId();
        }
    }

    private function getEmployee()
    {
        $user = Auth::user();
        $email = $user['email'] ?? '';

        // Try exact match by email
        $stmt = $this->pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? AND email = ? LIMIT 1");
        $stmt->execute([$this->tenantId, $email]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            // Fallback to any employee for this tenant (Demo purposes)
            $stmt = $this->pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? LIMIT 1");
            $stmt->execute([$this->tenantId]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $employee;
    }

    public function dashboard()
    {
        $employee = $this->getEmployee();

        if (!$employee) {
            $employee = ['first_name' => 'Guest', 'last_name' => 'User', 'email' => 'No Email Linked', 'position' => 'N/A', 'id' => null];
            $attendance = [];
            $leaves = [];
            $payslips = [];
            $activeAttendance = false;
            require __DIR__ . '/../Views/hr/self_service/dashboard.php';
            return;
        }

        $stmtAtt = $this->pdo->prepare("SELECT * FROM erp_attendance WHERE employee_id = ? ORDER BY check_in DESC LIMIT 5");
        $stmtAtt->execute([$employee['id']]);
        $attendance = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);

        $stmtLeave = $this->pdo->prepare("SELECT * FROM erp_leave_requests WHERE employee_id = ? AND start_date >= CURDATE() ORDER BY start_date ASC");
        $stmtLeave->execute([$employee['id']]);
        $leaves = $stmtLeave->fetchAll(PDO::FETCH_ASSOC);

        $stmtPay = $this->pdo->prepare("SELECT * FROM erp_payroll WHERE employee_id = ? ORDER BY pay_period_end DESC LIMIT 5");
        $stmtPay->execute([$employee['id']]);
        $payslips = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

        $stmtActive = $this->pdo->prepare("SELECT * FROM erp_attendance WHERE employee_id = ? AND DATE(check_in) = CURDATE() AND check_out IS NULL ORDER BY check_in DESC LIMIT 1");
        $stmtActive->execute([$employee['id']]);
        $activeAttendance = $stmtActive->fetch(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/self_service/dashboard.php';
    }

    public function checkIn()
    {
        $employee = $this->getEmployee();

        if ($employee) {
            $checkInTime = date('Y-m-d H:i:s');
            $stmtInsert = $this->pdo->prepare("INSERT INTO erp_attendance (tenant_id, employee_id, check_in, notes) VALUES (?, ?, ?, 'Self Check-in')");
            $stmtInsert->execute([$this->tenantId, $employee['id'], $checkInTime]);
        }
        header('Location: /erp/my-portal');
        exit;
    }

    public function checkOut()
    {
        $employee = $this->getEmployee();

        if ($employee) {
            $checkOutTime = date('Y-m-d H:i:s');
            $stmtUpdate = $this->pdo->prepare("UPDATE erp_attendance SET check_out = ? WHERE employee_id = ? AND check_out IS NULL AND DATE(check_in) = CURDATE() ORDER BY check_in DESC LIMIT 1");
            $stmtUpdate->execute([$checkOutTime, $employee['id']]);
        }
        header('Location: /erp/my-portal');
        exit;
    }

    public function requestLeave()
    {
        $employee = $this->getEmployee();
        require __DIR__ . '/../Views/hr/self_service/request_leave.php';
    }

    public function profile()
    {
        $employee = $this->getEmployee();
        require __DIR__ . '/../Views/hr/self_service/profile.php';
    }

    public function updateProfile()
    {
        $employee = $this->getEmployee();
        if ($employee) {
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $emergencyContact = trim($_POST['emergency_contact'] ?? '');
            $bankName = trim($_POST['bank_name'] ?? '');
            $bankAccountNumber = trim($_POST['bank_account_number'] ?? '');
            $bankAccountName = trim($_POST['bank_account_name'] ?? '');

            $stmt = $this->pdo->prepare("
                UPDATE erp_employees 
                SET phone = ?, address = ?, emergency_contact = ?, bank_name = ?, bank_account_number = ?, bank_account_name = ? 
                WHERE id = ?
            ");
            $stmt->execute([$phone, $address, $emergencyContact, $bankName, $bankAccountNumber, $bankAccountName, $employee['id']]);
        }

        header('Location: /erp/my-portal/profile?saved=1');
        exit;
    }

    public function sops()
    {
        $employee = $this->getEmployee();
        $assignments = [];
        if ($employee && !empty($employee['id'])) {
            $stmt = $this->pdo->prepare("
                SELECT a.*, s.title, s.content, s.created_at as sop_created_at
                FROM erp_sop_assignments a
                JOIN erp_sops s ON a.sop_id = s.id
                WHERE a.employee_id = ? AND a.tenant_id = ?
                ORDER BY a.status ASC, a.created_at DESC
            ");
            $stmt->execute([$employee['id'], $this->tenantId]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        require __DIR__ . '/../Views/hr/self_service/sops.php';
    }

    public function readSop($id = null)
    {
        $assignmentId = $id ?? ($_GET['id'] ?? null);
        $employee = $this->getEmployee();
        if (!$employee || !$assignmentId) {
            header('Location: /erp/my-portal/sops');
            exit;
        }

        $stmt = $this->pdo->prepare("
            SELECT a.*, s.title, s.content 
            FROM erp_sop_assignments a
            JOIN erp_sops s ON a.sop_id = s.id
            WHERE a.id = ? AND a.employee_id = ? AND a.tenant_id = ?
        ");
        $stmt->execute([$assignmentId, $employee['id'], $this->tenantId]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$assignment) {
            header('Location: /erp/my-portal/sops');
            exit;
        }

        require __DIR__ . '/../Views/hr/self_service/sop_read.php';
    }

    public function signSop()
    {
        $employee = $this->getEmployee();
        $assignmentId = $_POST['assignment_id'] ?? null;
        $signedName = trim($_POST['signed_name'] ?? '');
        $signature = $_POST['signature'] ?? '';

        if ($employee && $assignmentId) {
            $stmt = $this->pdo->prepare("
                UPDATE erp_sop_assignments 
                SET status = 'signed', signature = ?, signed_name = ?, signed_at = NOW() 
                WHERE id = ? AND employee_id = ? AND tenant_id = ?
            ");
            $stmt->execute([$signature, $signedName, $assignmentId, $employee['id'], $this->tenantId]);
        }

        header('Location: /erp/my-portal/sops?status=signed');
        exit;
    }
}

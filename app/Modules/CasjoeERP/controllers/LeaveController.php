<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class LeaveController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, e.first_name, e.last_name 
            FROM erp_leave_requests l
            JOIN erp_employees e ON l.employee_id = e.id
            WHERE l.tenant_id = ?
            ORDER BY l.created_at DESC
        ");
        $stmt->execute([$this->tenantId]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/leave/index.php';
    }

    public function create()
    {
        // Get employees for dropdown (admin perspective request or self-service if user context was fully implemented)
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/leave/create.php';
    }

    public function store()
    {
        $employeeId = $_POST['employee_id'];
        $type = $_POST['leave_type'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $reason = $_POST['reason'];

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_leave_requests 
            (tenant_id, employee_id, leave_type, start_date, end_date, reason) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$this->tenantId, $employeeId, $type, $startDate, $endDate, $reason]);

        // Send email to HR/Admin
        $empStmt = $this->pdo->prepare("SELECT first_name, last_name, email FROM erp_employees WHERE id = ?");
        $empStmt->execute([$employeeId]);
        $emp = $empStmt->fetch(PDO::FETCH_ASSOC);
        
        $adminStmt = $this->pdo->prepare("SELECT email FROM users WHERE tenant_id = ? AND role IN ('admin', 'hr')");
        $adminStmt->execute([$this->tenantId]);
        $admins = $adminStmt->fetchAll(PDO::FETCH_COLUMN);

        if ($emp && $admins) {
            $subject = "New Leave Request: {$emp['first_name']} {$emp['last_name']}";
            $message = "<p><strong>{$emp['first_name']} {$emp['last_name']}</strong> has submitted a new leave request.</p>";
            $message .= "<p><strong>Type:</strong> " . htmlspecialchars($type) . "<br>";
            $message .= "<strong>Dates:</strong> $startDate to $endDate<br>";
            $message .= "<strong>Reason:</strong> " . nl2br(htmlspecialchars($reason)) . "</p>";
            $message .= "<p>Please log in to the ERP to approve or reject this request.</p>";
            
            foreach ($admins as $adminEmail) {
                try {
                    \App\Core\Mailer::send($adminEmail, $subject, $message, false);
                } catch (\Exception $e) {
                    error_log("Leave request email failed: " . $e->getMessage());
                }
            }
        }

        header('Location: /erp/leave');
        exit;
    }

    public function approve()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("UPDATE erp_leave_requests SET status = 'approved' WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        // Notify Employee
        $reqStmt = $this->pdo->prepare("
            SELECT l.leave_type, l.start_date, l.end_date, e.email, e.first_name 
            FROM erp_leave_requests l
            JOIN erp_employees e ON l.employee_id = e.id
            WHERE l.id = ?
        ");
        $reqStmt->execute([$id]);
        $req = $reqStmt->fetch(PDO::FETCH_ASSOC);

        if ($req && $req['email']) {
            $subject = "Leave Request Approved";
            $message = "<p>Hello {$req['first_name']},</p>";
            $message .= "<p>Your <strong>{$req['leave_type']}</strong> request from {$req['start_date']} to {$req['end_date']} has been <strong>approved</strong>.</p>";
            try {
                \App\Core\Mailer::send($req['email'], $subject, $message, false);
            } catch (\Exception $e) {
                error_log("Leave approval email failed: " . $e->getMessage());
            }
        }

        header('Location: /erp/leave');
        exit;
    }

    public function reject()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("UPDATE erp_leave_requests SET status = 'rejected' WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        // Notify Employee
        $reqStmt = $this->pdo->prepare("
            SELECT l.leave_type, l.start_date, l.end_date, e.email, e.first_name 
            FROM erp_leave_requests l
            JOIN erp_employees e ON l.employee_id = e.id
            WHERE l.id = ?
        ");
        $reqStmt->execute([$id]);
        $req = $reqStmt->fetch(PDO::FETCH_ASSOC);

        if ($req && $req['email']) {
            $subject = "Leave Request Rejected";
            $message = "<p>Hello {$req['first_name']},</p>";
            $message .= "<p>Your <strong>{$req['leave_type']}</strong> request from {$req['start_date']} to {$req['end_date']} has been <strong>rejected</strong>.</p>";
            try {
                \App\Core\Mailer::send($req['email'], $subject, $message, false);
            } catch (\Exception $e) {
                error_log("Leave rejection email failed: " . $e->getMessage());
            }
        }

        header('Location: /erp/leave');
        exit;
    }


    public function delete() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_hr_leaves WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/hr/leaves');
        exit;
    }
}

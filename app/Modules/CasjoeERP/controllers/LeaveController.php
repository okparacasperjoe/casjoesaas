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

        require __DIR__ . '/../views/hr/leave/index.php';
    }

    public function create()
    {
        // Get employees for dropdown (admin perspective request or self-service if user context was fully implemented)
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/leave/create.php';
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

        header('Location: /erp/leave');
        exit;
    }

    public function approve()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("UPDATE erp_leave_requests SET status = 'approved' WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/leave');
        exit;
    }

    public function reject()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("UPDATE erp_leave_requests SET status = 'rejected' WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/leave');
        exit;
    }
}

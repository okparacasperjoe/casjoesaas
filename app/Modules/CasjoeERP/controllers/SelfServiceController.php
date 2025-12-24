<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class SelfServiceController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function dashboard()
    {
        // Mock: Get current logged in employee. 
        // In real app, we would get this from session. 
        // For demo, we just pick the first employee or the one with ID 1.
        $stmt = $this->pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? LIMIT 1");
        $stmt->execute([$this->tenantId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            echo "No employee record found for your account.";
            return;
        }

        // Fetch recent attendance
        $stmtAtt = $this->pdo->prepare("SELECT * FROM erp_attendance WHERE employee_id = ? ORDER BY check_in DESC LIMIT 5");
        $stmtAtt->execute([$employee['id']]);
        $attendance = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch upcoming time off
        $stmtLeave = $this->pdo->prepare("SELECT * FROM erp_leave_requests WHERE employee_id = ? AND start_date >= CURDATE() ORDER BY start_date ASC");
        $stmtLeave->execute([$employee['id']]);
        $leaves = $stmtLeave->fetchAll(PDO::FETCH_ASSOC);

        // Fetch payslips
        $stmtPay = $this->pdo->prepare("SELECT * FROM erp_payroll WHERE employee_id = ? ORDER BY pay_period_end DESC LIMIT 5");
        $stmtPay->execute([$employee['id']]);
        $payslips = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/self_service/dashboard.php';
    }
}

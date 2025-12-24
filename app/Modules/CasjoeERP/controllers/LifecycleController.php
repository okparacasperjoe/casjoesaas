<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class LifecycleController
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
            FROM erp_employee_lifecycle l
            JOIN erp_employees e ON l.employee_id = e.id
            WHERE l.tenant_id = ?
            ORDER BY l.date DESC
        ");
        $stmt->execute([$this->tenantId]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/lifecycle/index.php';
    }

    public function create()
    {
        // Get employees
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/hr/lifecycle/create.php';
    }

    public function store()
    {
        $employeeId = $_POST['employee_id'];
        $type = $_POST['type'];
        $date = $_POST['date'];
        $reason = $_POST['reason'];
        $newPosition = $_POST['new_position'] ?? null;

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_employee_lifecycle 
                (tenant_id, employee_id, type, date, reason, new_position) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$this->tenantId, $employeeId, $type, $date, $reason, $newPosition]);

            // Update Employee Status if Resignation/Termination
            if ($type === 'resignation' || $type === 'termination') {
                $upd = $this->pdo->prepare("UPDATE erp_employees SET status = 'inactive' WHERE id = ?");
                $upd->execute([$employeeId]);
            }
            // Update Job Title if Promotion
            if ($type === 'promotion' && $newPosition) {
                $upd = $this->pdo->prepare("UPDATE erp_employees SET job_title = ? WHERE id = ?");
                $upd->execute([$newPosition, $employeeId]);
            }

            $this->pdo->commit();
            header('Location: /erp/lifecycle');
            exit;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Error processing lifecycle event: " . $e->getMessage());
        }
    }
}

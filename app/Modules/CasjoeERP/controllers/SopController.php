<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class SopController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        
        \App\Modules\CasjoeERP\Helpers\TimezoneHelper::applyTenantTimezone($this->pdo, $this->tenantId);
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_sops WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $sops = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/sops/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/hr/sops/create.php';
    }

    public function store()
    {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        if (empty($title) || empty($content)) {
            die("Title and Content are required.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO erp_sops (tenant_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $content]);

        header("Location: /erp/sops?status=created");
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die("ID missing");

        $stmt = $this->pdo->prepare("SELECT * FROM erp_sops WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $sop = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sop) die("SOP not found");

        require __DIR__ . '/../Views/hr/sops/create.php';
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        if (!$id || empty($title) || empty($content)) {
            die("Invalid data");
        }

        $stmt = $this->pdo->prepare("UPDATE erp_sops SET title = ?, content = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$title, $content, $id, $this->tenantId]);

        header("Location: /erp/sops?status=updated");
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM erp_sops WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$id, $this->tenantId]);
        }
        header("Location: /erp/sops?status=deleted");
        exit;
    }

    public function assign()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die("ID missing");

        $stmt = $this->pdo->prepare("SELECT * FROM erp_sops WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $sop = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sop) die("SOP not found");

        // Fetch employees
        $stmtEmp = $this->pdo->prepare("SELECT id, first_name, last_name, email FROM erp_employees WHERE tenant_id = ? AND status = 'active' ORDER BY first_name ASC");
        $stmtEmp->execute([$this->tenantId]);
        $employees = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

        // Fetch departments
        $stmtDept = $this->pdo->prepare("SELECT id, name FROM erp_departments WHERE tenant_id = ?");
        $stmtDept->execute([$this->tenantId]);
        $departments = $stmtDept->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/sops/assign.php';
    }

    public function storeAssignment()
    {
        $sop_id = $_POST['sop_id'] ?? null;
        $department_id = $_POST['department_id'] ?? null;
        $employee_ids = $_POST['employee_ids'] ?? [];

        if (!$sop_id) die("SOP ID missing");

        $employeesToAssign = [];

        if (!empty($department_id)) {
            $stmt = $this->pdo->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? AND department_id = ? AND status = 'active'");
            $stmt->execute([$this->tenantId, $department_id]);
            $employeesToAssign = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $employeesToAssign = $employee_ids;
        }

        if (empty($employeesToAssign)) {
            header("Location: /erp/sops/assign?id=$sop_id&error=no_employees");
            exit;
        }

        $stmtSop = $this->pdo->prepare("SELECT title FROM erp_sops WHERE id = ? AND tenant_id = ?");
        $stmtSop->execute([$sop_id, $this->tenantId]);
        $sopTitle = $stmtSop->fetchColumn();

        foreach ($employeesToAssign as $emp_id) {
            // Check if already assigned
            $check = $this->pdo->prepare("SELECT id FROM erp_sop_assignments WHERE tenant_id = ? AND sop_id = ? AND employee_id = ?");
            $check->execute([$this->tenantId, $sop_id, $emp_id]);
            if (!$check->fetch()) {
                $insert = $this->pdo->prepare("INSERT INTO erp_sop_assignments (tenant_id, sop_id, employee_id, status) VALUES (?, ?, ?, 'assigned')");
                $insert->execute([$this->tenantId, $sop_id, $emp_id]);
                
                // Get employee info
                $stmtEmp = $this->pdo->prepare("SELECT first_name, email FROM erp_employees WHERE id = ?");
                $stmtEmp->execute([$emp_id]);
                $emp = $stmtEmp->fetch(\PDO::FETCH_ASSOC);
                
                // Send email
                if ($emp && $emp['email']) {
                    $host = $_SERVER['HTTP_HOST'] ?? 'app.casjoe.com';
                    $subject = "New Document Assigned: " . $sopTitle;
                    $message = "<p>Hi {$emp['first_name']},</p>";
                    $message .= "<p>A new document (<strong>{$sopTitle}</strong>) has been assigned to you. Please log in to your portal to read and sign it.</p>";
                    $message .= "<p><a href='https://{$host}/erp/my-portal/sops' style='display:inline-block;padding:10px 20px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:5px;'>View Documents</a></p>";
                    \App\Core\Mailer::send($emp['email'], $subject, $message);
                }
            }
        }

        header("Location: /erp/sops/tracking?id=$sop_id&status=assigned");
        exit;
    }

    public function tracking()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die("ID missing");

        $stmt = $this->pdo->prepare("SELECT * FROM erp_sops WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $sop = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sop) die("SOP not found");

        $stmtTracks = $this->pdo->prepare("
            SELECT a.*, e.first_name, e.last_name, e.email 
            FROM erp_sop_assignments a
            JOIN erp_employees e ON a.employee_id = e.id
            WHERE a.sop_id = ? AND a.tenant_id = ?
            ORDER BY a.status ASC, e.first_name ASC
        ");
        $stmtTracks->execute([$id, $this->tenantId]);
        $assignments = $stmtTracks->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/sops/tracking.php';
    }
}

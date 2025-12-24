<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\TenantContext;
use PDO;

class ClientPortalController
{
    private $pdo;
    private $tenantId;
    private $user;
    private $customerId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        $this->user = Auth::user();

        if (!$this->user || $this->user['role'] !== 'client') {
            header('Location: /login');
            exit;
        }

        // Find linked customer record
        // Assuming 'email' links User to Customer
        $stmt = $this->pdo->prepare("SELECT id, name FROM erp_crm_customers WHERE email = ? AND tenant_id = ?");
        $stmt->execute([$this->user['email'], $this->tenantId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            die("Client record not found.");
        }
        $this->customerId = $customer['id'];
    }

    public function dashboard()
    {
        // Stats
        $activeProjects = $this->pdo->prepare("SELECT COUNT(*) FROM erp_projects WHERE client_id = ? AND status != 'completed'");
        $activeProjects->execute([$this->customerId]);
        $countProjects = $activeProjects->fetchColumn();

        $unpaidInvoices = $this->pdo->prepare("SELECT COUNT(*) FROM erp_invoices WHERE client_email = ? AND status != 'paid'");
        $unpaidInvoices->execute([$this->user['email']]); // Invoice usually linked by email or name
        $countInvoices = $unpaidInvoices->fetchColumn();

        require __DIR__ . '/../views/client/dashboard.php';
    }

    public function projects()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_projects WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->customerId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/client/projects.php';
    }

    public function projectDetails($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_projects WHERE id = ? AND client_id = ?");
        $stmt->execute([$id, $this->customerId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            header('Location: /erp/client/projects');
            exit;
        }

        // Get Tasks
        $tStmt = $this->pdo->prepare("SELECT * FROM erp_tasks WHERE project_id = ? ORDER BY status ASC");
        $tStmt->execute([$id]);
        $tasks = $tStmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/client/project_details.php';
    }

    public function invoices()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_invoices WHERE client_email = ? ORDER BY created_at DESC");
        $stmt->execute([$this->user['email']]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/client/invoices.php';
    }
}

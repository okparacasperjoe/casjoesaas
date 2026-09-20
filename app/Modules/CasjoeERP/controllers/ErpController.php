<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use PDO;

class ErpController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    private function checkAdmin()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    private function checkHrAccess()
    {
        $role = \App\Core\Auth::user()['role'] ?? '';
        if ($role !== 'admin' && $role !== 'hr') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    public function dashboard()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);

        $db = Database::getInstance();

        // Main ERP Dashboard Stats
        $stmtEmp = $db->query("SELECT COUNT(*) FROM erp_employees WHERE tenant_id = ? AND status='active'", [$tenantId]);
        $employeeCount = $stmtEmp ? $stmtEmp->fetchColumn() : 0;
        
        $stmtCust = $db->query("SELECT COUNT(*) FROM erp_customers WHERE tenant_id = ? AND status='customer'", [$tenantId]);
        $customerCount = $stmtCust ? $stmtCust->fetchColumn() : 0;

        // Pending tasks
        $pendingTasks = 0;
        $totalTasks = 0;
        try {
            $stmtPendTask = $db->query("SELECT COUNT(*) FROM erp_tasks WHERE tenant_id = ? AND status IN ('open','in_progress')", [$tenantId]);
            $pendingTasks = $stmtPendTask ? $stmtPendTask->fetchColumn() : 0;
            
            $stmtTotTask = $db->query("SELECT COUNT(*) FROM erp_tasks WHERE tenant_id = ?", [$tenantId]);
            $totalTasks = $stmtTotTask ? $stmtTotTask->fetchColumn() : 0;
        } catch (\Exception $e) {}

        // Pending leave requests
        $pendingLeave = 0;
        try {
            $stmtLeave = $db->query("SELECT COUNT(*) FROM erp_leave_requests WHERE tenant_id = ? AND status='pending'", [$tenantId]);
            $pendingLeave = $stmtLeave ? $stmtLeave->fetchColumn() : 0;
        } catch (\Exception $e) {}

        // Overdue invoices
        $overdueInvoices = 0;
        try {
            $stmtInv = $db->query("SELECT COUNT(*) FROM erp_invoices WHERE tenant_id = ? AND status='sent' AND due_date < CURDATE()", [$tenantId]);
            $overdueInvoices = $stmtInv ? $stmtInv->fetchColumn() : 0;
        } catch (\Exception $e) {}

        // Open projects
        $openProjects = 0;
        try {
            $stmtProj = $db->query("SELECT COUNT(*) FROM erp_projects WHERE tenant_id = ? AND status='active'", [$tenantId]);
            $openProjects = $stmtProj ? $stmtProj->fetchColumn() : 0;
        } catch (\Exception $e) {}

        // Scheduler Bookings
        $upcomingBookings = 0;
        try {
            $stmtBook = $db->query("SELECT COUNT(*) FROM erp_scheduler_bookings WHERE tenant_id = ? AND status='confirmed' AND booking_date >= CURDATE()", [$tenantId]);
            $upcomingBookings = $stmtBook ? $stmtBook->fetchColumn() : 0;
        } catch (\Exception $e) {}

        $stats = [
            'employees' => $employeeCount,
            'customers' => $customerCount,
            'revenue' => 0.00,
            'pending_tasks' => $pendingTasks,
            'total_tasks' => $totalTasks,
            'pending_leave' => $pendingLeave,
            'overdue_invoices' => $overdueInvoices,
            'open_projects' => $openProjects,
            'upcoming_bookings' => $upcomingBookings,
        ];

        // Goals
        $goals = [
            ['title' => 'Revenue Target', 'current' => 2000, 'target' => 3000000, 'unit' => 'NGN', 'color' => '#000066'],
            ['title' => 'Client Projects', 'current' => 80000, 'target' => 2000000, 'unit' => 'NGN', 'color' => '#FFA600'],
            ['title' => 'User Growth', 'current' => 0, 'target' => 500, 'unit' => 'Users', 'color' => '#2ed573'],
        ];

        // Staff of the Month
        try {
            $stmt = $db->query("SELECT first_name, last_name, job_title, profile_photo FROM erp_employees WHERE tenant_id = ? AND status = 'active' ORDER BY created_at ASC LIMIT 1", [$tenantId]);
            $staffOfMonth = $stmt->fetch();
        } catch (\Exception $e) {
            $staffOfMonth = null;
        }

        // Recent activity
        $recentActivity = [];
        try {
            $stmt = $db->query("SELECT action, description, created_at FROM erp_activity_log WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 5", [$tenantId]);
            $recentActivity = $stmt->fetchAll() ?: [];
        } catch (\Exception $e) {}

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function finance()
    {
        $this->checkAdmin();
        $tenantId = \App\Core\TenantContext::getTenantId();
        // List GL Accounts
        $stmt = \App\Core\Database::getInstance()->query("SELECT * FROM erp_gl_accounts WHERE tenant_id = ? ORDER BY code ASC", [$tenantId]);
        $accounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/finance/index.php';
    }

    public function hr()
    {
        $this->checkHrAccess();
        $tenantId = \App\Core\TenantContext::getTenantId();
        $stmt = \App\Core\Database::getInstance()->query("SELECT * FROM erp_employees WHERE tenant_id = ? ORDER BY last_name ASC", [$tenantId]);
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/hr/index.php';
    }

    public function crm()
    {
        $this->checkAdmin();
        $tenantId = \App\Core\TenantContext::getTenantId();
        $stmt = \App\Core\Database::getInstance()->query("SELECT * FROM erp_customers WHERE tenant_id = ? ORDER BY created_at DESC", [$tenantId]);
        $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/crm/index.php';
    }

    // Inventory & Assets
    public function stock() { $this->checkAdmin(); header('Location: /erp/inventory'); exit; }
    public function assets() { (new \App\Modules\CasjoeERP\Controllers\FinanceController())->assets(); }

    // HR Extended
    public function departments() { $this->checkHrAccess(); header('Location: /erp/departments'); exit; }
    public function attendance() { $this->checkHrAccess(); header('Location: /erp/attendance'); exit; }
    public function leave() { $this->checkHrAccess(); header('Location: /erp/leave'); exit; }
    public function payroll() { $this->checkHrAccess(); header('Location: /erp/payroll'); exit; }
    public function performance() { $this->checkHrAccess(); header('Location: /erp/performance'); exit; }
    public function recruitment() { $this->checkHrAccess(); header('Location: /erp/recruitment'); exit; }
    public function training() { $this->checkHrAccess(); header('Location: /erp/training'); exit; }
    public function promotion() { $this->checkHrAccess(); header('Location: /erp/lifecycle'); exit; }
    public function resignation() { $this->checkHrAccess(); header('Location: /erp/lifecycle'); exit; }
    public function termination() { $this->checkHrAccess(); header('Location: /erp/lifecycle'); exit; }

    // CRM Extended
    public function leads() { $this->checkAdmin(); header('Location: /erp/crm/leads'); exit; }
    public function opportunities() { $this->checkAdmin(); header('Location: /erp/crm/opportunities'); exit; }
    public function sales() { $this->checkAdmin(); header('Location: /erp/crm/sales'); exit; }
    public function tickets() { $this->checkAdmin(); header('Location: /erp/crm'); exit; }
    public function knowledge_base() { require __DIR__ . '/../Views/system/kb.php'; }

    // Project Management
    public function projects() { header('Location: /erp/projects'); exit; }
    public function tasks() { header('Location: /erp/tasks'); exit; }
    public function calendar() { header('Location: /erp/calendar'); exit; }

    // Finance
    public function transactions() { $this->checkAdmin(); header('Location: /erp/finance/transactions'); exit; }

    // System & Utilities
    public function announcements() { header('Location: /erp/announcements'); exit; }
    public function chat() { require __DIR__ . '/../Views/system/chat.php'; }
    public function reports() { $this->checkAdmin(); header('Location: /erp/reports'); exit; }
    public function settings() { $this->checkAdmin(); header('Location: /erp/settings'); exit; }

    public function activity() { $this->checkAdmin(); header('Location: /erp/activity'); exit; }
    public function notifications() { require __DIR__ . '/../Views/system/notifications.php'; }

    public function benefits()
    {
        $this->checkHrAccess();
        header('Location: /erp/payroll');
        exit;
    }

    public function stats()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        $db = Database::getInstance();
        $stmtEmp = $db->query("SELECT COUNT(*) FROM erp_employees WHERE tenant_id = ? AND status='active'", [$tenantId]);
        $employeeCount = $stmtEmp ? (int)$stmtEmp->fetchColumn() : 0;
        $stmtCust = $db->query("SELECT COUNT(*) FROM erp_customers WHERE tenant_id = ? AND status='customer'", [$tenantId]);
        $customerCount = $stmtCust ? (int)$stmtCust->fetchColumn() : 0;

        $pendingTasks = 0;
        try {
            $stmtPendTask = $db->query("SELECT COUNT(*) FROM erp_tasks WHERE tenant_id = ? AND status IN ('open','in_progress')", [$tenantId]);
            $pendingTasks = $stmtPendTask ? (int)$stmtPendTask->fetchColumn() : 0;
        } catch (\Exception $e) {}

        $pendingLeave = 0;
        try {
            $stmtLeave = $db->query("SELECT COUNT(*) FROM erp_leave_requests WHERE tenant_id = ? AND status='pending'", [$tenantId]);
            $pendingLeave = $stmtLeave ? (int)$stmtLeave->fetchColumn() : 0;
        } catch (\Exception $e) {}

        $stats = [
            'success' => true,
            'employees' => $employeeCount,
            'customers' => $customerCount,
            'pending_tasks' => $pendingTasks,
            'pending_leave' => $pendingLeave,
        ];
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
}


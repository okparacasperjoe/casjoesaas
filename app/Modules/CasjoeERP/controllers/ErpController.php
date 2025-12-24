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

        // Main ERP Dashboard Stats
        $stats = [
            'employees' => $this->pdo->query("SELECT COUNT(*) FROM erp_employees WHERE status='active'")->fetchColumn(),
            'customers' => $this->pdo->query("SELECT COUNT(*) FROM erp_customers WHERE status='customer'")->fetchColumn(),
            'revenue' => 0.00, // Placeholder
        ];

        require __DIR__ . '/../views/dashboard.php';
    }

    public function finance()
    {
        $this->checkAdmin();
        // List GL Accounts
        $stmt = $this->pdo->query("SELECT * FROM erp_gl_accounts ORDER BY code ASC");
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/finance/index.php';
    }

    public function hr()
    {
        $this->checkHrAccess();
        $stmt = $this->pdo->query("SELECT * FROM erp_employees ORDER BY last_name ASC");
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/hr/index.php';
    }

    public function crm()
    {
        $this->checkAdmin();
        $stmt = $this->pdo->query("SELECT * FROM erp_customers ORDER BY created_at DESC");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/crm/index.php';
    }

    // Inventory & Assets
    public function stock() { $this->checkAdmin(); header('Location: /erp/inventory'); exit; }
    public function assets() { $this->checkAdmin(); require __DIR__ . '/../views/system/assets.php'; }

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
    public function tickets() { $this->checkAdmin(); header('Location: /erp/support'); exit; }
    public function knowledge_base() { require __DIR__ . '/../views/system/kb.php'; }

    // Project Management
    public function projects() { header('Location: /erp/projects'); exit; }
    public function tasks() { header('Location: /erp/tasks'); exit; }
    public function calendar() { header('Location: /erp/calendar'); exit; }

    // Finance
    public function transactions() { $this->checkAdmin(); header('Location: /erp/finance/transactions'); exit; }

    // System & Utilities
    public function announcements() { header('Location: /erp/announcements'); exit; }
    public function chat() { require __DIR__ . '/../views/system/chat.php'; }
    public function reports() { $this->checkAdmin(); header('Location: /erp/reports'); exit; }
    public function settings() { $this->checkAdmin(); header('Location: /erp/settings'); exit; }
    public function users() { $this->checkAdmin(); require __DIR__ . '/../views/system/users.php'; }
    public function activity() { $this->checkAdmin(); header('Location: /erp/activity'); exit; }
    public function notifications() { require __DIR__ . '/../views/system/notifications.php'; }
}

<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class DashboardController
{
    public function index()
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $user = \App\Core\Auth::user();
        $tenantId = \App\Core\TenantContext::getTenantId();
        if ($user && !empty($user['tenant_id'])) {
            $tenantId = $user['tenant_id'];
        }

        $email = $user['email'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? AND email = ? LIMIT 1");
        $stmt->execute([$tenantId, $email]);
        $employee = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$employee) {
            $stmt = $pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? LIMIT 1");
            $stmt->execute([$tenantId]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        $activeAttendance = false;
        if ($employee) {
            $stmtActive = $pdo->prepare("SELECT * FROM erp_attendance WHERE employee_id = ? AND DATE(check_in) = CURDATE() AND check_out IS NULL ORDER BY check_in DESC LIMIT 1");
            $stmtActive->execute([$employee['id']]);
            $activeAttendance = $stmtActive->fetch(\PDO::FETCH_ASSOC);
        }

        // Finance Summary (Cash Balance from GL)
        $cashBalance = 0.00; // Placeholder for GL query

        // HR Summary
        $stmt = $db->query("SELECT COUNT(*) as count FROM erp_employees WHERE tenant_id = ? AND status = 'active'", [$tenantId]);
        $employeeCount = $stmt->fetch()['count'];

        // CRM Summary
        $stmt = $db->query("SELECT COUNT(*) as count FROM erp_customers WHERE tenant_id = ?", [$tenantId]);
        $customerCount = $stmt->fetch()['count'];

        // Inventory Summary
        $stmt = $db->query("SELECT COUNT(*) as count FROM erp_products WHERE tenant_id = ?", [$tenantId]);
        $productCount = $stmt->fetch()['count'];

        $stats = [
            'revenue' => 190000.00,
            'pending_tasks' => 5,
            'total_tasks' => 8,
            'employees' => $employeeCount,
            'customers' => $customerCount,
        ];
        
        $goals = [
            ['title' => 'Classora', 'current' => 2000, 'target' => 3000000, 'unit' => 'NGN'],
            ['title' => 'Client Jobs', 'current' => 80000, 'target' => 2000000, 'unit' => 'NGN'],
            ['title' => 'Casjoe App Launch Users', 'current' => 0, 'target' => 3000000, 'unit' => 'NGN'],
        ];

        $stmt = $db->query("SELECT first_name, last_name, job_title FROM erp_employees WHERE tenant_id = ? AND status = 'active' ORDER BY created_at ASC LIMIT 1", [$tenantId]);
        $staffOfMonth = $stmt->fetch();
        if(!$staffOfMonth) {
            $staffOfMonth = ['first_name' => 'Dick', 'last_name' => 'sopuruchukwu okareoma', 'job_title' => 'Video Editor'];
        }

        require __DIR__ . '/../Views/dashboard.php';
    }
}

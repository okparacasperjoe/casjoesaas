<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class DashboardController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

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

        require __DIR__ . '/../views/dashboard.php';
    }
}

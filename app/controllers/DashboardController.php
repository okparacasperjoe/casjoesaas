<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class DashboardController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Aggregate Data from Modules

        // Academy
        $stmt = $db->query("SELECT COUNT(*) as count FROM academy_courses WHERE tenant_id = ?", [$tenantId]);
        $courseCount = $stmt->fetch()['count'];

        // Pay
        $stmt = $db->query("SELECT * FROM cp_wallets WHERE tenant_id = ?", [$tenantId]);
        $wallet = $stmt->fetch();
        $balance = $wallet ? $wallet['balance'] : 0.00;

        // ERP
        $stmt = $db->query("SELECT COUNT(*) as count FROM erp_employees WHERE tenant_id = ?", [$tenantId]);
        $empCount = $stmt->fetch()['count'];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}

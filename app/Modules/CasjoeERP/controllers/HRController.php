<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class HRController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM erp_employees WHERE tenant_id = ?", [$tenantId]);
        $employees = $stmt->fetchAll();

        require __DIR__ . '/../Views/hr.php';
    }

    public function addEmployee()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $db->query(
            "INSERT INTO erp_employees (tenant_id, first_name, last_name, email, department, salary, hired_date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())",
            [$tenantId, $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['department'], $_POST['salary']]
        );

        header("Location: /erp/hr");
    }
}

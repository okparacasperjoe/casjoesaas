<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class DepartmentController
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
            SELECT d.*, COUNT(e.id) as employee_count 
            FROM erp_departments d 
            LEFT JOIN erp_employees e ON e.department_id = d.id 
            WHERE d.tenant_id = ? 
            GROUP BY d.id 
            ORDER BY d.name ASC
        ");
        $stmt->execute([$this->tenantId]);
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/departments/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/hr/departments/create.php';
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($name)) {
            die("Name is required");
        }

        $stmt = $this->pdo->prepare("INSERT INTO erp_departments (tenant_id, name, description) VALUES (?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $description]);

        header('Location: /erp/departments');
        exit;
    }


    public function update() {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $this->pdo->prepare("UPDATE erp_departments SET name = ?, description = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$name, $description, $id, $this->tenantId]);
        header('Location: /erp/departments');
        exit;
    }

    public function delete() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_departments WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/departments');
        exit;
    }

    public function designations()
    {
        header('Location: /erp/departments');
        exit;
    }
}

<?php

namespace App\Modules\CasjoeErp\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class GoalController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->tenantId = TenantContext::getTenantId();
        $this->pdo = Database::getInstance()->getConnection();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        // Self-healing: Create table if missing
        $sql = "CREATE TABLE IF NOT EXISTS erp_goals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            target_value DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            current_value DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            unit VARCHAR(50) DEFAULT 'NGN',
            deadline DATE NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_tenant (tenant_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        try {
            $this->pdo->exec($sql);
        } catch (\Exception $e) {
            // Log silent error or ignore if already exists/permission issue
        }
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_goals WHERE tenant_id = ? ORDER BY deadline ASC");
        $stmt->execute([$this->tenantId]);
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../Views/goals/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/goals/create.php';
    }

    public function store()
    {
        $title = $_POST['title'];
        $target = $_POST['target_value'];
        $current = $_POST['current_value'] ?? 0;
        $unit = $_POST['unit'] ?? '';
        $deadline = $_POST['deadline'] ?? null;

        $stmt = $this->pdo->prepare("INSERT INTO erp_goals (tenant_id, title, target_value, current_value, unit, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $target, $current, $unit, $deadline]);

        header('Location: /erp/goals?success=created');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT * FROM erp_goals WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$goal) {
            header('Location: /erp/goals');
            exit;
        }

        require __DIR__ . '/../Views/goals/edit.php';
    }

    public function update()
    {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $target = $_POST['target_value'];
        $current = $_POST['current_value'];
        $unit = $_POST['unit'];
        $deadline = $_POST['deadline'];
        $status = $_POST['status'];

        $stmt = $this->pdo->prepare("UPDATE erp_goals SET title=?, target_value=?, current_value=?, unit=?, deadline=?, status=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$title, $target, $current, $unit, $deadline, $status, $id, $this->tenantId]);

        header('Location: /erp/goals?success=updated');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_goals WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        header('Location: /erp/goals?success=deleted');
        exit;
    }
}

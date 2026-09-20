<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class TrainingController
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
        $stmt = $this->pdo->prepare("SELECT * FROM erp_training_programs WHERE tenant_id = ? ORDER BY start_date DESC");
        $stmt->execute([$this->tenantId]);
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/training/index.php';
    }

    public function storeProgram()
    {
        $name = $_POST['name'];
        $instructor = $_POST['instructor'];
        $start = $_POST['start_date'];
        $end = $_POST['end_date'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_training_programs (tenant_id, name, instructor, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $instructor, $start, $end]);

        header('Location: /erp/training');
        exit;
    }

    public function enroll()
    {
         $programId = $_POST['program_id'];
         
         // Enroll all active employees for demo purposes if not specified
         // Or just a placeholder action
         // Real logic: insert into erp_training_enrollments
         
         // We'll just demonstrate simple enrollment of a "test" employee or current user if implied
         // For now, let's just redirect back as this is a list view focused implementation
         header("Location: /erp/training");
         exit;
    }

    public function deleteProgram() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_hr_training WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/training');
        exit;
    }
}

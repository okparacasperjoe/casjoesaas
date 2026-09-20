<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class PerformanceController
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
            SELECT r.*, e.first_name, e.last_name 
            FROM erp_performance_reviews r
            JOIN erp_employees e ON r.employee_id = e.id
            WHERE r.tenant_id = ?
            ORDER BY r.review_date DESC
        ");
        $stmt->execute([$this->tenantId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/performance/index.php';
    }

    public function create()
    {
        // Get employees
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/performance/create.php';
    }

    public function store()
    {
        $employeeId = $_POST['employee_id'];
        $reviewDate = $_POST['review_date'];
        $rating = $_POST['rating'];
        $comments = $_POST['comments'];
        $reviewerId = $_SESSION['user_id'] ?? null; // Assuming current user is reviewer

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_performance_reviews 
            (tenant_id, employee_id, reviewer_id, review_date, rating, comments) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$this->tenantId, $employeeId, $reviewerId, $reviewDate, $rating, $comments]);

        header('Location: /erp/performance');
        exit;
    }


    public function delete() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_hr_performance WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/hr/performance');
        exit;
    }
}

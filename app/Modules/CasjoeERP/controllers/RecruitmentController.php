<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class RecruitmentController
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
        $stmt = $this->pdo->prepare("SELECT * FROM erp_job_postings WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/recruitment/index.php';
    }

    public function storeJob()
    {
        $title = $_POST['title'];
        $department = $_POST['department'];
        $description = $_POST['description'];

        $customQuestions = null;
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            $questions = array_filter(array_map('trim', $_POST['questions']));
            if (!empty($questions)) {
                $customQuestions = json_encode(array_values($questions));
            }
        }

        $stmt = $this->pdo->prepare("INSERT INTO erp_job_postings (tenant_id, title, department, description, custom_questions) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $title, $department, $description, $customQuestions]);

        header('Location: /erp/recruitment');
        exit;
    }

    public function applications()
    {
        $jobId = $_GET['job_id'] ?? null;
        if ($jobId) {
            $stmt = $this->pdo->prepare("SELECT a.*, j.title FROM erp_job_applications a JOIN erp_job_postings j ON a.job_id = j.id WHERE a.job_id = ? AND a.tenant_id = ? ORDER BY a.created_at DESC");
            $stmt->execute([$jobId, $this->tenantId]);
        } else {
             $stmt = $this->pdo->prepare("SELECT a.*, j.title FROM erp_job_applications a JOIN erp_job_postings j ON a.job_id = j.id WHERE a.tenant_id = ? ORDER BY a.created_at DESC");
             $stmt->execute([$this->tenantId]);
        }
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/recruitment/applications.php';
    }

    public function storeApplication() 
    {
         // Mock public submission
         // In real app, this might not require auth, but here we assume internal testing
         $jobId = $_POST['job_id'];
         $name = $_POST['candidate_name'];
         $email = $_POST['email'];

         $stmt = $this->pdo->prepare("INSERT INTO erp_job_applications (tenant_id, job_id, candidate_name, email) VALUES (?, ?, ?, ?)");
         $stmt->execute([$this->tenantId, $jobId, $name, $email]);

         header("Location: /erp/recruitment"); 
         exit;
    }

    public function deleteJob() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_job_postings WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/recruitment');
        exit;
    }

    public function deleteApplication() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_job_applications WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/recruitment/applications');
        exit;
    }
}

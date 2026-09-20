<?php

namespace App\Core\Controllers;

use App\Core\Database;
use PDO;

class PublicCareersController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function showJob()
    {
        $jobId = $_GET['id'] ?? null;
        if (!$jobId) {
            die("Job ID not provided.");
        }

        // Fetch the job posting and its associated tenant name
        $stmt = $this->pdo->prepare("
            SELECT j.*, t.name as company_name, t.domain, t.subdomain 
            FROM erp_job_postings j
            JOIN tenants t ON j.tenant_id = t.id
            WHERE j.id = ?
        ");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            die("Job not found or no longer available.");
        }

        require __DIR__ . '/../Views/careers/public_job.php';
    }

    public function submitApplication()
    {
        $jobId = $_POST['job_id'] ?? null;
        $tenantId = $_POST['tenant_id'] ?? null;
        $name = $_POST['candidate_name'] ?? '';
        $email = $_POST['email'] ?? '';
        
        if (!$jobId || !$tenantId || !$name || !$email) {
            die("Required fields missing.");
        }

        // Handle Resume Upload
        $resumePath = null;
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/resumes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = time() . '_' . basename($_FILES['resume']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
                $resumePath = '/uploads/resumes/' . $fileName;
            }
        }

        // Fetch original questions to pair with answers
        $stmtQ = $this->pdo->prepare("SELECT custom_questions FROM erp_job_postings WHERE id = ?");
        $stmtQ->execute([$jobId]);
        $jobPost = $stmtQ->fetch(PDO::FETCH_ASSOC);
        $questions = !empty($jobPost['custom_questions']) ? json_decode($jobPost['custom_questions'], true) : [];

        $customAnswers = null;
        if (isset($_POST['answers']) && is_array($_POST['answers']) && !empty($questions)) {
            $answersArray = [];
            foreach ($_POST['answers'] as $index => $answer) {
                if (isset($questions[$index])) {
                    $answersArray[] = [
                        'question' => $questions[$index],
                        'answer' => trim($answer)
                    ];
                }
            }
            if (!empty($answersArray)) {
                $customAnswers = json_encode($answersArray);
            }
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_job_applications (tenant_id, job_id, candidate_name, email, resume_path, custom_answers) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$tenantId, $jobId, $name, $email, $resumePath, $customAnswers]);

        // Redirect back with success message
        header("Location: /careers/job?id=" . $jobId . "&success=1");
        exit;
    }

    public function checkModules()
    {
        $stmt = $this->pdo->query("SELECT id, name, slug FROM modules");
        $modules = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo "MODULES IN PRODUCTION:<br>";
        foreach ($modules as $m) {
            echo "ID: " . $m['id'] . " | Name: " . $m['name'] . " | Slug: " . $m['slug'] . "<br>";
        }
    }
}

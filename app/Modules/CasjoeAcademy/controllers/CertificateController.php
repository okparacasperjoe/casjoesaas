<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Auth;
use App\Core\Database;

class CertificateController
{
    // Generate Certificate for a Course
    public function generate($params = [])
    {
        $courseId = $_POST['course_id'] ?? $params['courseId'] ?? null;
        
        if (!$courseId) {
            die("Error: Course ID is missing. Please try again or contact support.");
        }
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        // 1. Check if already exists
        $stmt = $db->query("SELECT certificate_code FROM academy_certificates WHERE user_id = ? AND course_id = ?", 
            [$user['id'], $courseId]
        );
        $existing = $stmt->fetch();

        if ($existing) {
            header('Location: /academy/certificate/' . $existing['certificate_code']);
            exit;
        }

        // 2. Generate Unique Code
        $code = strtoupper('CASJOE-' . bin2hex(random_bytes(4)));
        
        // 3. Save
        $db->query("INSERT INTO academy_certificates (tenant_id, user_id, course_id, certificate_code) VALUES (?, ?, ?, ?)", 
            [$user['tenant_id'], $user['id'], $courseId, $code]
        );

        header('Location: /academy/certificate/' . $code);
    }

    // Public Verification / View
    public function view($params)
    {
        $code = $params['code'] ?? '';
        $db = Database::getInstance();
        
        $stmt = $db->query("
            SELECT c.*, u.name as student_name, co.title as course_title 
            FROM academy_certificates c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN academy_courses co ON c.course_id = co.id
            WHERE c.certificate_code = ?
        ", [$code]);
        
        $cert = $stmt->fetch();

        if (!$cert) {
            die('Certificate not found.');
        }

        require __DIR__ . '/../Views/learner/certificate/view.php';
    }
}


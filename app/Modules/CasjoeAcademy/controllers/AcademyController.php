<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use PDO;

class AcademyController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);

        // Public Catalog
        $stmt = $this->pdo->query("SELECT * FROM academy_courses WHERE status = 'published' ORDER BY created_at DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/catalog.php';
    }

    public function myCourses()
    {
        if (!isset($_SESSION['user_id'])) {
           header('Location: /login');
           exit;
        }

        $userId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("
            SELECT c.*, e.progress_percent 
            FROM academy_courses c
            JOIN academy_enrollments e ON c.id = e.course_id
            WHERE e.user_id = ?
        ");
        $stmt->execute([$userId]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/student_dashboard.php';
    }

    public function course($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM academy_courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) die("Course not found");

        // Get Curriculum
        $stmt = $this->pdo->prepare("SELECT * FROM academy_sections WHERE course_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$id]);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as &$section) {
            $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE section_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$section['id']]);
            $section['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Check Enrollment
        $isEnrolled = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->pdo->prepare("SELECT * FROM academy_enrollments WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$_SESSION['user_id'], $id]);
            $isEnrolled = $stmt->fetch();
        }

        require __DIR__ . '/../views/course_details.php';
    }

    public function enroll($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Logic to check payment if price > 0 would go here
        
        // Enroll
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO academy_enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id]);

        header("Location: /academy/course/$id");
    }

    public function learn($lessonId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Fetch Lesson
        $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lesson) die("Lesson not found");

        // Fetch Section & Course
        $stmt = $this->pdo->prepare("SELECT * FROM academy_sections WHERE id = ?");
        $stmt->execute([$lesson['section_id']]);
        $section = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT * FROM academy_courses WHERE id = ?");
        $stmt->execute([$section['course_id']]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fetch All Sections for Sidebar
         $stmt = $this->pdo->prepare("SELECT * FROM academy_sections WHERE course_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$course['id']]);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as &$s) {
            $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE section_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$s['id']]);
            $s['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../views/learn.php';
    }
}

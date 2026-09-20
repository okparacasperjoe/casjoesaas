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
        header('Location: /academy/overview');
        exit;
    }

    public function overview()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);
        
        $active = 'academy_overview';
        require __DIR__ . '/../Views/overview.php';
    }

    public function catalog()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);

        // Public Catalog Courses (Deduplicated by Title)
        $stmt = $this->pdo->query("SELECT * FROM academy_courses WHERE status = 'published' ORDER BY id DESC");
        $rawCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $uniqueCourses = [];
        foreach ($rawCourses as $c) {
            $key = strtolower(trim($c['title']));
            if (!isset($uniqueCourses[$key])) {
                $uniqueCourses[$key] = $c;
            }
        }
        $courses = array_values($uniqueCourses);

        // Public Catalog Books (All Tenants - Deduplicated by Title)
        $books = [];
        try {
            $stmtBooks = $this->pdo->query("SELECT * FROM academy_books WHERE (is_published = 1 OR is_published IS NULL) ORDER BY id DESC");
            if ($stmtBooks) {
                $rawBooks = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);
                $uniqueBooks = [];
                foreach ($rawBooks as $b) {
                    $key = strtolower(trim($b['title']));
                    if (!isset($uniqueBooks[$key])) {
                        $uniqueBooks[$key] = $b;
                    }
                }
                $books = array_values($uniqueBooks);
            }
        } catch (\Exception $e) {
            $books = [];
        }

        require __DIR__ . '/../Views/catalog.php';
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

        require __DIR__ . '/../Views/student_dashboard.php';
    }

    public function course($id)
    {
        if (is_array($id)) $id = array_values($id)[0];
        
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

        require __DIR__ . '/../Views/course_details.php';
    }

    public function enroll($id)
    {
        if (is_array($id)) $id = array_values($id)[0];
        
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
        if (is_array($lessonId)) $lessonId = array_values($lessonId)[0];
        
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

        require __DIR__ . '/../Views/learn.php';
    }
}

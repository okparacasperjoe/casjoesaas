<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TenantContext;

class LearnerController
{
    private $pdo;
    private $tenantId;

    public function __construct() {
        if (!Auth::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header("Location: /login");
            exit;
        }
        $this->tenantId = TenantContext::getTenantId();
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function dashboard() {
        $userId = Auth::user()['id'];
        $tenantId = Auth::user()['tenant_id'];

        $stmt = $this->pdo->prepare("
            SELECT c.*, e.progress_percent, e.enrolled_at, e.id as enrollment_id
            FROM academy_courses c
            JOIN academy_enrollments e ON c.id = e.course_id
            WHERE e.user_id = ?
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute([$userId]);
        $courses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch Gamification Stats
        $stats = \App\Modules\CasjoeAcademy\Services\GamificationService::getUserStats($userId, $tenantId);
        $badges = \App\Modules\CasjoeAcademy\Services\GamificationService::getUserBadges($userId, $tenantId);

        // Fetch Certificates
        $stmt = $this->pdo->prepare("
            SELECT c.certificate_code, c.issued_at, co.title as course_title 
            FROM academy_certificates c
            JOIN academy_courses co ON c.course_id = co.id
            WHERE c.user_id = ?
            ORDER BY c.issued_at DESC
        ");
        $stmt->execute([$userId]);
        $certificates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/student_dashboard.php';
    }

    public function learn($params) {
        $id = $params['id'];
        $userId = Auth::user()['id'];

        // 1. Fetch Lesson
        $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE id = ?");
        $stmt->execute([$id]);
        $lesson = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$lesson) {
            header("Location: /academy/learn?error=LessonNotFound&id=" . htmlspecialchars($id));
            exit;
        }

        // 2. Fetch Course & Sections
        $courseId = 0; 
        if (!empty($lesson['section_id'])) {
            $stmt = $this->pdo->prepare("SELECT course_id FROM academy_sections WHERE id = ?");
            $stmt->execute([$lesson['section_id']]);
            $courseId = $stmt->fetchColumn();
        } elseif (!empty($lesson['course_id'])) {
            $courseId = $lesson['course_id'];
        }

        if (!$courseId) {
            header("Location: /academy/learn?error=LessonContextMissing&id=" . htmlspecialchars($id));
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM academy_courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 3. Verify Enrollment or Ownership
        $stmt = $this->pdo->prepare("SELECT * FROM academy_enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        $enrollment = $stmt->fetch(\PDO::FETCH_ASSOC);

        $isOwner = (Auth::user()['tenant_id'] == $course['tenant_id']);

        if (!$enrollment && !$isOwner) {
            header("Location: /academy/course/" . htmlspecialchars($courseId) . "?error=NotEnrolled");
            exit;
        }

        // 4. Fetch Full Curriculum for Sidebar
        // Check if academy_lessons has section_id
        try {
            $cols = $this->pdo->query("SHOW COLUMNS FROM academy_lessons")->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            $cols = []; 
        }
        $hasSectionId = in_array('section_id', $cols);

        $sections = [];

        if ($hasSectionId) {
            // Nested Structure
            $stmt = $this->pdo->prepare("SELECT * FROM academy_sections WHERE course_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$courseId]);
            $sections = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($sections as &$s) {
                $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE section_id = ? ORDER BY sort_order ASC");
                $stmt->execute([$s['id']]);
                $s['lessons'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } else {
            // Flat Structure
            $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE course_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$courseId]);
            $flatLessons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch sections if they exist, to use titles if possible
            $stmt = $this->pdo->prepare("SELECT * FROM academy_sections WHERE course_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$courseId]);
            $existingSections = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($existingSections)) {
                // Mock a single section for the view
                $sections[] = [
                    'id' => 0,
                    'title' => 'Course Content',
                    'lessons' => $flatLessons
                ];
            } else {
                // Fallback: Dump all lessons into the first section found
               $sections = $existingSections;
               // If there are multiple sections but lessons are flat, this logic is imperfect but prevents crash.
               // Best effort: attach all lessons to first section.
               if (!empty($sections)) {
                   $sections[0]['lessons'] = $flatLessons;
                   // Ensure other sections have empty lessons array to prevent view errors
                   for($i=1; $i<count($sections); $i++) {
                       $sections[$i]['lessons'] = [];
                   }
               } else {
                    $sections[] = [
                        'id' => 0,
                        'title' => 'Course Content',
                        'lessons' => $flatLessons
                    ];
               }
            }
        }

        // 5. Check Completion Status
        $stmt = $this->pdo->prepare("SELECT lesson_id FROM academy_lesson_completions WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        $completedLessonIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $isCompleted = in_array($id, $completedLessonIds);

        // View
        require __DIR__ . '/../Views/learn.php';
    }

    public function markComplete($params) {
        $this->completeLesson($params);
    }

    public function completeLesson($params) {
        $id = isset($params['id']) ? $params['id'] : $params['lessonId'];
        $userId = Auth::user()['id'];

        // Get Course ID from Lesson
        $stmt = $this->pdo->prepare("SELECT * FROM academy_lessons WHERE id = ?");
        $stmt->execute([$id]);
        $lesson = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$lesson) die("Invalid lesson.");

        $courseId = 0;
        if (!empty($lesson['section_id'])) {
            $stmt = $this->pdo->prepare("SELECT course_id FROM academy_sections WHERE id = ?");
            $stmt->execute([$lesson['section_id']]);
            $courseId = $stmt->fetchColumn();
        } elseif (!empty($lesson['course_id'])) {
            $courseId = $lesson['course_id'];
        }

        if (!$courseId) die("Invalid lesson context.");

        // Mark as Complete
        // Use IGNORE to prevent error on duplicate click
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO academy_lesson_completions (user_id, lesson_id, course_id) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $id, $courseId]);
        
        // If a new row was inserted, award points for lesson completion
        if ($stmt->rowCount() > 0) {
            \App\Modules\CasjoeAcademy\Services\GamificationService::awardPoints($userId, Auth::user()['tenant_id'], 10);
            
            // Set session variable for toast notification
            $_SESSION['gamification_toast'] = [
                'message' => 'Lesson Completed! +10 Points',
                'icon' => 'star'
            ];
        }

        // Calculate New Progress
        $this->updateProgress($userId, $courseId);
        
        // Check badges after updating progress (as course master badge relies on progress)
        \App\Modules\CasjoeAcademy\Services\GamificationService::checkAndAwardBadges($userId, Auth::user()['tenant_id']);

        // Redirect to next lesson or same page
        // Redirect to same page
        header("Location: /academy/learn/" . $id);
    }

    private function updateProgress($userId, $courseId) {
        // Check for section_id
        try {
            $cols = $this->pdo->query("SHOW COLUMNS FROM academy_lessons")->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) { $cols = []; }
        $hasSectionId = in_array('section_id', $cols);

        if ($hasSectionId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM academy_lessons l
                JOIN academy_sections s ON l.section_id = s.id
                WHERE s.course_id = ?
            ");
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM academy_lessons WHERE course_id = ?");
        }
        
        $stmt->execute([$courseId]);
        $total = $stmt->fetchColumn();

        if ($total == 0) return;

        // Count Completed
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM academy_lesson_completions WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        $completed = $stmt->fetchColumn();

        $percent = min(100, round(($completed / $total) * 100));

        // Update Enrollment
        $this->pdo->prepare("UPDATE academy_enrollments SET progress_percent = ? WHERE user_id = ? AND course_id = ?")
                  ->execute([$percent, $userId, $courseId]);
    }
}


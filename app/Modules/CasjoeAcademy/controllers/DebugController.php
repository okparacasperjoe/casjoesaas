<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use PDO;

class DebugController
{
    public function fix()
    {
        echo "<h1>Casjoe Business School Fixer</h1>";
        $db = Database::getInstance()->getConnection();

        // 1. Fix Certificate Table
        echo "<h2>1. Certificate Table Setup</h2>";
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `academy_certificates` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `course_id` int(11) NOT NULL,
              `certificate_code` varchar(50) NOT NULL UNIQUE,
              `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $db->exec($sql);
            echo "<p style='color:green'>Table `academy_certificates` checked/created.</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
        }

        // 2. Clean Orphaned Enrollments
        echo "<h2>2. Cleaning Data</h2>";
        try {
            // Check orphans before delete
            $stmt = $db->query("
                SELECT e.id, e.course_id 
                FROM academy_enrollments e 
                LEFT JOIN academy_courses c ON e.course_id = c.id 
                WHERE c.id IS NULL
            ");
            $orphans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($orphans) {
                echo "<p>Found " . count($orphans) . " orphaned enrollments (Courses that happen to be deleted). Deleting...</p>";
                $ids = array_column($orphans, 'id');
                $inQuery = implode(',', array_fill(0, count($ids), '?'));
                $db->prepare("DELETE FROM academy_enrollments WHERE id IN ($inQuery)")->execute($ids);
                echo "<p style='color:green'>Deleted orphans.</p>";
            } else {
                echo "<p style='color:green'>No orphaned enrollments found.</p>";
            }
        } catch (\Exception $e) {
            echo "<p style='color:red'>Error cleaning data: " . $e->getMessage() . "</p>";
        }

        // 3. Diagnose ID 8 (User Error Report)
        echo "<h2>3. Diagnosing ID 8</h2>";
        $id = 8;
        
        // Is it a Course?
        $stmt = $db->prepare("SELECT * FROM academy_courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Checking <strong>Course ID $id</strong>: " . ($course ? "<span style='color:green'>Found</span>" : "<span style='color:red'>Not Found</span>") . "</p>";

        // Is it a Lesson?
        $stmt = $db->prepare("SELECT * FROM academy_lessons WHERE id = ?");
        $stmt->execute([$id]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($lesson) {
            echo "<p>Checking <strong>Lesson ID $id</strong>: <span style='color:green'>Found!</span> Title: " . htmlspecialchars($lesson['title']) . "</p>";
            echo "<p>Belongs to Section: {$lesson['section_id']}</p>";
            
            // What course does it belong to?
            if ($lesson['section_id']) {
                $stmt = $db->prepare("SELECT * FROM academy_sections WHERE id = ?");
                $stmt->execute([$lesson['section_id']]);
                $section = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>Section belongs to Course ID: " . ($section['course_id'] ?? 'NULL') . "</p>";
            } else {
                 echo "<p>Lesson has no section_id. Course ID: " . ($lesson['course_id'] ?? 'NULL') . "</p>";
            }
        } else {
            echo "<p>Checking <strong>Lesson ID $id</strong>: <span style='color:red'>Not Found</span></p>";
        }

        echo "<h3>Done. Please try using the Academy again.</h3>";
        echo "<a href='/academy'>Go to Academy</a>";
    }
}

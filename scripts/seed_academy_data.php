<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding Academy Data...\n";

try {
    // 1. Check if courses exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM academy_courses WHERE tenant_id = $tenantId");
    if ($stmt->fetchColumn() == 0) {
        // Create Course
        $sql = "INSERT INTO academy_courses (tenant_id, title, category, price_per_seat, description, thumbnail, status, is_system_course) 
                VALUES (?, ?, ?, ?, ?, ?, 'published', 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $tenantId, 
            'Business Management 101', 
            'Business', 
            99.00, 
            'Learn the basics of running a business.', 
            '/assets/course_business.jpg'
        ]);
        $courseId = $pdo->lastInsertId();
        echo "[OK] Created Course ID: $courseId\n";

        // Create Section
        $pdo->prepare("INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, 1)")
            ->execute([$courseId, 'Introduction']);
        $sectionId = $pdo->lastInsertId();

        // Create Lesson
        $pdo->prepare("INSERT INTO academy_lessons (section_id, title, content, sort_order) VALUES (?, ?, ?, 1)")
            ->execute([$sectionId, 'Welcome to the Course', 'This is the first lesson content.']);
        
        echo "[OK] Created Section & Lesson.\n";

    } else {
        echo "[SKIP] Courses already exist.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

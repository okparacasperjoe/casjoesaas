<?php
require_once __DIR__ . '/app/core/Database.php';
// Mock $_SESSION for tenant context if needed, but we'll manually insert
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Seeding Academy Data...\n";

// 1. Create Course
$stmt = $pdo->prepare("INSERT INTO academy_courses (tenant_id, title, description, price, status) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([1, 'PHP for Beginners', 'Learn PHP from scratch in this comprehensive course.', 0.00, 'published']);
$courseId = $pdo->lastInsertId();
echo "Created Course ID: $courseId\n";

// 2. Create Section
$stmt = $pdo->prepare("INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, ?)");
$stmt->execute([$courseId, 'Introduction', 1]);
$sectionId = $pdo->lastInsertId();

// 3. Create Lesson
$stmt = $pdo->prepare("INSERT INTO academy_lessons (section_id, title, content, video_url, sort_order) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$sectionId, 'Welcome to PHP', 'PHP is a server scripting language...', 'https://www.youtube.com/watch?v=OK_JCtrrv-c', 1]);
echo "Created Lesson for Section $sectionId\n";

// 2nd Course
$stmt->execute([1, 'Advanced SaaS Development', 'Building a multi-tenant app.', 10000.00, 'published']);
$pdo->query("INSERT INTO academy_sections (course_id, title) VALUES (".$pdo->lastInsertId().", 'Architecture')");

echo "Seeding Complete.\n";

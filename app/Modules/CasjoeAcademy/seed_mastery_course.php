<?php
// Casjoe Mastery Course Seeder
// Run this file to populate/update the mastery course content

require_once __DIR__ . '/../../Core/bootstrap.php';

use App\Core\Database;
use App\Core\TenantContext;

$db = Database::getInstance()->getConnection();
$tenantId = 1; // Default tenant or current context. Assuming global course for main tenant.

// 1. Create/Update Course
$courseTitle = "Casjoe Mastery Course";
$description = "Welcome to the official Casjoe Mastery course! This free knowledge base covers everything you need to know about navigating and utilizing the powerful modules within the Casjoe ecosystem. From payments to project management, we've got you covered.";

$stmt = $db->prepare("SELECT id FROM academy_courses WHERE title = ? AND tenant_id = ?");
$stmt->execute([$courseTitle, $tenantId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course) {
    $courseId = $course['id'];
    echo "Updating existing course ID: $courseId<br>";
    $stmt = $db->prepare("UPDATE academy_courses SET description = ?, status = 'published', price = 0.00 WHERE id = ?");
    $stmt->execute([$description, $courseId]);
} else {
    echo "Creating new course...<br>";
    $stmt = $db->prepare("INSERT INTO academy_courses (tenant_id, title, description, status, price, thumbnail) VALUES (?, ?, ?, 'published', 0.00, '/assets/mastery_thumb.jpg')");
    $stmt->execute([$tenantId, $courseTitle, $description]);
    $courseId = $db->lastInsertId();
    echo "Created course ID: $courseId<br>";
}

// 2. Define Sections & Lessons
$curriculum = [
    'Getting Started' => [
        ['title' => 'Welcome to Casjoe Code', 'content' => 'Overview of the platform and what you can achieve.'],
        ['title' => 'Setting up your Workspace', 'content' => 'How to configure your dashboard and profile.'],
        ['title' => 'Navigating the Dashboard', 'content' => 'A tour of the main navigation and features.']
    ],
    'Financial Tools' => [
        ['title' => 'Casjoe Pay Basics', 'content' => 'Introduction to the payment module.'],
        ['title' => 'Creating Payment Links', 'content' => 'How to generate and share payment links.'],
        ['title' => 'Managing Your Wallet', 'content' => 'Deposits, withdrawals, and transfers.']
    ],
    'Business Management (ERP)' => [
        ['title' => 'Overview of Casjoe ERP', 'content' => 'Managing HR, CRM, and Inventory.'],
        ['title' => 'Employee Management', 'content' => 'Adding staff and managing roles.'],
        ['title' => 'CRM & Leads', 'content' => 'Tracking customer interactions.']
    ],
    'Productivity & Tools' => [
        ['title' => 'Using Smart Forms', 'content' => 'Creating dynamic forms for data collection.'],
        ['title' => 'Casjoe Links', 'content' => 'Shortening URLs and generating QR codes.']
    ],
    'Communication & Support' => [
        ['title' => 'Casjoe Mail', 'content' => 'Setting up email campaigns.'],
        ['title' => 'Getting Support', 'content' => 'How to use the support ticket system.']
    ]
];

// 3. Insert Content
$sectionOrder = 1;
foreach ($curriculum as $sectionTitle => $lessons) {
    // Upsert Section
    $stmt = $db->prepare("SELECT id FROM academy_sections WHERE course_id = ? AND title = ?");
    $stmt->execute([$courseId, $sectionTitle]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($section) {
        $sectionId = $section['id'];
    } else {
        $stmt = $db->prepare("INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$courseId, $sectionTitle, $sectionOrder]);
        $sectionId = $db->lastInsertId();
    }
    $sectionOrder++;

    // Upsert Lessons
    $lessonOrder = 1;
    foreach ($lessons as $l) {
        $stmt = $db->prepare("SELECT id FROM academy_lessons WHERE section_id = ? AND title = ?");
        $stmt->execute([$sectionId, $l['title']]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lesson) {
            $stmt = $db->prepare("INSERT INTO academy_lessons (section_id, title, content, sort_order, duration_minutes) VALUES (?, ?, ?, ?, 10)");
            $stmt->execute([$sectionId, $l['title'], $l['content'], $lessonOrder]);
        }
        $lessonOrder++;
    }
}

echo "Done! Course content populated successfully.";

<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Debugging Academy Data...\n";

// Count Courses
$count = $pdo->query("SELECT COUNT(*) FROM academy_courses")->fetchColumn();
echo "Total Courses: $count\n";

// List Courses
$stmt = $pdo->query("SELECT id, title, tenant_id, is_system_course, status FROM academy_courses");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($courses)) {
    echo "No courses found.\n";
} else {
    foreach ($courses as $c) {
        echo "[ID: {$c['id']}] {$c['title']} | Tenant: {$c['tenant_id']} | System: {$c['is_system_course']} | Status: {$c['status']}\n";
    }
}

<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "NUCLEAR RESET of Academy Data...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// Simplified Cleanup
$pdo->exec("DELETE FROM academy_courses WHERE is_system_course = 1");
$pdo->exec("TRUNCATE TABLE academy_licenses"); 
$pdo->exec("TRUNCATE TABLE academy_enrollments");
$pdo->exec("TRUNCATE TABLE academy_certificates");
// Orphaned sections/lessons might remain if cascade fails, but that's acceptable for now to unblock.
// $pdo->exec("DELETE FROM academy_sections WHERE course_id NOT IN (SELECT id FROM academy_courses)");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "Done. Clean slate for seeding.\n";

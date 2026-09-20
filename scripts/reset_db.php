<?php
require_once __DIR__ . '/../app/core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$pdo->exec("DROP TABLE IF EXISTS academy_enrollments");
$pdo->exec("DROP TABLE IF EXISTS academy_lessons");
$pdo->exec("DROP TABLE IF EXISTS academy_sections");
$pdo->exec("DROP TABLE IF EXISTS academy_courses");
echo "Academy Tables Dropped.\n";

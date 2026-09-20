<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Ensuring all columns exist...\n";

$columns = [
    "ALTER TABLE academy_courses ADD COLUMN category varchar(100) DEFAULT 'General'",
    "ALTER TABLE academy_courses ADD COLUMN price_per_seat decimal(10,2) DEFAULT 0.00",
    "ALTER TABLE academy_courses ADD COLUMN is_system_course boolean DEFAULT 1",
    "ALTER TABLE academy_courses ADD COLUMN thumbnail varchar(255) DEFAULT '/assets/course_placeholder.jpg'"
];

foreach ($columns as $sql) {
    try {
        $pdo->exec($sql);
        echo "Executed: $sql\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), "Duplicate column") !== false) {
             // exists
        } else {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "Columns checked.\n";

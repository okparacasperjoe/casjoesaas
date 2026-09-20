<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // Student Progress Tracking
    $db->query("CREATE TABLE IF NOT EXISTS intro_student_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lesson_id INT NOT NULL,
        course_id INT NOT NULL, -- Denormalized for faster course % calculation
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_progress (user_id, lesson_id),
        INDEX (user_id),
        INDEX (course_id)
    )");

    echo "Schema updated: intro_student_progress table created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}

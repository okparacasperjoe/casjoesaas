<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // 1. Quizzes (Linked to a specific course)
    $db->query("CREATE TABLE IF NOT EXISTS intro_quizzes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        passing_score INT DEFAULT 70,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE,
        INDEX (course_id)
    )");

    // 2. Quiz Questions
    $db->query("CREATE TABLE IF NOT EXISTS intro_quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quiz_id INT NOT NULL,
        question_text TEXT NOT NULL,
        options JSON NOT NULL, -- Array of ['text', 'is_correct'] or just strings with a separate correct_index
        correct_option_index INT NOT NULL, -- 0-based index of the correct option
        FOREIGN KEY (quiz_id) REFERENCES intro_quizzes(id) ON DELETE CASCADE,
        INDEX (quiz_id)
    )");

    // 3. Quiz Attempts (Student tracking)
    $db->query("CREATE TABLE IF NOT EXISTS intro_quiz_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        quiz_id INT NOT NULL,
        score INT NOT NULL, -- Percentage
        passed BOOLEAN DEFAULT FALSE,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (quiz_id) REFERENCES intro_quizzes(id) ON DELETE CASCADE,
        INDEX (user_id),
        INDEX (quiz_id)
    )");

    echo "Schema updated: intro_quizzes, intro_quiz_questions, intro_quiz_attempts tables created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}

<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Setting up Academy B2B Schema...\n";

// 1. Licenses Table (B2B Purchasing)
$pdo->exec("CREATE TABLE IF NOT EXISTS `academy_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `seats_total` int(11) DEFAULT 0,
  `seats_used` int(11) DEFAULT 0,
  `purchased_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`course_id`) REFERENCES `academy_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 2. Quizzes Table
$pdo->exec("CREATE TABLE IF NOT EXISTS `academy_quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `passing_score` int(11) DEFAULT 70,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`course_id`) REFERENCES `academy_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 3. Quiz Questions
$pdo->exec("CREATE TABLE IF NOT EXISTS `academy_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `options` text NOT NULL COMMENT 'JSON array of options',
  `correct_option` int(11) NOT NULL, -- Index of correct option
  `points` int(11) DEFAULT 10,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`quiz_id`) REFERENCES `academy_quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 4. Certificates
$pdo->exec("CREATE TABLE IF NOT EXISTS `academy_certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `license_id` int(11) DEFAULT NULL,
  `certificate_code` varchar(50) NOT NULL UNIQUE,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 5. Update Courses Table for System Catalog
try {
    $pdo->exec("ALTER TABLE academy_courses ADD COLUMN price_per_seat decimal(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE academy_courses ADD COLUMN is_system_course boolean DEFAULT 1");
    $pdo->exec("ALTER TABLE academy_courses ADD COLUMN category varchar(100) DEFAULT 'General'");
} catch(PDOException $e) {
    // Ignore if exists
    echo "Columns might already exist: " . $e->getMessage() . "\n";
}

echo "Schema Updated Successfully!\n";

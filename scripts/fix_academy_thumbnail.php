<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Fixing Academy Schema...\n";

try {
    // Attempt to add 'thumbnail' column
    echo "Adding thumbnail column to academy_courses...\n";
    $pdo->exec("ALTER TABLE academy_courses ADD COLUMN thumbnail varchar(255) DEFAULT '/assets/course_placeholder.jpg'");
    echo "Column 'thumbnail' added successfully.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'thumbnail' already exists.\n";
    } else {
        echo "Error adding column: " . $e->getMessage() . "\n";
    }
}

echo "Fix applied.\n";

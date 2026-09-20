<?php

require_once 'app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Checking schema for 'academy_courses'...\n";

try {
    // Attempt to add the column. strict sql might fail if it exists, or we can check first.
    // Easiest is to try adding it and catch exception, or use a check.
    
    // Check if column exists
    $check = $pdo->query("SHOW COLUMNS FROM academy_courses LIKE 'status'");
    if ($check->rowCount() == 0) {
        echo "Column 'status' missing. Adding it...\n";
        $pdo->exec("ALTER TABLE academy_courses ADD COLUMN status ENUM('draft', 'published') DEFAULT 'draft' AFTER price");
        echo "Column 'status' added successfully.\n";
    } else {
        echo "Column 'status' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}

<?php
// Migration script to add created_by_user column to erp_tasks table
require_once __DIR__ . '/../../Core/Database.php';

try {
    $db = App\Core\Database::getInstance()->getConnection();
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM erp_tasks LIKE 'created_by_user'");
    if ($stmt->rowCount() > 0) {
        echo "Column 'created_by_user' already exists. No migration needed.\n";
        exit(0);
    }
    
    // Add the column
    $db->exec("ALTER TABLE erp_tasks ADD COLUMN created_by_user INT DEFAULT NULL AFTER assigned_to");
    
    echo "✓ Successfully added 'created_by_user' column to erp_tasks table.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

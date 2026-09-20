<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // Add columns to shop_products if they don't exist
    $columnsToAdd = [
        "ADD COLUMN stock_quantity INT DEFAULT 0",
        "ADD COLUMN low_stock_threshold INT DEFAULT 5",
        "ADD COLUMN sku VARCHAR(50) DEFAULT NULL",
        "ADD COLUMN is_digital TINYINT(1) DEFAULT 0",
        "ADD COLUMN file_url VARCHAR(255) DEFAULT NULL", // Direct link or protected path
        "ADD COLUMN download_limit INT DEFAULT NULL" // Max downloads enabled?
    ];

    foreach ($columnsToAdd as $sql) {
        try {
            $db->query("ALTER TABLE shop_products $sql");
            echo "Executed: ALTER TABLE shop_products $sql\n";
        } catch (PDOException $e) {
            // Check if error is "Duplicate column name" (code 42S21) to ignore
            if ($e->getCode() == '42S21') {
                echo "Column already exists, skipping: $sql\n";
            } else {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }

    // New Table: Shop POS Sessions (for Point of Sale)
    $db->query("CREATE TABLE IF NOT EXISTS shop_pos_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        user_id INT NOT NULL, -- Cashier
        opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        closed_at TIMESTAMP NULL,
        starting_cash DECIMAL(10, 2) DEFAULT 0.00,
        ending_cash DECIMAL(10, 2) DEFAULT 0.00,
        status ENUM('open', 'closed') DEFAULT 'open',
        INDEX (tenant_id),
        INDEX (user_id)
    )");
    echo "Schema updated: shop_pos_sessions table created.\n";

} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}

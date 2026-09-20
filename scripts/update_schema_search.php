<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Add category column if it doesn't exist
    $columns = $db->query("SHOW COLUMNS FROM shop_products LIKE 'category'")->fetchAll();
    if (empty($columns)) {
        $db->exec("ALTER TABLE shop_products ADD COLUMN category VARCHAR(100) NULL AFTER name");
        echo "Added category column to shop_products.\n";
    } else {
        echo "Category column already exists.\n";
    }

    // Add indexes for performance
    try {
        $db->exec("CREATE INDEX idx_shop_products_price ON shop_products(price)");
        $db->exec("CREATE INDEX idx_shop_products_category ON shop_products(category)");
        $db->exec("CREATE FULLTEXT INDEX idx_shop_products_search ON shop_products(name, description)");
         echo "Added indexes.\n";
    } catch (Exception $e) {
        // Indexes might already exist, ignore
        echo "Indexes might already exist: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

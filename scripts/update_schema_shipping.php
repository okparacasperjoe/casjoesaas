<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // 1. Create shop_shipping_zones table
    $db->exec("CREATE TABLE IF NOT EXISTS shop_shipping_zones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT NOT NULL,
        zone_name VARCHAR(255) NOT NULL,
        cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vendor_id) REFERENCES shop_vendors(id) ON DELETE CASCADE
    )");
    echo "Created shop_shipping_zones table.\n";

    // 2. Add shipping_cost to shop_orders
    $stmt = $db->query("SHOW COLUMNS FROM shop_orders LIKE 'shipping_cost'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_orders ADD COLUMN shipping_cost DECIMAL(10,2) DEFAULT 0.00 AFTER service_fee");
        echo "Added shipping_cost to shop_orders.\n";
    }

    // 3. Add shipping_zone_name to shop_orders (for record keeping)
    $stmt = $db->query("SHOW COLUMNS FROM shop_orders LIKE 'shipping_zone_name'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_orders ADD COLUMN shipping_zone_name VARCHAR(255) NULL AFTER shipping_cost");
        echo "Added shipping_zone_name to shop_orders.\n";
    }

    echo "Schema updated successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

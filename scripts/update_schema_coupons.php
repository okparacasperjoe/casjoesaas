<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // 1. Create shop_coupons table
    $db->exec("CREATE TABLE IF NOT EXISTS shop_coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        vendor_id INT NOT NULL,
        code VARCHAR(50) NOT NULL,
        type ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent',
        value DECIMAL(10,2) NOT NULL,
        min_spend DECIMAL(10,2) DEFAULT 0.00,
        usage_limit INT DEFAULT 0, -- 0 = unlimited
        used_count INT DEFAULT 0,
        expires_at DATE NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_code (tenant_id, code),
        FOREIGN KEY (vendor_id) REFERENCES shop_vendors(id) ON DELETE CASCADE
    )");
    echo "Created shop_coupons table.\n";

    // 2. Add discount fields to shop_orders
    $stmt = $db->query("SHOW COLUMNS FROM shop_orders LIKE 'discount_amount'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_orders ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER shipping_cost");
        echo "Added discount_amount to shop_orders.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM shop_orders LIKE 'coupon_code'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_orders ADD COLUMN coupon_code VARCHAR(50) NULL AFTER discount_amount");
        echo "Added coupon_code to shop_orders.\n";
    }

    echo "Schema updated successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

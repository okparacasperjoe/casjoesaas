<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Check if column exists to avoid error on re-run
    $stmt = $db->query("SHOW COLUMNS FROM shop_vendors LIKE 'fee_bearer'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_vendors ADD COLUMN fee_bearer ENUM('merchant', 'customer') DEFAULT 'merchant'");
        echo "Added fee_bearer to shop_vendors.\n";
    }

    $stmt = $db->query("SHOW COLUMNS FROM shop_orders LIKE 'service_fee'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE shop_orders ADD COLUMN service_fee DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount");
        echo "Added service_fee to shop_orders.\n";
    }

    echo "Schema updated successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

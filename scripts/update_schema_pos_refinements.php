<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

try {
    $db->query("ALTER TABLE shop_orders ADD COLUMN discount_amount DECIMAL(10, 2) DEFAULT 0.00");
    // Ensure payment_method is big enough or ENUM includes 'card'
    // Actually, let's just make it VARCHAR just in case it was enum. To be safe.
    $db->query("ALTER TABLE shop_orders MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cash'");
    
    echo "Schema updated: discount_amount added, payment_method modified.\n";
} catch (PDOException $e) {
    echo "Error (likely exists): " . $e->getMessage() . "\n";
}

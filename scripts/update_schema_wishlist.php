<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Create shop_wishlists table
    $db->exec("CREATE TABLE IF NOT EXISTS shop_wishlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_wishlist (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES shop_products(id) ON DELETE CASCADE
    )");
    echo "Created shop_wishlists table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

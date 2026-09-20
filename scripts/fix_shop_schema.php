<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

try {
    $db->query("ALTER TABLE shop_products ADD COLUMN image_path VARCHAR(255) DEFAULT NULL");
    $db->query("ALTER TABLE shop_products ADD COLUMN type ENUM('physical', 'digital') DEFAULT 'physical'");
    echo "Schema fixed: image_path and type added.\n";
} catch (PDOException $e) {
    echo "Error (likely exists): " . $e->getMessage() . "\n";
}

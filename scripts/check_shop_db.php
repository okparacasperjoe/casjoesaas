<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
echo "Checking Shop Tables...\n";
$tables = ['shop_products', 'shop_vendors', 'shop_orders', 'shop_order_items', 'shop_digital_access'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "[OK] Table `$table` exists with $count rows.\n";
    } catch (PDOException $e) {
        echo "[MISSING] Table `$table` does not exist.\n";
    }
}

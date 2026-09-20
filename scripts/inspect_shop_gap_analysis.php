<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
echo "Inspecting shop_products columns:\n";
try {
    $stmt = $pdo->query("DESCRIBE shop_products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Table shop_products error: " . $e->getMessage() . "\n";
}

echo "\nInspecting shop_orders columns:\n";
try {
    $stmt = $pdo->query("DESCRIBE shop_orders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Table shop_orders error: " . $e->getMessage() . "\n";
}

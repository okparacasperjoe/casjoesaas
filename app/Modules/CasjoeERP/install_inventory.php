<?php
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/inventory_install.sql');

try {
    $db->exec($sql);
    echo "Inventory tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing inventory tables: " . $e->getMessage() . "\n";
}

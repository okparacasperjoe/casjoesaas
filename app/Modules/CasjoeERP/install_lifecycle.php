<?php
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/lifecycle_install.sql');

try {
    $db->exec($sql);
    echo "Lifecycle tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing lifecycle tables: " . $e->getMessage() . "\n";
}

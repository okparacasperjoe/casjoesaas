<?php
require_once __DIR__ . '/app/core/Database.php';
use App\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/app/core/sql/system_settings_install.sql');
    $pdo->exec($sql);
    echo "System settings table created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit(1);
}

<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();

try {
    $db->query("ALTER TABLE users ADD COLUMN currency VARCHAR(10) DEFAULT 'USD'");
    echo "Schema updated: currency added to users.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

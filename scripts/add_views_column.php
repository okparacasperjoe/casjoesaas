<?php
require_once __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Adding 'views' column to cp_payment_links...\n";
    $pdo->exec("ALTER TABLE cp_payment_links ADD COLUMN views INT DEFAULT 0");
    echo "Column 'views' added successfully.\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'views' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

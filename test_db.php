<?php
// Test Database Connection
$config = require __DIR__ . '/config/database.php';

echo "Attempting connection to: " . $config['host'] . "\n";
echo "Database: " . $config['dbname'] . "\n";
echo "User: " . $config['username'] . "\n";

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection Successful!";
} catch (PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
}

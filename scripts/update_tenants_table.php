<?php
require_once __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Updating tenants table for Onboarding Flow...\n";

    // Add columns if they don't exist
    $columns = [
        "ADD COLUMN business_type VARCHAR(50) NULL",
        "ADD COLUMN currency VARCHAR(3) DEFAULT 'USD'",
        "ADD COLUMN onboarding_step INT DEFAULT 1",
        "ADD COLUMN onboarding_intent TEXT NULL"
    ];

    foreach ($columns as $col) {
        try {
            $pdo->exec("ALTER TABLE tenants $col");
            echo "Executed: ALTER TABLE tenants $col\n";
        } catch (PDOException $e) {
            // Ignore if column exists (SQLSTATE 42S21)
            if ($e->getCode() == '42S21') {
                echo "Column already exists, skipping.\n";
            } else {
                echo "Error adding column: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "Tenants table updated successfully.\n";

} catch (PDOException $e) {
    echo "Connection Error: " . $e->getMessage() . "\n";
}

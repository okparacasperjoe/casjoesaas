<?php
require 'app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();

echo "Updating Schema...\n";
try {
    $pdo->exec("ALTER TABLE cm_subscribers ADD COLUMN company VARCHAR(255) DEFAULT NULL");
    echo "Added company\n";
} catch (Exception $e) { echo "Company: " . $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE cm_subscribers ADD COLUMN phone VARCHAR(50) DEFAULT NULL");
    echo "Added phone\n";
} catch (Exception $e) { echo "Phone: " . $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE cm_subscribers ADD COLUMN tags VARCHAR(255) DEFAULT NULL");
    echo "Added tags\n";
} catch (Exception $e) { echo "Tags: " . $e->getMessage() . "\n"; }

echo "Schema Update Attempt Finished.\n";

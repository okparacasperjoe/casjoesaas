<?php
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/payroll_install.sql');

try {
    // Split SQL by semicolon to execute statements individually if needed, 
    // or just exec all if the driver supports multiple statements. 
    // PDO::exec might fail on multiple statements depending on config, but usually works for simple schema.
    $db->exec($sql);
    echo "Payroll tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing payroll tables: " . $e->getMessage() . "\n";
}

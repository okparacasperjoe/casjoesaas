<?php
// Enable Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>System Diagnostic</h1>";

// 1. Check File Structure
echo "<h2>1. File Check</h2>";
$bootstrap = __DIR__ . '/../app/Core/bootstrap.php';
$autoloader = __DIR__ . '/../vendor/autoload.php';

if (file_exists($bootstrap)) {
    echo "<p style='color:green'>[OK] Bootstrap found at: $bootstrap</p>";
} else {
    echo "<p style='color:red'>[ERROR] Bootstrap NOT found at: $bootstrap</p>";
}

if (file_exists($autoloader)) {
    echo "<p style='color:green'>[OK] Vendor Autoloader found at: $autoloader</p>";
} else {
    echo "<p style='color:red'>[ERROR] Vendor Autoloader NOT found at: $autoloader</p>";
}

// 2. Check Database
echo "<h2>2. Database Check</h2>";
try {
    $config_path = __DIR__ . '/../config/database.php';
    if (!file_exists($config_path)) {
        throw new Exception("Config file missing at: $config_path");
    }
    
    $config = require $config_path;
    echo "<p>Attempting connection to <strong>{$config['host']}</strong>...</p>";
    
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "<p style='color:green'>[OK] Database Connection Successful!</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>[FAIL] Database Error: " . $e->getMessage() . "</p>";
}

<?php
// Mock Session
session_start();
$_SESSION['user_id'] = 1;

// Define constants
define('APP_START', microtime(true));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Mock TenantContext
class MockTenantContext {
    public static function getTenantId() { return 1; }
}
$_SERVER['HTTP_HOST'] = 'localhost'; 

use App\Core\Database;
use App\Core\Controllers\SecurityController;

function capture($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
}

$db = Database::getInstance()->getConnection();

echo "Starting Verification (Security Page)...\n";

// 1. Ensure User 1 exists and reset 2FA
echo "Resetting User 1 2FA status...\n";
$db->prepare("UPDATE users SET two_factor_enabled = 0, two_factor_secret = NULL WHERE id = 1")->execute();

// 2. Test Case 1: 2FA Disabled
echo "\nTest Case 1: 2FA Disabled\n";
$controller = new SecurityController();

$output = capture(function() use ($controller) {
    $controller->index();
});

if (strpos($output, 'Secret:') !== false && strpos($output, 'Setup 2FA') !== false) {
    echo "PASS: 2FA Setup UI shown.\n";
} else {
    echo "FAIL: 2FA Setup UI NOT shown.\n"; // . substr($output, 0, 500);
}

if (strpos($output, 'Disable 2FA') === false) {
    echo "PASS: Disable button NOT shown.\n";
} else {
    echo "FAIL: Disable button SHOWN unexpectedly.\n";
}


// 3. Test Case 2: 2FA Enabled
echo "\nTest Case 2: 2FA Enabled\n";
// Manually enable in DB
$db->prepare("UPDATE users SET two_factor_enabled = 1, two_factor_secret = 'TESTSECRET' WHERE id = 1")->execute();

$output = capture(function() use ($controller) {
    $controller->index();
});

if (strpos($output, 'Disable 2FA') !== false) {
    echo "PASS: Disable button shown.\n";
} else {
    echo "FAIL: Disable button NOT shown.\n";
}

if (strpos($output, 'Setup 2FA') === false) {
    echo "PASS: Setup UI NOT shown.\n";
} else {
    echo "FAIL: Setup UI SHOWN unexpectedly.\n";
}

echo "\nVerification Complete.\n";

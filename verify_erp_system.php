<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;
use App\Modules\CasjoeERP\Controllers\SystemController;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
$property->setAccessible(true);
$property->setValue(null, 1);

$step = $argv[1] ?? 'all';

function capture($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
}

echo "Checking System Module...\n";

if ($step === 'init' || $step === 'all') {
    $db = Database::getInstance()->getConnection();
    // Check tables
    $count = $db->query("SELECT COUNT(*) FROM erp_settings")->fetchColumn();
    if ($count > 0) echo "PASS: Settings table populated.\n";
    else echo "FAIL: Settings empty.\n";

    $countA = $db->query("SELECT COUNT(*) FROM erp_activity_log")->fetchColumn();
    if ($countA > 0) echo "PASS: Activity Log table populated.\n";
    else echo "FAIL: Activity Log empty.\n";
}

if ($step === 'check' || $step === 'all') {
    $controller = new SystemController();

    // Check Settings View
    $output = capture(function() use ($controller) {
        $controller->settings();
    });
    if (strpos($output, 'System Settings') !== false) {
        if (strpos($output, 'My SaaS Company') !== false) echo "PASS: Settings view rendered with data.\n";
        else echo "FAIL: Settings view rendered but missing specific data.\n";
    } else echo "FAIL: Settings view failed.\n";

    // Check Activity View
    $output2 = capture(function() use ($controller) {
        $controller->activity();
    });
    if (strpos($output2, 'Activity Logs') !== false) {
        if (strpos($output2, 'system_init') !== false) echo "PASS: Activity view rendered with loaded logs.\n";
        else echo "FAIL: Activity view rendered but missing logs.\n";
    } else echo "FAIL: Activity view failed.\n";
}

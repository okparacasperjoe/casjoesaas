<?php
session_start();
$_SESSION['user_id'] = 1;

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

use App\Core\Database;
use App\Modules\CasjoeERP\Controllers\FinanceController;

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

echo "Checking Finance Module...\n";

if ($step === 'init' || $step === 'all') {
    // Rely on seed data or check if it exists
    $db = Database::getInstance()->getConnection();
    $count = $db->query("SELECT COUNT(*) FROM erp_inventory")->fetchColumn();
    if ($count > 0) echo "PASS: Inventory table populated.\n";
    else echo "FAIL: Inventory empty.\n";
    
    $countT = $db->query("SELECT COUNT(*) FROM erp_transactions")->fetchColumn();
    if ($countT > 0) echo "PASS: Transactions table populated.\n";
    else echo "FAIL: Transactions empty.\n";
}

if ($step === 'check' || $step === 'all') {
    $controller = new FinanceController();

    $output = capture(function() use ($controller) {
        $controller->inventory();
    });
    if (strpos($output, 'Inventory / Stock') !== false) {
        if (strpos($output, 'Office Laptop') !== false) echo "PASS: Inventory view rendered with data.\n";
        else echo "FAIL: Inventory view rendered but missing data.\n";
    } else echo "FAIL: Inventory view failed.\n";   

    $output2 = capture(function() use ($controller) {
        $controller->finance();
    });
    if (strpos($output2, 'Financial Overview') !== false) {
        if (strpos($output2, '$5,000.00') !== false) echo "PASS: Dashboard view rendered with correct totals.\n";
        else echo "FAIL: Dashboard view rendered but wrong totals.\n";
    } else echo "FAIL: Dashboard view failed.\n";
}

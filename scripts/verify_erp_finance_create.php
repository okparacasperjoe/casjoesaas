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
$db = Database::getInstance()->getConnection();

if ($step === 'create' || $step === 'all') {
    echo "Creating Inventory Item...\n";
    $_POST = [
        'item_name' => 'Test Laptop',
        'sku' => 'TEST-001',
        'quantity' => 50,
        'unit_price' => 999.99
    ];
    $c = new FinanceController();
    $c->storeInventory(); // Will exit
} elseif ($step === 'check') {
    echo "Checking Inventory...\n";
    $item = $db->query("SELECT * FROM erp_inventory WHERE sku = 'TEST-001'")->fetch(PDO::FETCH_ASSOC);
    if ($item) echo "PASS: Inventory item created.\n";
    else echo "FAIL: Inventory item missing.\n";
}

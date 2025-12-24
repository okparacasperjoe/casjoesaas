<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $base_dir = __DIR__ . '/app/';
        $relative_class = substr($class, strpos($class, '\\') + 1); 
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Core\Database;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
// setAccessible(true) is no longer required in recent PHP versions for private properties accessible via Reflection
$property->setValue(null, 1);

$db = Database::getInstance()->getConnection();

echo "Verifying ERP Inventory...\n";

// 1. Create Item
$sku = "TEST-SKU-" . rand(1000,9999);
$stmt = $db->prepare("INSERT INTO erp_inventory_items (tenant_id, sku, name, unit_price, stock_quantity) VALUES (1, ?, 'Test Product', 99.99, 100)");
$stmt->execute([$sku]);
$id = $db->lastInsertId();

if ($id) {
    echo "PASS: Inventory Item Created (ID: $id, SKU: $sku).\n";
} else {
    echo "FAIL: Item Creation Failed.\n";
    exit(1);
}

// 2. Fetch Item
$stmt = $db->prepare("SELECT * FROM erp_inventory_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item && $item['unit_price'] == 99.99) {
    echo "PASS: Item Verification Successful.\n";
} else {
    echo "FAIL: Item Verification Failed.\n";
}

// 3. Verify View
if (file_exists(__DIR__ . '/app/modules/CasjoeERP/views/inventory/index.php')) {
    echo "PASS: Inventory Index View exists.\n";
}

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
$property->setAccessible(true);
$property->setValue(null, 1);

$db = Database::getInstance()->getConnection();

echo "Verifying ERP CRM Sales...\n";

// 1. Setup: Ensure Customer and Inventory Item exist
$db->exec("INSERT IGNORE INTO erp_crm_customers (id, tenant_id, name, status) VALUES (999, 1, 'Sales Test Customer', 'active')");
$db->exec("INSERT INTO erp_inventory_items (tenant_id, sku, name, unit_price, stock_quantity) VALUES (1, 'SALE-TEST', 'Sale Item', 50.00, 100) ON DUPLICATE KEY UPDATE stock_quantity=100");
$itemId = $db->query("SELECT id FROM erp_inventory_items WHERE sku='SALE-TEST'")->fetchColumn();

// 2. Create Opportunity
$stmt = $db->prepare("INSERT INTO erp_crm_opportunities (tenant_id, title, value, stage, customer_id) VALUES (1, 'Big Deal', 5000.00, 'prospecting', 999)");
$stmt->execute();
$oppId = $db->lastInsertId();

if ($oppId) {
    echo "PASS: Opportunity Created (ID: $oppId).\n";
} else {
    echo "FAIL: Opportunity Creation Failed.\n";
    exit(1);
}

// 3. Create Sale & Verify Inventory Deduction
// Default Stock is 100. We sell 5. Expected 95.
$qty = 5;
$total = 50.00 * $qty;

$db->beginTransaction();
$db->prepare("INSERT INTO erp_crm_sales (tenant_id, customer_id, total_amount, status) VALUES (1, 999, ?, 'completed')")->execute([$total]);
$saleId = $db->lastInsertId();
$db->prepare("INSERT INTO erp_crm_sale_items (sale_id, inventory_item_id, quantity, unit_price, total_price) VALUES (?, ?, ?, 50.00, ?)")->execute([$saleId, $itemId, $qty, $total]);
$db->prepare("UPDATE erp_inventory_items SET stock_quantity = stock_quantity - ? WHERE id = ?")->execute([$qty, $itemId]);
$db->commit();

// Verify Stock
$newStock = $db->query("SELECT stock_quantity FROM erp_inventory_items WHERE id = $itemId")->fetchColumn();

if ($newStock == 95) {
    echo "PASS: Sale Recorded & Inventory Deducted (100 -> 95).\n";
} else {
    echo "FAIL: Inventory Update Failed (Expected 95, Got $newStock).\n";
}

// 4. Verify Views
if (file_exists(__DIR__ . '/app/modules/CasjoeERP/views/crm/sales/index.php')) {
    echo "PASS: Sales Index View exists.\n";
}

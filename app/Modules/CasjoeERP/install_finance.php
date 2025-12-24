<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/finance_install.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "Finance Module installed successfully.\n";

    // Seed some data if empty
    $count = $db->query("SELECT COUNT(*) FROM erp_inventory")->fetchColumn();
    if ($count == 0) {
        echo "Seeding default finance data...\n";
        $tenantId = 1; 
        
        $db->exec("INSERT INTO erp_inventory (tenant_id, item_name, sku, quantity, unit_price) VALUES 
            ($tenantId, 'Office Laptop', 'DL-5500', 5, 1200.00),
            ($tenantId, 'Ergonomic Chair', 'HM-AERON', 10, 800.00)
        ");
        
        $db->exec("INSERT INTO erp_assets (tenant_id, asset_name, value, status) VALUES 
            ($tenantId, 'Company Van', 25000.00, 'active')
        ");
        
        $db->exec("INSERT INTO erp_transactions (tenant_id, description, amount, type, date) VALUES 
            ($tenantId, 'Client Payment', 5000.00, 'income', CURDATE()),
            ($tenantId, 'Office Rent', 2000.00, 'expense', CURDATE())
        ");
        
        echo "Seeding complete.\n";
    }

} catch (Exception $e) {
    die("Error installing Finance module: " . $e->getMessage() . "\n");
}

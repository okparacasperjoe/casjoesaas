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

echo "Verifying ERP CRM...\n";

// 1. Create Customer
$name = "Acme Corp " . rand(1000,9999);
$stmt = $db->prepare("INSERT INTO erp_crm_customers (tenant_id, name, company, status) VALUES (1, ?, 'Acme Inc', 'active')");
$stmt->execute([$name]);
$custId = $db->lastInsertId();

if ($custId) {
    echo "PASS: Customer Created (ID: $custId, Name: $name).\n";
} else {
    echo "FAIL: Customer Creation Failed.\n";
    exit(1);
}

// 2. Create Lead
$leadName = "John Doe Lead " . rand(1000,9999);
$stmt = $db->prepare("INSERT INTO erp_crm_leads (tenant_id, name, source, status) VALUES (1, ?, 'Website', 'new')");
$stmt->execute([$leadName]);
$leadId = $db->lastInsertId();

if ($leadId) {
    echo "PASS: Lead Created (ID: $leadId).\n";
} else {
    echo "FAIL: Lead Creation Failed.\n";
    exit(1);
}

// 3. Verify Views
if (file_exists(__DIR__ . '/app/modules/CasjoeERP/views/crm/customers/index.php')) {
    echo "PASS: Customer Index View exists.\n";
}
if (file_exists(__DIR__ . '/app/modules/CasjoeERP/views/crm/leads/index.php')) {
    echo "PASS: Lead Index View exists.\n";
}

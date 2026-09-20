require_once __DIR__ . '/../app/Core/bootstrap.php';

$db = \App\Core\Database::getInstance();
$tenantId = \App\Core\TenantContext::getTenantId();

echo "<h1>Production Module Fixer</h1>";
echo "<pre>";

try {
    // 1. Ensure 'modules' table exists and populate it
    echo "1. Checking 'modules' table...\n";
    $modules = [
        ['name' => 'Casjoe Pay', 'slug' => 'casjoe_pay'],
        ['name' => 'Casjoe ERP', 'slug' => 'casjoe_erp'],
        ['name' => 'Casjoe Links', 'slug' => 'casjoe_links'],
        ['name' => 'Casjoe Mail', 'slug' => 'casjoe_mail'],
        ['name' => 'Casjoe SmartForms', 'slug' => 'casjoe_forms'],
        ['name' => 'Casjoe Academy', 'slug' => 'casjoe_academy'] // Assuming Academy exists
    ];

    foreach ($modules as $mod) {
        $stmt = $db->query("SELECT id FROM modules WHERE slug = ?", [$mod['slug']]);
        if (!$stmt->fetch()) {
            $db->query("INSERT INTO modules (name, slug) VALUES (?, ?)", [$mod['name'], $mod['slug']]);
            echo " - Inserted module: {$mod['name']}\n";
        } else {
            echo " - Module exists: {$mod['name']}\n";
        }
    }

    // 2. Ensure current tenant has these modules enabled
    echo "\n2. Enabling modules for Tenant ID: $tenantId ...\n";
    
    // Get all module IDs
    $stmt = $db->query("SELECT id, slug FROM modules");
    $allModules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allModules as $m) {
        $stmt = $db->query("SELECT id FROM tenant_modules WHERE tenant_id = ? AND module_id = ?", [$tenantId, $m['id']]);
        if (!$stmt->fetch()) {
            $db->query("INSERT INTO tenant_modules (tenant_id, module_id, status) VALUES (?, ?, 'enabled')", [$tenantId, $m['id']]);
            echo " - Enabled '{$m['slug']}' for tenant $tenantId\n";
        } else {
            echo " - '{$m['slug']}' already enabled for tenant $tenantId\n";
        }
    }

    echo "\nDone. Modules should now be loadable.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";

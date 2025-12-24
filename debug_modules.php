<?php
require_once __DIR__ . '/app/core/Database.php';

// Mock TenantContext for CLI
class MockTenantContext {
    public static function getTenantId() {
        return 1; // Assuming localhost is ID 1
    }
}

use App\Core\Database;

$db = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Checking enabled modules for Tenant ID: $tenantId\n";

$stmt = $db->prepare("
    SELECT m.slug, tm.status
    FROM tenant_modules tm
    JOIN modules m ON m.id = tm.module_id
    WHERE tm.tenant_id = ?
");
$stmt->execute([$tenantId]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($modules);

echo "\nChecking Module Files:\n";
$modulesDir = __DIR__ . '/app/modules';
$dirs = glob($modulesDir . '/*', GLOB_ONLYDIR);

foreach ($dirs as $dir) {
    if (file_exists($dir . '/module.json')) {
        $config = json_decode(file_get_contents($dir . '/module.json'), true);
        echo "Found: " . $config['slug'] . " in " . basename($dir) . "\n";
    }
}

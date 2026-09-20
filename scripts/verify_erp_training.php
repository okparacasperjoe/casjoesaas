<?php
session_start();
$_SESSION['user_id'] = 1;

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $base_dir = __DIR__ . '/../app/';
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

echo "Verifying ERP Training...\n";

// 1. Create Program
$stmt = $db->prepare("INSERT INTO erp_training_programs (tenant_id, name, instructor, start_date, end_date) VALUES (1, 'Verification Training', 'Dr. Check', '2025-05-01', '2025-05-05')");
$stmt->execute();
$id = $db->lastInsertId();

if ($id) {
    echo "PASS: Training Program Created (ID: $id).\n";
} else {
    echo "FAIL: Program Creation Failed.\n";
    exit(1);
}

// 2. Verify View Exists
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/training/index.php')) {
    echo "PASS: Index view exists.\n";
}

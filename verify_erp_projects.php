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
use App\Modules\CasjoeERP\Controllers\ProjectController;

// Mock TenantContext
$reflection = new ReflectionClass('App\\Core\\TenantContext');
$property = $reflection->getProperty('tenantId');
$property->setAccessible(true);
$property->setValue(null, 1);

$step = $argv[1] ?? 'all';
$db = Database::getInstance()->getConnection();

if ($step === 'init' || $step === 'all') {
    $db->exec("DELETE FROM erp_projects WHERE name = 'Verify Project'");
    echo "Cleaned DB.\n";
    if ($step === 'init') exit;
}

if ($step === 'create' || $step === 'all') {
    echo "Creating Project...\n";
    $_POST = [
        'name' => 'Verify Project',
        'description' => 'A test project'
    ];
    $projController = new ProjectController();
    $projController->storeProject(); // Will exit
}

if ($step === 'check') {
    echo "Checking Results...\n";
    $proj = $db->query("SELECT * FROM erp_projects WHERE name = 'Verify Project'")->fetch(PDO::FETCH_ASSOC);
    if ($proj) echo "PASS: Project created.\n";
    else echo "FAIL: Project missing.\n";
    
    // Check Tasks view access
    $output = capture(function() {
        $c = new ProjectController();
        $c->tasks();
    });
    if (strpos($output, 'My Tasks') !== false) {
        // Also check if tasks from seed are visible (Design Mockups)
        if (strpos($output, 'Design Mockups') !== false) {
             echo "PASS: Tasks view rendered and data found.\n";
        } else {
             echo "FAIL: Tasks view rendered but NO data found.\n";
        }
    }
    else echo "FAIL: Tasks view failed.\n";
}

function capture($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
}

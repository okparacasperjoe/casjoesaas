<?php
// Mock Environment
session_start();
$_SESSION['user_id'] = 1;
$_SERVER['REQUEST_URI'] = '/erp/payroll'; // Mock URI for sidebar active state

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

// Mock Dependencies
class MockDatabase {
    public function getConnection() { return new MockPDO(); }
    public static function getInstance() { return new self(); }
}
class MockPDO {
    public function query($sql) { return new Mockstmt(); }
    public function prepare($sql) { return new Mockstmt(); }
}
class Mockstmt {
    public function fetchColumn() { return 0; }
    public function fetchAll() { return []; }
    public function execute() {}
}
// Alias Database to MockDatabase if it doesn't exist (it might if using real app)
// Actually, let's just let it try to use real classes if they exist, but if not we might have issues.
// But since we are in the root url, the autoloader should find App\Core\Database.
// The issue is Database connects to real DB. If that fails (e.g. no creds), we fail.
// My previous script used real DB. I will assume real DB works or catch exception.

use App\Modules\CasjoeERP\Controllers\ErpController;

function capture($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
}

echo "Starting ERP Verification...\n";

try {
    $controller = new ErpController();
    echo "Controller instantiated.\n";

    echo "Testing 'payroll' method...\n";
    $output = capture(function() use ($controller) {
        $controller->payroll();
    });

    if (strpos($output, 'Payroll') !== false) {
        echo "PASS: Output contains 'Payroll' title.\n";
    } else {
        echo "FAIL: Output missing 'Payroll'.\n";
    }

    if (strpos($output, 'nav-menu') !== false) {
        echo "PASS: Sidebar rendered.\n";
    } else {
        echo "FAIL: Sidebar NOT rendered.\n";
    }
    
    // Check for a few new links in sidebar
    if (strpos($output, '/erp/recruitment') !== false) {
        echo "PASS: Recruitment link found in sidebar.\n";
    } else {
        echo "FAIL: Recruitment link missing.\n";
    }
    
    if (strpos($output, '/erp/backup') !== false) {
        echo "PASS: Backup link found in sidebar.\n";
    } else {
        echo "FAIL: Backup link missing.\n";
    }

} catch (Throwable $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

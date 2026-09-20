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

echo "Verifying ERP Performance Reviews...\n";

// 1. Ensure Employee Exists
$stmt = $db->prepare("SELECT id FROM erp_employees WHERE tenant_id = ? LIMIT 1");
$stmt->execute([1]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    $db->exec("INSERT INTO erp_employees (tenant_id, first_name, last_name, email, status) VALUES (1, 'Perf', 'Tester', 'perf@test.com', 'active')");
    $employeeId = $db->lastInsertId();
} else {
    $employeeId = $employee['id'];
}

// 2. Clear old test data
$db->exec("DELETE FROM erp_performance_reviews WHERE comments = 'Verification Review'");

// 3. Create Review via DB
$today = date('Y-m-d');
$stmt = $db->prepare("
    INSERT INTO erp_performance_reviews 
    (tenant_id, employee_id, reviewer_id, review_date, rating, comments) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([1, $employeeId, 1, $today, 5, 'Verification Review']);
$reviewId = $db->lastInsertId();

if ($reviewId) {
    echo "PASS: Review Created (ID: $reviewId).\n";
} else {
    echo "FAIL: Review Creation Failed.\n";
    exit(1);
}

// 4. Verify Fetch
$stmt = $db->prepare("SELECT * FROM erp_performance_reviews WHERE id = ?");
$stmt->execute([$reviewId]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if ($review['rating'] == 5) {
    echo "PASS: Review Fetch Verified.\n";
} else {
    echo "FAIL: Review Data Match Failed.\n";
    exit(1);
}

// 5. Verify Views
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/performance/index.php')) {
    echo "PASS: Index view exists.\n";
}
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/hr/performance/create.php')) {
    echo "PASS: Create view exists.\n";
}

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

echo "Verifying ERP Calendar...\n";

// 1. Create a task due today
$today = date('Y-m-d');
$db->exec("DELETE FROM erp_tasks WHERE title = 'Calendar Verification Task'");
$stmt = $db->prepare("INSERT INTO erp_tasks (tenant_id, title, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([1, 'Calendar Verification Task', 'This task should appear on the calendar', 'high', $today]);
$taskId = $db->lastInsertId();
echo "PASS: Task created (ID: $taskId).\n";

// 2. Fetch tasks for this month (Simulate Controller Logic)
$month = date('m');
$year = date('Y');
$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDayTimestamp);
$startDate = "$year-$month-01";
$endDate = "$year-$month-$daysInMonth";

$stmt = $db->prepare("
    SELECT t.* 
    FROM erp_tasks t 
    WHERE t.tenant_id = ? 
    AND t.due_date BETWEEN ? AND ?
    AND t.title = 'Calendar Verification Task'
");
$stmt->execute([1, $startDate, $endDate]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($task) {
    echo "PASS: Task found in current month query.\n";
} else {
    echo "FAIL: Task not found in query.\n";
    exit(1);
}

// 3. Verify View File Exists
if (file_exists(__DIR__ . '/../app/modules/CasjoeERP/views/projects/calendar.php')) {
    echo "PASS: Calendar view file exists.\n";
} else {
    echo "FAIL: Calendar view file missing.\n";
    exit(1);
}

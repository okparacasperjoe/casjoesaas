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

echo "Verifying ERP Recruitment...\n";

// 1. Create Job
$stmt = $db->prepare("INSERT INTO erp_job_postings (tenant_id, title, department, description) VALUES (1, 'Verification Dev', 'IT', 'Test Job')");
$stmt->execute();
$jobId = $db->lastInsertId();

if ($jobId) {
    echo "PASS: Job Posted (ID: $jobId).\n";
} else {
    echo "FAIL: Job Posting Failed.\n";
    exit(1);
}

// 2. Apply
$stmt = $db->prepare("INSERT INTO erp_job_applications (tenant_id, job_id, candidate_name, email) VALUES (1, ?, 'Jane Tester', 'jane@test.com')");
$stmt->execute([$jobId]);
$appId = $db->lastInsertId();

if ($appId) {
    echo "PASS: Application Submitted (ID: $appId).\n";
} else {
    echo "FAIL: Application Failed.\n";
    exit(1);
}

// 3. Verify Logic
$stmt = $db->prepare("SELECT count(*) as count FROM erp_job_applications WHERE job_id = ?");
$stmt->execute([$jobId]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($count > 0) {
    echo "PASS: Applications linked to Job.\n";
} else {
    echo "FAIL: Linkage check failed.\n";
}

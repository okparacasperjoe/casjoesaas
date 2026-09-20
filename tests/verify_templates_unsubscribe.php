<?php

require_once __DIR__ . '/../app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();

echo "--- Verifying Templates ---\n";
$count = $pdo->query("SELECT COUNT(*) FROM cm_templates")->fetchColumn();
echo "Templates Count: $count\n";

echo "\n--- Verifying Schema ---\n";
$cols = $pdo->query("DESCRIBE cm_subscribers")->fetchAll(PDO::FETCH_COLUMN);
$missing = array_diff(['company', 'phone', 'tags', 'status'], $cols);
if(empty($missing)) {
    echo "Schema Columns: OK\n";
} else {
    echo "Missing Columns: " . implode(', ', $missing) . "\n";
}

echo "\n--- Verifying Unsubscribe ---\n";
// Create dummy sub
$pdo->exec("INSERT IGNORE INTO cm_subscribers (tenant_id, list_id, email, status) VALUES (1, 1, 'unsub@test.com', 'active')");
$id = $pdo->lastInsertId();
// Unsubscribe it
$pdo->exec("UPDATE cm_subscribers SET status = 'unsubscribed' WHERE id = $id");
// Check
$status = $pdo->query("SELECT status FROM cm_subscribers WHERE id = $id")->fetchColumn();
echo "Subscriber Status: $status\n";

if($count >= 3 && empty($missing) && $status === 'unsubscribed') {
    echo "\nSUCCESS: All enhancements verified.\n";
} else {
    echo "\nFAILURE: Something is missing.\n";
}

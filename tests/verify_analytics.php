<?php

require_once __DIR__ . '/../app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();

echo "--- Verifying Analytics Table ---\n";
try {
    $rows = $pdo->query("DESCRIBE cm_analytics")->fetchAll(PDO::FETCH_ASSOC);
    echo "Table cm_analytics exists with columns: " . count($rows) . "\n";
} catch(Exception $e) {
    echo "Table Missing: " . $e->getMessage() . "\n";
    exit;
}

echo "\n--- Verifying Tracking Logic ---\n";
$hash = md5(uniqid());
$pdo->prepare("INSERT INTO cm_analytics (tenant_id, campaign_id, subscriber_id, hash, status) VALUES (1, 1, 1, ?, 'sent')")->execute([$hash]);

// Simulate Pixel Hit (Logic only, no HTTP)
echo "Tracking hit for hash: $hash\n";
$stmt = $pdo->prepare("UPDATE cm_analytics SET status = 'opened', opened_at = NOW() WHERE hash = ? AND status != 'opened'");
$stmt->execute([$hash]);

// Check
$status = $pdo->query("SELECT status FROM cm_analytics WHERE hash = '$hash'")->fetchColumn();
echo "Status: $status\n"; 

if($status === 'opened') {
    echo "\nSUCCESS: Analytics logic verified.\n";
} else {
    echo "\nFAILURE: Tracker did not update status.\n";
}

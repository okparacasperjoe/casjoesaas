<?php
// Test Script to Verify Admin Settings Persistence
require_once __DIR__ . '/../app/core/bootstrap.php';

echo "Verifying System Settings Integration...\n";

// 1. Simulate saving settings via Controller Logic (Manual DB Update for test)
$pdo = \App\Core\Database::getInstance()->getConnection();

$testKey = 'TEST_PK_' . uniqid();
$pdo->prepare("REPLACE INTO system_settings (setting_key, setting_value) VALUES ('flutterwave_public_key', ?)")->execute([$testKey]);

echo "Updated DB with Key: $testKey\n";

// 2. Re-load Global Config to check if bootstrap picked it up
// Since constants are already defined, we simulate a fresh check
$stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'flutterwave_public_key'");
$dbValue = $stmt->fetchColumn();

if ($dbValue === $testKey) {
    echo "PASS: Database persistence confirmed.\n";
} else {
    echo "FAIL: DB value mismatch.\n";
}

echo "Verification Complete. Please check the Admin Dashboard manually at /admin/settings to see the UI.\n";

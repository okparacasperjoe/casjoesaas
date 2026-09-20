<?php

// script/test_visa.php
// Run usage: php scripts/test_visa.php

require_once __DIR__ . '/../app/Core/bootstrap.php';

use App\Services\VisaCardService;
use App\Core\Database;

echo "\n--- Visa DPS Integration Test ---\n";

// 1. Init Service
echo "[1] Initializing Service...\n";
try {
    $service = new VisaCardService();
    echo "✓ Service Initialized\n";
} catch (\Exception $e) {
    die("✗ Failed to init service: " . $e->getMessage() . "\n");
}

// 2. Test Connection (Get Transactions for dummy ID to check auth)
echo "\n[2] Testing Auth with Dummy Call...\n";
$dummyId = "4444440000000001"; // Test ID
try {
    // This will likely fail with 404 or Valid response, but checks auth
    $res = $service->getCardStatus($dummyId);
    if ($res['success']) {
        echo "✓ Connection Successful (Card Found)\n";
    } else {
        echo "⚠ API Error (Expected for dummy data): " . $res['error'] . "\n";
        if (strpos($res['error'], '401') !== false || strpos($res['error'], '403') !== false) {
            echo "❌ AUTH FAILED. Check API Token.\n";
        } else {
            echo "✓ Auth seems OK (Error was not 401/403)\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n[3] Test Card Creation Payload...\n";
// We won't actually call createCard unless we want to spam sandbox
// But we can check if the method exists and validates
if (method_exists($service, 'createCard')) {
    echo "✓ `createCard` method exists\n";
}

echo "\n--- Test Complete ---\n";

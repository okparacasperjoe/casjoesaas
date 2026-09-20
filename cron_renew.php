<?php

require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Services\SubscriptionService;

/**
 * Casjoe Auto-Renewal CRON Job
 * Recommended Schedule: Run once per day at 12:00 AM
 */

if (php_sapi_name() !== 'cli') {
    // Basic security for web access
    $token = $_GET['token'] ?? '';
    if ($token !== 'casjoe_renew_key_9912') {
        die("Unauthorized access.");
    }
}

echo "--- Casjoe Subscription Renewal Cron --- \n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";

try {
    $service = new SubscriptionService();
    $service->renewAllExpiring();
    echo "Process completed successfully.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

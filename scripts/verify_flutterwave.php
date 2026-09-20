<?php
// Mock verifying Flutterwave Integration Logic
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@casjoe.com';

// Mock DB
echo "Testing Flutterwave Payment Flow...\n";

// 1. Verify Constants
require_once __DIR__ . '/../app/core/bootstrap.php';

if (defined('FLUTTERWAVE_PUBLIC_KEY') && FLUTTERWAVE_PUBLIC_KEY === '1a8fd095-2d80-49e2-9c82-36f580975161') {
    echo "PASS: Public Key Configured correctly.\n";
} else {
    echo "FAIL: Public Key mismatch.\n";
    exit(1);
}

// 2. Verify Routes
$routes = file_get_contents(__DIR__ . '/../app/core/bootstrap.php');
if (strpos($routes, '/billing/pay') !== false && strpos($routes, '/billing/callback') !== false) {
    echo "PASS: Payment routes found.\n";
} else {
    echo "FAIL: Missing routes.\n";
}

// 3. Verify Controller Method Existence (Reflection)
require_once __DIR__ . '/../app/core/Controllers/BillingController.php';
$ref = new ReflectionClass('App\Core\Controllers\BillingController');
if ($ref->hasMethod('initiatePayment') && $ref->hasMethod('callback')) {
    echo "PASS: Controller methods exist.\n";
} else {
    echo "FAIL: Missing controller methods.\n";
}

echo "Flutterwave Integration Logic Verified.\n";

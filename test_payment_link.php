<?php
// Test Script to Verify Flutterwave API Connectivity
require_once __DIR__ . '/app/core/bootstrap.php';

if (!defined('FLUTTERWAVE_PUBLIC_KEY')) define('FLUTTERWAVE_PUBLIC_KEY', 'placeholder');
if (!defined('FLUTTERWAVE_SECRET_KEY')) define('FLUTTERWAVE_SECRET_KEY', 'placeholder');

echo "Testing Flutterwave API Connectivity...\n";
echo "Public Key: " . FLUTTERWAVE_PUBLIC_KEY . "\n";
echo "Secret Key: " . FLUTTERWAVE_SECRET_KEY . "\n";

if (FLUTTERWAVE_SECRET_KEY === 'FLWSECK_TEST-SANDBOX-PLACEHOLDER') {
    echo "\nWARNING: You are using the placeholder Secret Key.\n";
    echo "The API call will likely fail unless you update it in app/core/bootstrap.php.\n";
    echo "Continuing anyway...\n\n";
}

// Prepare a test payload
$txRef = 'TEST-' . uniqid();
$payload = [
    'tx_ref' => $txRef,
    'amount' => 100, // Small amount for testing
    'currency' => 'NGN',
    'redirect_url' => 'https://example.com/callback',
    'payment_options' => 'card',
    'customer' => [
        'email' => 'test@casjoe.com',
        'name' => 'Test User'
    ],
    'customizations' => [
        'title' => 'API Connection Test',
        'description' => 'Verifying Keys'
    ]
];

// Call Flutterwave
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . FLUTTERWAVE_SECRET_KEY, // Standard calls use Secret Key
        'Content-Type: application/json'
    ],
]);

echo "Sending Request to Flutterwave...\n";
$response = curl_exec($curl);
$err = curl_error($curl);
// curl_close($curl); // Not necessary in PHP 8.0+ and flagged as deprecated usage by some linters

if ($err) {
    echo "cURL Error: " . $err . "\n";
    exit(1);
}

echo "Response received:\n";
$res = json_decode($response, true);

if (isset($res['status']) && $res['status'] === 'success') {
    echo "\n[SUCCESS] Payment Link Generated!\n";
    echo "Link: " . $res['data']['link'] . "\n";
    echo "\nPlease open this link in your browser to verify the checkout page loads.\n";
} else {
    echo "\n[FAILED] Could not generate payment link.\n";
    echo "Message: " . ($res['message'] ?? 'Unknown error') . "\n";
    echo "Detailed Data: " . print_r($res, true) . "\n";
    
    // Hint for V4 or Auth issues
    if (strpos(($res['message'] ?? ''), 'authorization') !== false) {
        echo "\n[HINT] This error suggests usually indicates an invalid Secret Key.\n";
        echo "Please verify that the key defined as FLUTTERWAVE_SECRET_KEY in bootstrap.php is correct.\n";
    }
}

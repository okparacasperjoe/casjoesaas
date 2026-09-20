<?php
// Tests the FULL chain: fetch card_number_url HTML → extract Bearer + path → call SecureProxy API
// Visit: https://app.casjoe.com/debug_extract_pan.php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/bootstrap.php';

$publicKey = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : '';
$cardId    = '6a793db40c21ac21977ebc1f';

echo "<pre style='background:#111;color:#0f0;padding:20px;font-size:12px;word-wrap:break-word;white-space:pre-wrap;'>";

// Step 1: Get card details
$apiUrl = "https://strowallet.com/api/bitvcard/fetch-nfccard-detail/?public_key=" 
        . urlencode($publicKey) . "&card_id=" . urlencode($cardId) . "&mode=live";

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>15, CURLOPT_SSL_VERIFYPEER=>false]);
$raw = curl_exec($ch);
curl_close($ch);
$apiData = json_decode($raw, true);
$detail = $apiData['response']['card_detail'] ?? [];
$cardNumberUrl = $detail['card_number_url'] ?? '';
$cvvUrl        = $detail['cvv_url'] ?? '';

echo "=== STEP 1: card_number_url found ===\n\n";

// Step 2: Fetch the card_number_url HTML
$ch2 = curl_init($cardNumberUrl);
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
]);
$html = curl_exec($ch2);
curl_close($ch2);

echo "=== STEP 2: Fetched HTML, parsing SecureProxy params ===\n\n";

// Step 3: Extract the SecureProxy params from the HTML
// Looking for: SecureProxy.create("vdl2xefo5")
// path:'/cards/.../secure-data/number'
// Authorization:'Bearer ...'
// jsonPathSelector:'data.number'

$proxyId = '';
$path    = '';
$bearer  = '';

if (preg_match('/SecureProxy\.create\(["\']([^"\']+)["\']\)/', $html, $m)) {
    $proxyId = $m[1];
}
if (preg_match("/path:\s*['\"]([^'\"]+)['\"]/", $html, $m)) {
    $path = $m[1];
}
if (preg_match("/Authorization:\s*['\"]Bearer\s+([^'\"]+)['\"]/", $html, $m)) {
    $bearer = $m[1];
}

echo "Proxy ID : $proxyId\n";
echo "Path     : $path\n";
echo "Bearer   : " . substr($bearer, 0, 50) . "...\n\n";

// Step 4: Try different base URL patterns for SecureProxy
$baseUrls = [
    "https://$proxyId.securepro.xyz",
    "https://api.securepro.xyz/$proxyId",
    "https://api.securepro.xyz",
];

foreach ($baseUrls as $base) {
    $fullUrl = $base . $path;
    echo "=== Trying: $fullUrl ===\n";
    
    $ch3 = curl_init($fullUrl);
    curl_setopt_array($ch3, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $bearer",
            "Accept: application/json",
            "Origin: https://strowallet.com",
            "Referer: https://strowallet.com/",
        ],
    ]);
    $resp = curl_exec($ch3);
    $err  = curl_error($ch3);
    $code = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
    curl_close($ch3);
    
    echo "HTTP: $code\n";
    if ($err) echo "cURL Error: $err\n";
    echo "Response: " . htmlspecialchars(substr($resp, 0, 500)) . "\n\n";
    
    // If we got JSON, try to extract the number
    $json = json_decode($resp, true);
    if ($json && isset($json['data']['number'])) {
        echo "*** CARD NUMBER FOUND: " . $json['data']['number'] . " ***\n\n";
    }
}

// Step 5: Also try CVV
echo "=== NOW TRYING CVV ===\n";
$ch4 = curl_init($cvvUrl);
curl_setopt_array($ch4, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>15, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_FOLLOWLOCATION=>true]);
$cvvHtml = curl_exec($ch4);
curl_close($ch4);

$cvvPath   = '';
$cvvBearer = '';
if (preg_match("/path:\s*['\"]([^'\"]+)['\"]/", $cvvHtml, $m)) {
    $cvvPath = $m[1];
}
if (preg_match("/Authorization:\s*['\"]Bearer\s+([^'\"]+)['\"]/", $cvvHtml, $m)) {
    $cvvBearer = $m[1];
}

echo "CVV Path  : $cvvPath\n";
echo "CVV Bearer: " . substr($cvvBearer, 0, 50) . "...\n\n";

foreach ($baseUrls as $base) {
    $fullUrl = $base . $cvvPath;
    echo "=== Trying CVV: $fullUrl ===\n";
    
    $ch5 = curl_init($fullUrl);
    curl_setopt_array($ch5, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $cvvBearer",
            "Accept: application/json",
            "Origin: https://strowallet.com",
            "Referer: https://strowallet.com/",
        ],
    ]);
    $resp = curl_exec($ch5);
    $err  = curl_error($ch5);
    $code = curl_getinfo($ch5, CURLINFO_HTTP_CODE);
    curl_close($ch5);
    
    echo "HTTP: $code\n";
    if ($err) echo "cURL Error: $err\n";
    echo "Response: " . htmlspecialchars(substr($resp, 0, 500)) . "\n\n";
    
    $json = json_decode($resp, true);
    if ($json && isset($json['data']['cvv'])) {
        echo "*** CVV FOUND: " . $json['data']['cvv'] . " ***\n\n";
    }
}

echo "</pre>";

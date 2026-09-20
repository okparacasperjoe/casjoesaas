<?php
// Test server-side card number fetch via SecurePro proxy
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../app/Core/bootstrap.php';

$cardId = '6a793db40c21ac21977ebc1f';
$publicKey = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : '';

echo "<pre style='background:#111;color:#0f0;padding:15px;font-size:12px;'>";

// STEP 1: Get fresh card details (fresh bearer token)
$ch = curl_init("https://strowallet.com/api/bitvcard/fetch-nfccard-detail/?public_key={$publicKey}&card_id={$cardId}&mode=live");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15]);
$res = json_decode(curl_exec($ch), true);

$cardNumberUrl = $res['response']['card_detail']['card_number_url'] ?? null;
$cvvUrl        = $res['response']['card_detail']['cvv_url'] ?? null;
echo "card_number_url: " . ($cardNumberUrl ? "✅ Found" : "❌ Missing") . "\n\n";

// STEP 2: Get Bearer token from card_number_url HTML
$ch = curl_init($cardNumberUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15, CURLOPT_USERAGENT=>'Mozilla/5.0']);
$html = curl_exec($ch);

preg_match('/Authorization[\'"]?\s*:\s*[\'"]Bearer\s+([\w\.\-]+)/i', $html, $m);
$bearerToken = $m[1] ?? null;
echo "Bearer token: " . ($bearerToken ? "✅ " . substr($bearerToken,0,30) . "..." : "❌ Missing") . "\n\n";

// STEP 3: Try SecurePro proxy endpoints directly
$proxyDomain = 'vdl2xefo5';
$attempts = [
    "https://{$proxyDomain}.securepro.xyz/cards/{$cardId}/secure-data/number",
    "https://js.securepro.xyz/{$proxyDomain}/cards/{$cardId}/secure-data/number",
    "https://api.securepro.xyz/{$proxyDomain}/cards/{$cardId}/secure-data/number",
];

foreach ($attempts as $url) {
    echo "Trying: $url\n";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15,
        CURLOPT_HTTPHEADER=>[
            "Authorization: Bearer $bearerToken",
            "Accept: application/json",
            "Origin: https://strowallet.com",
            "Referer: https://strowallet.com/"
        ],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP $code — " . substr($body, 0, 300) . "\n\n";
}

// STEP 4: Also try getting CVV bearer
$ch = curl_init($cvvUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15, CURLOPT_USERAGENT=>'Mozilla/5.0']);
$cvvHtml = curl_exec($ch);
preg_match('/Authorization[\'"]?\s*:\s*[\'"]Bearer\s+([\w\.\-]+)/i', $cvvHtml, $mc);
$cvvToken = $mc[1] ?? null;
echo "CVV Bearer token: " . ($cvvToken ? "✅ " . substr($cvvToken,0,30) . "..." : "❌ Missing") . "\n";

$ch = curl_init("https://{$proxyDomain}.securepro.xyz/cards/{$cardId}/secure-data/cvv");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_TIMEOUT=>15,
    CURLOPT_HTTPHEADER=>["Authorization: Bearer $cvvToken","Accept: application/json","Origin: https://strowallet.com"],
]);
$cvvBody = curl_exec($ch);
$cvvCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "CVV HTTP $cvvCode — " . substr($cvvBody, 0, 300) . "\n";

echo "</pre>";

<?php
// Tests what the card_number_url returns when fetched server-side
// Visit: https://app.casjoe.com/debug_secure_fetch.php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/bootstrap.php';

$publicKey = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : '';
$cardId    = '6a793db40c21ac21977ebc1f';

echo "<pre style='background:#111;color:#0f0;padding:20px;font-size:12px;word-wrap:break-word;white-space:pre-wrap;'>";

// Step 1: Get the card details to retrieve the secure URLs
$apiUrl = "https://strowallet.com/api/bitvcard/fetch-nfccard-detail/?public_key=" 
        . urlencode($publicKey) . "&card_id=" . urlencode($cardId) . "&mode=live";

echo "=== STEP 1: Get card details ===\n";
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>15, CURLOPT_SSL_VERIFYPEER=>false]);
$raw = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$data = json_decode($raw, true);
$detail = $data['response']['card_detail'] ?? [];
$cardNumberUrl = $detail['card_number_url'] ?? '';
$cvvUrl        = $detail['cvv_url'] ?? '';

echo "HTTP: $code\n";
echo "card_number_url: $cardNumberUrl\n";
echo "cvv_url: $cvvUrl\n\n";

// Step 2: Fetch the card_number_url and show what we get back
echo "=== STEP 2: Fetch card_number_url ===\n";
$ch2 = curl_init($cardNumberUrl);
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; CasjoePay/1.0)',
    CURLOPT_HTTPHEADER     => ['Accept: text/html'],
    CURLOPT_VERBOSE        => false,
]);
$html2 = curl_exec($ch2);
$err2  = curl_error($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch2, CURLINFO_EFFECTIVE_URL);
curl_close($ch2);

echo "HTTP: $code2\n";
echo "cURL error: " . ($err2 ?: 'none') . "\n";
echo "Final URL (after redirects): $finalUrl\n\n";
echo "RAW HTML response (first 2000 chars):\n";
echo htmlspecialchars(substr($html2, 0, 2000)) . "\n\n";

// Step 3: Strip tags and try to extract number
echo "=== STEP 3: After strip_tags ===\n";
$text = preg_replace('/\s+/', ' ', trim(strip_tags($html2)));
echo "Text: " . htmlspecialchars(substr($text, 0, 500)) . "\n\n";

// Step 4: Try regex
echo "=== STEP 4: Regex match ===\n";
if (preg_match('/\b(\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4})\b/', $text, $m)) {
    echo "CARD NUMBER FOUND: " . $m[1] . "\n";
} else {
    echo "No 16-digit card number found in text.\n";
    echo "All digit sequences found:\n";
    preg_match_all('/\d+/', $text, $allDigits);
    foreach ($allDigits[0] as $d) {
        if (strlen($d) >= 3) echo "  '$d' (len=" . strlen($d) . ")\n";
    }
}

echo "</pre>";

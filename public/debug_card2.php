<?php
// Quick debug - shows the EXACT raw JSON from StroWallet for card details
// Usage: https://yourdomain.com/debug_card2.php?card_id=YOUR_CARD_ID

$card_id = $_GET['card_id'] ?? '';
if (!$card_id) {
    die("Usage: ?card_id=YOUR_CARD_ID");
}

// Load bootstrap to get the public key
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/bootstrap.php';

$publicKey = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : ($_ENV['STROWALLET_PUBLIC_KEY'] ?? 'NOT_FOUND');

echo "<pre style='background:#000;color:#0f0;padding:20px;font-size:13px;'>";
echo "Public Key: " . substr($publicKey, 0, 15) . "...\n\n";

$url = "https://strowallet.com/api/bitvcard/fetch-nfccard-detail/?public_key=" 
     . urlencode($publicKey) . "&card_id=" . urlencode($card_id) . "&mode=live";

echo "Calling: $url\n\n";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$raw  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP: $code\n\n";
echo "RAW RESPONSE:\n";
echo htmlspecialchars($raw) . "\n\n";

// Try to decode and show the specific fields we care about
$decoded = json_decode($raw, true);
if ($decoded) {
    $detail = $decoded['response']['card_detail'] ?? $decoded['data'] ?? $decoded;
    echo "---KEY FIELDS---\n";
    echo "card_number    : " . var_export($detail['card_number'] ?? 'NOT PRESENT', true) . "\n";
    echo "cvv            : " . var_export($detail['cvv'] ?? 'NOT PRESENT', true) . "\n";
    echo "expiry         : " . var_export($detail['expiry'] ?? 'NOT PRESENT', true) . "\n";
    echo "balance        : " . var_export($detail['balance'] ?? 'NOT PRESENT', true) . "\n";
    echo "card_number_url: " . var_export($detail['card_number_url'] ?? 'NOT PRESENT', true) . "\n";
    echo "cvv_url        : " . var_export($detail['cvv_url'] ?? 'NOT PRESENT', true) . "\n";
    echo "last4          : " . var_export($detail['last4'] ?? 'NOT PRESENT', true) . "\n";
}
echo "</pre>";

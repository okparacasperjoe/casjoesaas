<?php
// Quick test: call our own /pay/cards/details endpoint and show the JSON
// Usage: https://yourdomain.com/debug_cardapi.php?card_id=YOUR_CARD_ID
// Must be logged in for this to work

session_start();
if (empty($_SESSION['user_id'])) {
    die("Please log in first, then visit this URL.");
}

$card_id = $_GET['card_id'] ?? '';
if (!$card_id) {
    die("Usage: ?card_id=YOUR_CARD_ID");
}

// Load our own app
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Modules\CasjoePay\Services\StroWalletService;

header('Content-Type: text/plain');

$db   = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$card_id, $_SESSION['user_id']]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    echo "Card not found in DB for user " . $_SESSION['user_id'];
    exit;
}

echo "=== CARD IN DATABASE ===\n";
echo "card_id    : " . $card['card_id'] . "\n";
echo "masked_pan : " . ($card['masked_pan'] ?? 'null') . "\n";
echo "balance    : " . ($card['balance'] ?? 'null') . "\n\n";

$strowallet = new StroWalletService();
echo "Public Key : " . substr($strowallet->publicKey, 0, 15) . "...\n\n";

echo "=== CALLING StroWallet fetchNfcCardDetails ===\n";
try {
    $res = $strowallet->fetchNfcCardDetails($card_id);
    echo "FULL RESPONSE:\n";
    print_r($res);
    
    echo "\n=== KEY FIELDS ===\n";
    $detail = $res['response']['card_detail'] ?? [];
    echo "card_number    : " . var_export($detail['card_number'] ?? 'NOT PRESENT', true) . "\n";
    echo "cvv            : " . var_export($detail['cvv'] ?? 'NOT PRESENT', true) . "\n";
    echo "expiry         : " . var_export($detail['expiry'] ?? 'NOT PRESENT', true) . "\n";
    echo "balance        : " . var_export($detail['balance'] ?? 'NOT PRESENT', true) . "\n";
    echo "card_number_url: " . var_export($detail['card_number_url'] ?? 'NOT PRESENT', true) . "\n";
    echo "cvv_url        : " . var_export($detail['cvv_url'] ?? 'NOT PRESENT', true) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

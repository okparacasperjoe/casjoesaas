<?php

// Full Flow Test: Create User -> Fund Wallet -> Issue Visa Card
// Usage: php scripts/test_full_flow.php

require_once __DIR__ . '/../app/Core/bootstrap.php';

use App\Core\Database;
use App\Services\VisaCardService;

echo "========================================\n";
echo "   Visa Integration: End-to-End Test    \n";
echo "========================================\n\n";

$pdo = Database::getInstance()->getConnection();

try {
    // 1. Create Test User
    $rand = substr(md5(mt_rand()), 0, 5);
    $email = "visa_test_{$rand}@casjoe.com";
    $name = "Test User {$rand}";
    
    echo "[1] Creating Test User ($email)...\n";
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, tenant_id) VALUES (?, ?, ?, 'user', 1)");
    $stmt->execute([$name, $email, password_hash('password123', PASSWORD_BCRYPT)]);
    $userId = $pdo->lastInsertId();
    echo "    ✓ User ID: $userId\n";

    // 2. Fund Wallet (Optional, if logic requires it, but Service might not check wallet directly yet)
    // We'll create a wallet record just in case logic evolves
    echo "[2] Seeding Wallet...\n";
    $stmt = $pdo->prepare("INSERT INTO cp_wallets (user_id, currency, balance, tenant_id) VALUES (?, 'USD', 1000.00, 1)");
    $stmt->execute([$userId]);
    echo "    ✓ Balance: $1,000.00\n";

    // 3. Issue Card
    echo "[3] Requesting Visa Virtual Card...\n";
    $service = new VisaCardService();
    
    // Payload usually comes from Controller->Input, here we mock it
    $cardData = [
        'name_on_card' => $name,
        'pan' => null, // Let service generate sandbox PAN
    ];

    $result = $service->createCard($userId, $cardData);

    if ($result['success']) {
        echo "    ✓ SUCCESS!\n";
        echo "    - Card ID: " . $result['card_id'] . "\n";
        echo "    - Masked PAN: " . $result['masked_pan'] . "\n";
        echo "    - Status: Inactive (Requires Activation)\n";
        
        // 4. Verify DB
        echo "[4] Verifying Database Record...\n";
        $stmt = $pdo->prepare("SELECT * FROM cp_virtual_cards WHERE card_id = ?");
        $stmt->execute([$result['card_id']]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($card) {
            echo "    ✓ DB Record Found (ID: {$card['id']})\n";
            echo "    ✓ Provider: {$card['provider']}\n";
        } else {
            echo "    ✗ DB Record Missing!\n";
        }

    } else {
        echo "    ✗ FAILED: " . ($result['error'] ?? 'Unknown Error') . "\n";
    }

} catch (Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";

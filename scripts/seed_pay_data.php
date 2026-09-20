<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;
$userId = 1;

echo "Seeding Pay Data...\n";

try {
    // 1. Ensure Wallet
    $stmt = $pdo->prepare("SELECT id FROM cp_wallets WHERE user_id = ?");
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch();
    
    if (!$wallet) {
        $pdo->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, 'NGN', 1000000.00)")
            ->execute([$tenantId, $userId]);
        echo "[OK] Created Wallet with 1M NGN.\n";
    } else {
        echo "[SKIP] Wallet exists.\n";
    }

    // 2. Dummy Transaction
    $pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, status, description) VALUES (?, ?, ?, 'credit', 50000, 'successful', 'Welcome Bonus')")
        ->execute([$tenantId, $userId, 'ref_'.uniqid()]);
    echo "[OK] Created Dummy Transaction.\n";

    // 3. Payment Link
    $slug = 'consult-'.uniqid();
    $pdo->prepare("INSERT INTO cp_payment_links (tenant_id, user_id, slug, title, amount, currency) VALUES (?, ?, ?, 'Consultation Fee', 5000, 'NGN')")
        ->execute([$tenantId, $userId, $slug]);
    echo "[OK] Created Payment Link: $slug\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

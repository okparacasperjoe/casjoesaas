<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tables = ['cp_wallets', 'cp_transactions', 'cp_payment_links', 'cp_virtual_cards', 'transactions'];

echo "Checking Pay Tables...\n";
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
        echo "[OK] Table `$t` exists.\n";
    } catch (PDOException $e) {
        echo "[MISSING] Table `$t` does not exist.\n";
    }
}

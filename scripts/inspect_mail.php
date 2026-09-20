<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tables = ['cm_lists', 'cm_subscribers', 'cm_templates', 'cm_campaigns', 'cm_settings'];

echo "Checking Mail Tables...\n";
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
        echo "[OK] Table `$t` exists.\n";
    } catch (PDOException $e) {
        echo "[MISSING] Table `$t` does not exist.\n";
    }
}

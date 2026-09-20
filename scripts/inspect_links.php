<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tables = ['links_bio_pages', 'links_short_urls', 'links_static_sites', 'links_qr_codes', 'links_analytics'];

echo "Checking Links Tables...\n";
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
        echo "[OK] Table `$t` exists.\n";
    } catch (PDOException $e) {
        echo "[MISSING] Table `$t` does not exist.\n";
    }
}

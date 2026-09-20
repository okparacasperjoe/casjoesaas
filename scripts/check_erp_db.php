<?php

require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Checking ERP Tables...\n";
$tables = ['erp_gl_accounts', 'erp_journal_entries', 'erp_employees', 'erp_customers', 'erp_products'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "[OK] Table `$table` exists with $count rows.\n";
    } catch (PDOException $e) {
        echo "[MISSING] Table `$table` does not exist.\n";
    }
}

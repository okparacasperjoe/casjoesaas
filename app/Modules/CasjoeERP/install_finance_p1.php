<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/finance_update_phase1.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "Finance Module Phase 1 (Estimates, Expenses, Vendors) update applied successfully.\n";

} catch (Exception $e) {
    die("Error updating Finance module: " . $e->getMessage() . "\n");
}

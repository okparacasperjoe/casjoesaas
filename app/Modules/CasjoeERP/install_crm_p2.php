<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/crm_update_phase2.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "CRM Module Phase 2 (Kanban Stages) update applied successfully.\n";

} catch (Exception $e) {
    die("Error updating CRM module: " . $e->getMessage() . "\n");
}

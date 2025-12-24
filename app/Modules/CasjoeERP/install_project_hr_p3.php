<?php
require_once __DIR__ . '/../../../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/sql/project_hr_update_phase3.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }

    echo "Project/HR Module Phase 3 (Time Logs) update applied successfully.\n";

} catch (Exception $e) {
    die("Error updating Project/HR module: " . $e->getMessage() . "\n");
}

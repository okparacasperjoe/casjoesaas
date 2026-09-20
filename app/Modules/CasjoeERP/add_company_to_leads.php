<?php
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

try {
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM erp_crm_leads LIKE 'company'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $db->exec("ALTER TABLE erp_crm_leads ADD COLUMN company VARCHAR(255) AFTER source");
        echo "Added 'company' column to erp_crm_leads table.\n";
    } else {
        echo "'company' column already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
}

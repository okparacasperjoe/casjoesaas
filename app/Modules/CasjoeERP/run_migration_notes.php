<?php
// app/Modules/CasjoeERP/run_migration_notes.php
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if notes column exists
    $stmt = $db->query("SHOW COLUMNS FROM erp_crm_leads LIKE 'notes'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $db->exec("ALTER TABLE erp_crm_leads ADD COLUMN notes TEXT NULL AFTER status");
        echo "Successfully added 'notes' column to erp_crm_leads.<br>";
    } else {
        echo "'notes' column already exists in erp_crm_leads.<br>";
    }
    
    // Ensure company column exists (safeguard)
    $stmt = $db->query("SHOW COLUMNS FROM erp_crm_leads LIKE 'company'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $db->exec("ALTER TABLE erp_crm_leads ADD COLUMN company VARCHAR(255) NULL AFTER phone");
        echo "Successfully added 'company' column to erp_crm_leads.<br>";
    } else {
        echo "'company' column already exists in erp_crm_leads.<br>";
    }

    echo "<strong>Migration complete!</strong>";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}

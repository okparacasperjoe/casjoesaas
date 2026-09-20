<?php
require_once __DIR__ . '/../../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $queries = [
        "ALTER TABLE erp_crm_leads ADD COLUMN industry VARCHAR(255) NULL AFTER email;",
        "ALTER TABLE erp_crm_leads ADD COLUMN budget DECIMAL(15,2) NULL AFTER industry;",
        "ALTER TABLE erp_crm_leads ADD COLUMN timeline VARCHAR(255) NULL AFTER budget;",
        "ALTER TABLE erp_crm_leads ADD COLUMN interest_level VARCHAR(50) NULL AFTER timeline;",
        "ALTER TABLE erp_crm_leads ADD COLUMN ai_score INT DEFAULT 0 AFTER interest_level;",
        "ALTER TABLE erp_crm_leads ADD COLUMN ai_research_notes TEXT NULL AFTER ai_score;",
        "ALTER TABLE erp_crm_leads ADD COLUMN source VARCHAR(100) DEFAULT 'manual' AFTER ai_research_notes;"
    ];

    foreach ($queries as $query) {
        try {
            $db->exec($query);
        } catch (Exception $e) {
            // Ignore if column already exists
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                echo "Error executing query: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "CRM Lead tables updated successfully with AI fields.\n";

} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage() . "\n");
}

<?php
/**
 * Phase 1: Consent Tracking Migration
 * Run this script to migrate existing CasjoeMail subscribers to consent tracking system
 */

require_once __DIR__ . '/../../Core/Database.php';


echo "==============================================\n";
echo "  CasjoeMail Phase 1: Consent Tracking\n";
echo "==============================================\n\n";

try {
    $db = App\Core\Database::getInstance()->getConnection();
    
    echo "1. Checking database schema...\n";
    
    // Check if consent columns already exist
    $stmt = $db->query("SHOW COLUMNS FROM cm_subscribers LIKE 'consent_method'");
    if ($stmt->rowCount() > 0) {
        echo "   ✓ Consent columns already exist.\n\n";
    } else {
        echo "   Adding consent tracking columns...\n";
        
        // Run migration
        $sql = file_get_contents(__DIR__ . '/../sql/phase1_consent_tracking.sql');
        $db->exec($sql);
        
        echo "   ✓ Consent columns added successfully.\n\n";
    }
    
    echo "2. Migrating existing subscribers...\n";
    
    // Mark all existing subscribers as 'legacy' (grandfather clause)
    $stmt = $db->query("
        UPDATE cm_subscribers 
        SET consent_method = 'legacy',
            consent_date = created_at,
            consent_proof = JSON_OBJECT('note', 'Existing subscriber before consent tracking was implemented', 'migrated_at', NOW())
        WHERE consent_method IS NULL
    ");
    
    $updated = $stmt->rowCount();
    echo "   ✓ Marked $updated existing subscribers as 'legacy'.\n\n";
    
    echo "3. Verifying migration...\n";
    
    // Get statistics
    $stmt = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN consent_method IS NOT NULL THEN 1 ELSE 0 END) as with_consent,
            SUM(CASE WHEN consent_method = 'legacy' THEN 1 ELSE 0 END) as legacy_count
        FROM cm_subscribers
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   Total subscribers: " . $stats['total'] . "\n";
    echo "   With consent: " . $stats['with_consent'] . "\n";
    echo "   Legacy subscribers: " . $stats['legacy_count'] . "\n\n";
    
    echo "==============================================\n";
    echo "  ✓ Migration Complete!\n";
    echo "==============================================\n\n";
    echo "Next steps:\n";
    echo "1. Visit /mail/consent/audit to view consent records\n";
    echo "2. All existing subscribers are marked as 'legacy'\n";
    echo "3. New subscribers will have consent captured automatically\n\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

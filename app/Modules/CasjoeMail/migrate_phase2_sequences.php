<?php
/**
 * Phase 2: Smart Follow-Up Sequences Migration
 * Run this script to create the necessary database tables
 */

require __DIR__ . '/../../../Core/Database.php';

use App\Core\Database;
use App\Core\TenantContext;

// Get database connection
$db = Database::getInstance()->getConnection();
$tenantId = TenantContext::getTenantId();

echo "==============================================\n";
echo "  Phase 2: Sequences Migration\n";
echo "==============================================\n\n";

try {
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/../sql/phase2_sequences.sql');
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
            // Extract table name from CREATE TABLE statement
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "✅ Created table: {$matches[1]}\n";
            }
        }
    }
    
    echo "\n==============================================\n";
    echo "  Migration Complete!\n";
    echo "==============================================\n\n";
    
    echo "Created tables:\n";
    echo "  - cm_sequences\n";
    echo "  - cm_sequence_steps\n";
    echo "  - cm_sequence_enrollments\n\n";
    
    echo "Next steps:\n";
    echo "1. Navigate to /mail/sequences\n";
    echo "2. Create your first follow-up sequence\n";
    echo "3. Enroll subscribers\n\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "- Database connection is working\n";
    echo "- Tables don't already exist\n";
    echo "- You have CREATE TABLE permissions\n\n";
    exit(1);
}

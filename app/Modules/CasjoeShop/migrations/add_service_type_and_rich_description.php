<?php
/**
 * Migration: Add Service Product Type and Rich Description Support
 * 
 * This migration:
 * 1. Adds 'service' to the product type ENUM
 * 2. Extends description field from TEXT to MEDIUMTEXT for rich HTML content
 * 3. Adds index on type column for performance
 */

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../config/database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    echo "========================================\n";
    echo "CasjoeShop Migration: Service Type & Rich Descriptions\n";
    echo "========================================\n\n";
    
    // Step 1: Check current type column
    echo "1. Checking current product type ENUM...\n";
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'type'");
    $typeColumn = $stmt->fetch();
    
    if ($typeColumn) {
        echo "   Current Type: {$typeColumn['Type']}\n";
        
        // Check if 'service' already exists
        if (strpos($typeColumn['Type'], 'service') === false) {
            echo "2. Adding 'service' to product type ENUM...\n";
            $db->query("ALTER TABLE shop_products MODIFY COLUMN type ENUM('physical', 'digital', 'service') NOT NULL DEFAULT 'physical'");
            echo "   ✅ Service type added successfully!\n";
        } else {
            echo "2. ⏭️  Service type already exists, skipping...\n";
        }
    } else {
        echo "   ❌ ERROR: type column not found!\n";
        exit(1);
    }
    
    // Step 2: Check description column
    echo "\n3. Checking description column type...\n";
    $stmt = $db->query("SHOW COLUMNS FROM shop_products LIKE 'description'");
    $descColumn = $stmt->fetch();
    
    if ($descColumn) {
        echo "   Current Type: {$descColumn['Type']}\n";
        
        if (stripos($descColumn['Type'], 'mediumtext') === false) {
            echo "4. Upgrading description field to MEDIUMTEXT...\n";
            $db->query("ALTER TABLE shop_products MODIFY COLUMN description MEDIUMTEXT NULL");
            echo "   ✅ Description field upgraded successfully!\n";
        } else {
            echo "4. ⏭️  Description already MEDIUMTEXT, skipping...\n";
        }
    } else {
        echo "   ❌ ERROR: description column not found!\n";
        exit(1);
    }
    
    // Step 3: Add index on type column if not exists
    echo "\n5. Checking indexes on type column...\n";
    $stmt = $db->query("SHOW INDEX FROM shop_products WHERE Column_name = 'type'");
    $typeIndex = $stmt->fetch();
    
    if (!$typeIndex) {
        echo "6. Adding index on type column for better performance...\n";
        $db->query("CREATE INDEX idx_shop_products_type ON shop_products(type)");
        echo "   ✅ Index added successfully!\n";
    } else {
        echo "6. ⏭️  Index on type column already exists, skipping...\n";
    }
    
    echo "\n========================================\n";
    echo "✅ Migration completed successfully!\n";
    echo "========================================\n";
    echo "\nSummary:\n";
    echo "- Product types now support: physical, digital, service\n";
    echo "- Description field can now store rich HTML content (up to 16MB)\n";
    echo "- Type column indexed for faster queries\n\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

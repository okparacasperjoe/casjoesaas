<?php
require_once __DIR__ . "/../app/Core/Database.php";

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Check if country exists
    $stmt = $db->query("SHOW COLUMNS FROM cp_naira_card_users LIKE 'country'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE cp_naira_card_users ADD COLUMN country VARCHAR(10) DEFAULT 'NGA' AFTER state");
        echo "Added country column.\n";
    }
    
    // Check if postal_code exists
    $stmt = $db->query("SHOW COLUMNS FROM cp_naira_card_users LIKE 'postal_code'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE cp_naira_card_users ADD COLUMN postal_code VARCHAR(20) DEFAULT '100001' AFTER country");
        echo "Added postal_code column.\n";
    }

    echo "Database schema updated successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

@unlink(__FILE__);

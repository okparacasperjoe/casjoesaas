<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

try {
    echo "Updating Casjoe Pay schema for Virtual Cards...\n";

    // 1. Create table if not exists with provider column
    $sql = "CREATE TABLE IF NOT EXISTS cp_virtual_cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        user_id INT NOT NULL,
        provider ENUM('sudo', 'strowallet') DEFAULT 'sudo',
        card_name VARCHAR(255) NOT NULL,
        card_number VARCHAR(20),
        expiry_month VARCHAR(2),
        expiry_year VARCHAR(4),
        cvv VARCHAR(4),
        balance DECIMAL(15, 2) DEFAULT 0.00,
        currency VARCHAR(3) DEFAULT 'USD',
        status ENUM('active', 'frozen', 'terminated') DEFAULT 'active',
        provider_card_id VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->exec($sql);
    echo "Virtual Cards table checked/created.\n";
    
    // 2. Check if provider column exists (in case table already existed without it)
    $check = $db->query("SHOW COLUMNS FROM cp_virtual_cards LIKE 'provider'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE cp_virtual_cards ADD COLUMN provider ENUM('sudo', 'strowallet') DEFAULT 'sudo' AFTER user_id");
        echo "Added 'provider' column.\n";
    }

    echo "Schema update complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

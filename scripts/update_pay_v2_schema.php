<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

try {
    echo "Updating Casjoe Pay V2 Schema...\n";

    // 1. Wallets: Ensure unique constraint for multi-currency
    // We first check if the index exists to avoid errors.
    // Ideally we'd DROP INDEX if exists but MySQL implies checking first.
    // For simplicity in this script, we'll try to add it and catch duplicate error or ignore.
    
    // Check if user_id index needs upgrading to (user_id, currency)
    // NOTE: install.sql had KEY `user_id` (`user_id`). We want UNIQUE KEY `user_currency` (`user_id`, `currency`).
    // If we have duplicate NGN wallets, this might fail, but clean slate assumes we don't.
    
    try {
        $db->exec("ALTER TABLE cp_wallets ADD UNIQUE KEY `user_currency` (`user_id`, `currency`)");
        echo "Added unique constraint to wallets.\n";
    } catch (PDOException $e) {
        // Likely already exists or duplicates exist
        echo "Notice on Wallets: " . $e->getMessage() . "\n";
    }

    // 2. KYC Table
    $sql = "CREATE TABLE IF NOT EXISTS cp_kyc_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        verification_type ENUM('bvn', 'nin', 'passport', 'id_card') NOT NULL,
        verification_number VARCHAR(100),
        status ENUM('pending', 'verified', 'failed') DEFAULT 'pending',
        document_url VARCHAR(255),
        verified_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $db->exec($sql);
    echo "KYC table created.\n";

    // 3. Beneficiaries (Bank/MoMo)
    $sql = "CREATE TABLE IF NOT EXISTS cp_beneficiaries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        account_name VARCHAR(255) NOT NULL,
        account_number VARCHAR(50) NOT NULL,
        bank_name VARCHAR(100), -- or Provider Name like 'MTN'
        type ENUM('bank_account', 'mobile_money') NOT NULL,
        currency VARCHAR(3) DEFAULT 'NGN',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $db->exec($sql);
    echo "Beneficiaries table created.\n";
    
    // 4. Update Transactions to support extended metadata if not already
    // (It already has 'meta' JSON column, so we are good).

    echo "Casjoe Pay V2 Schema Upgrade Complete.\n";

} catch (PDOException $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}

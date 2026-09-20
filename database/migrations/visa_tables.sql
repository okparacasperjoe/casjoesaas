-- Visa Integration Tables
-- 1. Cardholders Table
CREATE TABLE IF NOT EXISTS `visa_cardholders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `visa_cardholder_id` VARCHAR(255) NOT NULL,
    `kyc_status` VARCHAR(50) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 2. Virtual Cards Table (Ensure compatibility or create new if not using cp_virtual_cards)
-- We will use cp_virtual_cards but ensure columns exist
-- Modify existing table or create if missing (safest to create generic if not exists, then alter)
CREATE TABLE IF NOT EXISTS `cp_virtual_cards` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `role` varchar(50) DEFAULT 'owner',
    `card_id` varchar(255) DEFAULT NULL,
    `name_on_card` varchar(255) DEFAULT NULL,
    `pan` varchar(255) DEFAULT NULL COMMENT 'Masked',
    `cvv` varchar(10) DEFAULT NULL COMMENT 'Encrypted',
    `expiry_month` varchar(5) DEFAULT NULL,
    `expiry_year` varchar(5) DEFAULT NULL,
    `currency` varchar(10) DEFAULT 'USD',
    `balance` decimal(15, 2) DEFAULT 0.00,
    `status` varchar(50) DEFAULT 'active',
    `provider` varchar(50) DEFAULT 'visa',
    `meta_data` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Add provider column if not exists (in case table exists from previous provider)
-- We'll do this via PHP script since SQL IF NOT EXISTS for column is tricky in pure SQL file without procedure
-- But for now, we assume the table creation covers it or we run an alter script later.
-- 3. Card Transactions
CREATE TABLE IF NOT EXISTS `cp_card_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `card_id` INT NOT NULL,
    `visa_transaction_id` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15, 2) NOT NULL,
    `currency` VARCHAR(10) DEFAULT 'USD',
    `merchant_name` VARCHAR(255) DEFAULT NULL,
    `merchant_category` VARCHAR(100) DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT 'pending',
    `transaction_type` VARCHAR(50) DEFAULT 'purchase',
    `transaction_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`card_id`) REFERENCES `cp_virtual_cards`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 4. Settings Insert (Generic)
-- This will be handled by the updateSettings logic, but we can seed defaults
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`)
VALUES ('visa_enabled', '0'),
    ('visa_api_token', ''),
    ('visa_sandbox_mode', '1');
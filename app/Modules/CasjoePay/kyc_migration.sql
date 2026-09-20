-- Migration for StroWallet KYC Tracking
ALTER TABLE `cp_virtual_cards`
ADD COLUMN `strowallet_customer_id` VARCHAR(255) DEFAULT NULL
AFTER `user_id`,
    ADD COLUMN `kyc_status` ENUM(
        'not_submitted',
        'pending',
        'approved',
        'rejected'
    ) DEFAULT 'not_submitted'
AFTER `strowallet_customer_id`,
    ADD COLUMN `kyc_notes` TEXT DEFAULT NULL
AFTER `kyc_status`;
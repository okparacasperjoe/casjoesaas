-- Migration: Add notes and ensure company exists in erp_crm_leads
-- This script adds the 'notes' column and ensures 'company' is present.
SET @dbname = DATABASE();
SET @tablename = 'erp_crm_leads';
SET @columnname = 'notes';
SET @preparedStatement = (
        SELECT IF(
                (
                    SELECT COUNT(*)
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = @dbname
                        AND TABLE_NAME = @tablename
                        AND COLUMN_NAME = @columnname
                ) > 0,
                'SELECT 1',
                'ALTER TABLE erp_crm_leads ADD COLUMN notes TEXT AFTER status'
            )
    );
PREPARE stmt
FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Ensure company (sometimes called company_name in other migrations) exists as 'company'
SET @columnname = 'company';
SET @preparedStatement = (
        SELECT IF(
                (
                    SELECT COUNT(*)
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = @dbname
                        AND TABLE_NAME = @tablename
                        AND COLUMN_NAME = @columnname
                ) > 0,
                'SELECT 1',
                'ALTER TABLE erp_crm_leads ADD COLUMN company VARCHAR(255) AFTER phone'
            )
    );
PREPARE stmt
FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
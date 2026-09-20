-- Add expiration to tenant_modules for recurring module subscriptions
ALTER TABLE `tenant_modules`
ADD `expires_at` TIMESTAMP NULL DEFAULT NULL
AFTER `status`;
ALTER TABLE `tenant_modules`
ADD `payment_method` varchar(50) DEFAULT NULL
AFTER `expires_at`;
ALTER TABLE `tenant_modules`
ADD `last_payment_at` TIMESTAMP NULL DEFAULT NULL
AFTER `payment_method`;
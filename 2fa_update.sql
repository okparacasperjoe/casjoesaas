ALTER TABLE `users`
ADD COLUMN `two_factor_secret` VARCHAR(255) NULL DEFAULT NULL
AFTER `password`;
ALTER TABLE `users`
ADD COLUMN `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0
AFTER `two_factor_secret`;
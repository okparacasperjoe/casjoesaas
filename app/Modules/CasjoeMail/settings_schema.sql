CREATE TABLE IF NOT EXISTS `cm_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text DEFAULT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `tenant_key` (`tenant_id`, `setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
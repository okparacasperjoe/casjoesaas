-- Chat Messages Table
CREATE TABLE IF NOT EXISTS `erp_chat_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `tenant_id` (`tenant_id`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Push Subscriptions Table
CREATE TABLE IF NOT EXISTS `erp_push_subscriptions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `endpoint` text NOT NULL,
    `keys_p256dh` text NOT NULL,
    `keys_auth` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
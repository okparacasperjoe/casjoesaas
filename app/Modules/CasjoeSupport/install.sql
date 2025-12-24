CREATE TABLE IF NOT EXISTS `cs_tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `status` ENUM('open', 'answered', 'closed') DEFAULT 'open',
    `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `tenant_id` (`tenant_id`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `cs_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `is_staff` BOOLEAN DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `ticket_id` (`ticket_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
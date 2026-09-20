-- Casjoe Links Module Schema
-- Bio Link Pages
CREATE TABLE IF NOT EXISTS `links_bio_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `title` varchar(255) DEFAULT 'My Bio Link',
    `description` text,
    `theme_config` longtext,
    -- JSON for colors, fonts, background
    `blocks` longtext,
    -- JSON for links, text, embeds
    `settings` longtext,
    -- JSON for SEO, password protection, sensitive warning
    `views` int(11) DEFAULT 0,
    `status` enum('active', 'disabled') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug_tenant` (`slug`, `tenant_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Short URLs
CREATE TABLE IF NOT EXISTS `links_short_urls` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `long_url` text NOT NULL,
    `short_code` varchar(50) NOT NULL,
    `type` enum('direct', 'frame', 'splash') DEFAULT 'direct',
    `password` varchar(255) DEFAULT NULL,
    `expires_at` datetime DEFAULT NULL,
    `max_clicks` int(11) DEFAULT 0,
    `clicks` int(11) DEFAULT 0,
    `targeting` longtext,
    -- JSON for country/device targeting
    `status` enum('active', 'disabled', 'expired') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `code_tenant` (`short_code`, `tenant_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Static Sites
CREATE TABLE IF NOT EXISTS `links_static_sites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `subdomain` varchar(255) NOT NULL,
    `storage_path` varchar(255) NOT NULL,
    -- Path to uploaded files
    `settings` longtext,
    -- JSON for SEO, password, analytics
    `status` enum('active', 'disabled') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- QR Codes
CREATE TABLE IF NOT EXISTS `links_qr_codes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `type` enum('url', 'text', 'wifi', 'vcard', 'email', 'sms') NOT NULL DEFAULT 'url',
    `content` text NOT NULL,
    `design_config` longtext,
    -- JSON for colors, logo, frame, shape
    `image_path` varchar(255) DEFAULT NULL,
    -- Cached generated image
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Analytics (Unified)
CREATE TABLE IF NOT EXISTS `links_analytics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `link_type` enum('bio', 'short', 'static', 'qr') NOT NULL,
    `link_id` int(11) NOT NULL,
    `visitor_ip` varchar(45) DEFAULT NULL,
    `country` varchar(255) DEFAULT NULL,
    `city` varchar(255) DEFAULT NULL,
    `device_type` varchar(50) DEFAULT NULL,
    `os` varchar(50) DEFAULT NULL,
    `browser` varchar(50) DEFAULT NULL,
    `referrer` text DEFAULT NULL,
    `visited_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
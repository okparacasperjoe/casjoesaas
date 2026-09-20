CREATE TABLE IF NOT EXISTS `shop_ads_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    /* For multi-tenant support if needed */
    `banner_location` VARCHAR(50) NOT NULL,
    /* 'top', 'middle', 'sidebar' */
    `title` VARCHAR(255),
    `description` TEXT,
    `button_text` VARCHAR(50),
    `button_link` VARCHAR(255),
    `image_url` VARCHAR(255),
    `bg_gradient_start` VARCHAR(20) DEFAULT '#000066',
    `bg_gradient_end` VARCHAR(20) DEFAULT '#000044',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
/* Insert default 'middle' banner (the one we just added statically) */
INSERT INTO `shop_ads_settings` (
        `tenant_id`,
        `banner_location`,
        `title`,
        `description`,
        `button_text`,
        `button_link`,
        `image_url`,
        `is_active`
    )
VALUES (
        1,
        'middle',
        'Grow Your Business with Casjoe Ads',
        'Reach thousands of potential customers.',
        'Start Advertising',
        '#',
        NULL,
        1
    );
/* Insert default 'top' banner placeholder */
INSERT INTO `shop_ads_settings` (
        `tenant_id`,
        `banner_location`,
        `title`,
        `description`,
        `button_text`,
        `button_link`,
        `image_url`,
        `is_active`
    )
VALUES (
        1,
        'top',
        'Special Offer!',
        'Get 50% off on all electronics this week.',
        'Shop Now',
        '/shop?category=Electronics',
        NULL,
        0
    );


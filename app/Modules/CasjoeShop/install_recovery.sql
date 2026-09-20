-- Shop Products
CREATE TABLE IF NOT EXISTS `shop_products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `vendor_id` INT,
    -- Nullable if platform product
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `category` VARCHAR(100),
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `stock_quantity` INT DEFAULT 0,
    `sku` VARCHAR(100),
    `image` VARCHAR(255),
    `type` ENUM('physical', 'digital') DEFAULT 'physical',
    `file_path` VARCHAR(255),
    -- For digital products
    `status` ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_tenant` (`tenant_id`),
    KEY `idx_vendor` (`vendor_id`)
);
-- Shop Vendors
CREATE TABLE IF NOT EXISTS `shop_vendors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `store_name` VARCHAR(255) NOT NULL,
    `store_slug` VARCHAR(255) UNIQUE,
    `description` TEXT,
    `logo` VARCHAR(255),
    `currency` VARCHAR(10) DEFAULT 'NGN',
    `slug` VARCHAR(255) NULL,
    `fee_bearer` ENUM('merchant', 'customer') DEFAULT 'merchant',
    `facebook_pixel_id` VARCHAR(255) NULL,
    `status` ENUM('pending', 'approved', 'suspended') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Shop Product Reviews
CREATE TABLE IF NOT EXISTS `shop_product_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` TINYINT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_product` (`product_id`)
);
-- Shop Orders
CREATE TABLE IF NOT EXISTS `shop_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM(
        'pending',
        'paid',
        'shipped',
        'delivered',
        'cancelled'
    ) DEFAULT 'pending',
    `payment_method` VARCHAR(50),
    `shipping_address` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Shop Order Items
CREATE TABLE IF NOT EXISTS `shop_order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `vendor_id` INT,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    KEY `idx_order` (`order_id`)
);
-- Shop Digital Access (For downloads)
CREATE TABLE IF NOT EXISTS `shop_digital_access` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_item_id` INT NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `max_downloads` INT DEFAULT 5,
    `download_count` INT DEFAULT 0,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_token` (`token`)
);
-- Shop Wishlists
CREATE TABLE IF NOT EXISTS `shop_wishlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_product` (`user_id`, `product_id`)
);
-- Ads Settings (Ensure this exists too)
CREATE TABLE IF NOT EXISTS `shop_ads_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `banner_location` VARCHAR(50) NOT NULL,
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
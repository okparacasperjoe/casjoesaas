SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";
-- --------------------------------------------------------
-- CORE TABLES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL COMMENT 'Custom domain',
  `subdomain` varchar(255) NOT NULL COMMENT 'Subdomain prefix',
  `status` enum('active', 'inactive', 'pending') DEFAULT 'active',
  `onboarding_step` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subdomain` (`subdomain`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin', 'user') NOT NULL DEFAULT 'user',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires_at` timestamp NULL DEFAULT NULL,
  `referral_code` varchar(50) DEFAULT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10, 2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `tenant_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `status` enum('enabled', 'disabled') NOT NULL DEFAULT 'disabled',
  `plan_type` varchar(20) DEFAULT 'free',
  `usage_count` int(11) DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `last_payment_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_module` (`tenant_id`, `module_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_tenant` (`setting_key`, `tenant_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- --------------------------------------------------------
-- CMS TABLES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `cms_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- --------------------------------------------------------
-- BILLING & SUBSCRIPTIONS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `plan` varchar(50) NOT NULL DEFAULT 'free',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `next_billing_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `billing_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `provider` varchar(50) DEFAULT 'flutterwave',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  UNIQUE KEY `reference` (`reference`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- --------------------------------------------------------
-- SELLING MODULES (CASJOE PAY, ERP, Academy etc omitted for brevity in first chunk, will append if needed or include core ERP)
-- --------------------------------------------------------
-- (I will append the module-specific tables next or just keep these core ones if that's what's most critical)
-- Actually, the user's deploy.sql was 100KB, so it had EVERYTHING. 
-- I'll keep the module tables from the existing file but clean up the Core.
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
-- Academy Module Tables
CREATE TABLE IF NOT EXISTS academy_courses (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT, thumbnail VARCHAR(255) DEFAULT '/assets/course_placeholder.jpg', price DECIMAL(10, 2) DEFAULT 0.00, status ENUM('draft', 'published') DEFAULT 'draft', is_system_course TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS academy_sections (id INT AUTO_INCREMENT PRIMARY KEY, course_id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_order INT DEFAULT 0, FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS academy_lessons (id INT AUTO_INCREMENT PRIMARY KEY, section_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT, video_url VARCHAR(255), duration_minutes INT DEFAULT 0, sort_order INT DEFAULT 0, FOREIGN KEY (section_id) REFERENCES academy_sections(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS academy_enrollments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, course_id INT NOT NULL, progress_percent INT DEFAULT 0, enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS academy_licenses (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, course_id INT NOT NULL, seats_total INT NOT NULL DEFAULT 0, seats_used INT NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_license (tenant_id, course_id));
CREATE TABLE IF NOT EXISTS academy_certificates (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT NOT NULL, course_id INT NOT NULL, certificate_code VARCHAR(100) UNIQUE, issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);


-- Casjoe Pay Tables
CREATE TABLE IF NOT EXISTS cp_wallets (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT NOT NULL, currency VARCHAR(3) NOT NULL DEFAULT 'NGN', balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY user_id (user_id));
CREATE TABLE IF NOT EXISTS cp_transactions (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT DEFAULT NULL, reference VARCHAR(100) NOT NULL, type ENUM('credit', 'debit') NOT NULL, amount DECIMAL(15, 2) NOT NULL, currency VARCHAR(3) NOT NULL DEFAULT 'NGN', status ENUM('pending', 'successful', 'failed') NOT NULL DEFAULT 'pending', description VARCHAR(255) DEFAULT NULL, meta LONGTEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY user_id (user_id), KEY reference (reference));
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `provider` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT 'credit',
  `recipient_account` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  UNIQUE KEY `reference` (`reference`)
);
CREATE TABLE IF NOT EXISTS cp_payment_links (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT NOT NULL, slug VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, amount DECIMAL(15, 2) DEFAULT NULL, currency VARCHAR(3) NOT NULL DEFAULT 'NGN', redirect_url VARCHAR(255) DEFAULT NULL, views INT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY slug (slug), KEY user_id (user_id));

-- Casjoe Mail Tables
CREATE TABLE IF NOT EXISTS cm_lists (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY tenant_id (tenant_id));
CREATE TABLE IF NOT EXISTS cm_subscribers (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, list_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, status ENUM('subscribed', 'unsubscribed') NOT NULL DEFAULT 'subscribed', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY email_list (email, list_id, tenant_id));
CREATE TABLE IF NOT EXISTS cm_templates (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS cm_campaigns (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content TEXT NOT NULL, list_id INT DEFAULT NULL, status ENUM('draft', 'scheduled', 'sent') NOT NULL DEFAULT 'draft', sent_count INT NOT NULL DEFAULT 0, open_count INT NOT NULL DEFAULT 0, sent_at TIMESTAMP NULL DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY tenant_id (tenant_id));

-- Casjoe Support Tables
CREATE TABLE IF NOT EXISTS cs_tickets (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT NOT NULL, subject VARCHAR(255) NOT NULL, status ENUM('open', 'answered', 'closed') DEFAULT 'open', priority ENUM('low', 'medium', 'high') DEFAULT 'medium', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY tenant_id (tenant_id), KEY user_id (user_id));
CREATE TABLE IF NOT EXISTS cs_messages (id INT AUTO_INCREMENT PRIMARY KEY, ticket_id INT NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, is_staff BOOLEAN DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY ticket_id (ticket_id));

-- Casjoe ERP Core Tables
CREATE TABLE IF NOT EXISTS erp_gl_accounts (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, type ENUM('asset','liability','equity','income','expense') NOT NULL, balance DECIMAL(15,2) NOT NULL DEFAULT 0.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY code (code, tenant_id));
CREATE TABLE IF NOT EXISTS erp_employees (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, department VARCHAR(100) DEFAULT NULL, position VARCHAR(100) DEFAULT NULL, salary DECIMAL(15,2) NOT NULL DEFAULT 0.00, hired_date DATE NOT NULL, status ENUM('active','terminated','leave') NOT NULL DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY tenant_id (tenant_id));
CREATE TABLE IF NOT EXISTS erp_customers (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, status ENUM('lead','customer','inactive') NOT NULL DEFAULT 'lead', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY tenant_id (tenant_id));


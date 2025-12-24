-- Database Update for Casjoe Academy & Casjoe Pay

-- --------------------------------------------------------
-- Update: Academy Tables
-- --------------------------------------------------------

-- Add columns to academy_courses
ALTER TABLE `academy_courses`
ADD COLUMN `slug` varchar(255) DEFAULT NULL,
ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT 0.00,
ADD COLUMN `image_url` varchar(255) DEFAULT NULL,
ADD COLUMN `category` varchar(100) DEFAULT 'Business',
ADD UNIQUE KEY `course_slug` (`slug`, `tenant_id`);

-- Create academy_lessons
CREATE TABLE `academy_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_free_preview` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `tenant_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `academy_lessons`
  ADD CONSTRAINT `fk_lessons_course` FOREIGN KEY (`course_id`) REFERENCES `academy_courses` (`id`) ON DELETE CASCADE;

-- Create academy_enrollments
CREATE TABLE `academy_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_course` (`user_id`, `course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Update: Casjoe Pay Tables
-- --------------------------------------------------------

-- Wallets
CREATE TABLE `cp_wallets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_currency` (`user_id`, `currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Virtual Cards
CREATE TABLE `cp_virtual_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `card_id` varchar(100) NOT NULL COMMENT 'Flutterwave Card ID',
  `start_month` varchar(2) NOT NULL,
  `start_year` varchar(2) NOT NULL,
  `masked_pan` varchar(20) NOT NULL,
  `card_type` varchar(20) DEFAULT 'MasterCard',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status` enum('active','blocked','terminated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment Links
CREATE TABLE `cp_payment_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Null means open amount',
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`, `tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update Transactions table to support more types
ALTER TABLE `transactions` 
MODIFY COLUMN `status` enum('pending','successful','failed','reversed') NOT NULL DEFAULT 'pending',
ADD COLUMN `type` enum('deposit','withdrawal','transfer','card_funding','payment') NOT NULL DEFAULT 'payment',
ADD COLUMN `recipient_account` varchar(255) DEFAULT NULL;


CREATE TABLE IF NOT EXISTS `tenant_ai_employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `employee_type` varchar(50) NOT NULL COMMENT 'e.g. sales_manager, accountant',
  `status` enum('active','suspended','cancelled') NOT NULL DEFAULT 'active',
  `subscription_id` varchar(100) DEFAULT NULL,
  `monthly_fee` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `currency` varchar(3) NOT NULL DEFAULT 'NGN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  UNIQUE KEY `tenant_employee` (`tenant_id`, `employee_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tenant_ai_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `token_balance` bigint(20) NOT NULL DEFAULT 0,
  `lifetime_purchased` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

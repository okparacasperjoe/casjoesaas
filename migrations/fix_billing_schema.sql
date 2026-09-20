-- Fix missing provider column in billing_invoices
ALTER TABLE billing_invoices ADD COLUMN provider VARCHAR(50) DEFAULT 'flutterwave' AFTER status;

-- Ensure transactions table exists (used in BillingController and WalletController)
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

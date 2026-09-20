<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Patching Pay Schema...\n";

try {
    // 1. Virtual Cards
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cp_virtual_cards` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tenant_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `card_id` varchar(255) NOT NULL,
      `masked_pan` varchar(20) NOT NULL,
      `currency` varchar(3) NOT NULL DEFAULT 'USD',
      `balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
      `start_month` varchar(2) DEFAULT NULL,
      `start_year` varchar(2) DEFAULT NULL,
      `status` enum('active', 'inactive', 'frozen') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "[OK] cp_virtual_cards checked.\n";

    // 2. Payment Links (if missing)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cp_payment_links` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tenant_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `slug` varchar(50) NOT NULL,
      `title` varchar(255) NOT NULL,
      `amount` decimal(15, 2) DEFAULT NULL,
      `currency` varchar(3) NOT NULL DEFAULT 'NGN',
      `redirect_url` varchar(255) DEFAULT NULL,
      `views` int(11) DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "[OK] cp_payment_links checked.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

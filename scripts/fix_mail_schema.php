<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Patching Mail Schema...\n";

try {
    // 1. Add missing columns to cm_subscribers
    try {
        $pdo->query("SELECT company FROM cm_subscribers LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE `cm_subscribers` 
            ADD COLUMN `company` varchar(255) DEFAULT NULL AFTER `last_name`,
            ADD COLUMN `phone` varchar(50) DEFAULT NULL AFTER `company`,
            ADD COLUMN `tags` text DEFAULT NULL AFTER `phone`
        ");
        echo "[OK] Added columns to cm_subscribers.\n";
    }

    // 2. Create cm_analytics table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cm_analytics` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tenant_id` int(11) NOT NULL,
        `campaign_id` int(11) NOT NULL,
        `subscriber_id` int(11) NOT NULL,
        `hash` varchar(64) NOT NULL,
        `status` enum('sent', 'opened', 'clicked') NOT NULL DEFAULT 'sent',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `opened_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `hash` (`hash`),
        KEY `campaign_id` (`campaign_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "[OK] Checked cm_analytics table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

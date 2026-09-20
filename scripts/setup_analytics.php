<?php
require 'app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();

echo "Creating Analytics Table...\n";
$sql = "CREATE TABLE IF NOT EXISTS cm_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    campaign_id INT NOT NULL,
    subscriber_id INT NOT NULL,
    hash VARCHAR(64) NOT NULL,
    status ENUM('sent', 'delivered', 'opened', 'clicked', 'bounced') DEFAULT 'sent',
    opened_at DATETIME DEFAULT NULL,
    clicked_at DATETIME DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_hash (hash),
    KEY idx_campaign (campaign_id)
)";

try {
    $pdo->exec($sql);
    echo "Table cm_analytics created.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

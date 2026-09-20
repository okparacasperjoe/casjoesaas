<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Create links_analytics table
    $db->exec("CREATE TABLE IF NOT EXISTS links_analytics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        link_type ENUM('bio', 'short', 'static', 'qr') NOT NULL,
        link_id INT NOT NULL,
        visitor_ip VARCHAR(45) NULL,
        country VARCHAR(255) NULL,
        city VARCHAR(255) NULL,
        device_type VARCHAR(50) NULL,
        os VARCHAR(50) NULL,
        browser VARCHAR(50) NULL,
        referrer TEXT NULL,
        visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created links_analytics table.\n";

    // Add indexes
    try {
         $db->exec("CREATE INDEX idx_links_analytics_link ON links_analytics(link_type, link_id)");
         $db->exec("CREATE INDEX idx_links_analytics_date ON links_analytics(visited_at)");
         echo "Added indexes.\n";
    } catch (Exception $e) {
        echo "Indexes might already exist.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

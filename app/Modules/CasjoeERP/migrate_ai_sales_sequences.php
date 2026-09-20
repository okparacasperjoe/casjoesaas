<?php
require_once __DIR__ . '/../../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS erp_ai_sales_sequences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        invoice_id INT NOT NULL,
        customer_id INT NOT NULL,
        step INT NOT NULL DEFAULT 1,
        status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
        last_run_at DATETIME NULL,
        next_run_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY idx_invoice_sequence (invoice_id)
    );
    ";

    $db->exec($sql);
    echo "AI Sales Sequence tables created successfully.\n";

} catch (Exception $e) {
    die("Error creating tables: " . $e->getMessage() . "\n");
}

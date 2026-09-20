<?php
require_once __DIR__ . '/../../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS erp_invoice_reminders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        days_offset INT NOT NULL DEFAULT 0,
        subject VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS erp_invoice_reminder_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT NOT NULL,
        reminder_id INT NOT NULL,
        sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY idx_invoice_reminder (invoice_id, reminder_id)
    );
    ";

    $db->exec($sql);
    echo "Invoice Reminders tables created successfully.\n";

} catch (Exception $e) {
    die("Error creating tables: " . $e->getMessage() . "\n");
}

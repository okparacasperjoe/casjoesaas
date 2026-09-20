<?php
require_once __DIR__ . '/../../../app/Core/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.<br>";

    // Add frequency_days to erp_invoice_reminders
    try {
        $db->exec("ALTER TABLE erp_invoice_reminders ADD COLUMN frequency_days INT DEFAULT 0 AFTER days_offset");
        echo "Successfully added frequency_days column.<br>";
    } catch (\Exception $e) {
        echo "Column frequency_days might already exist. " . $e->getMessage() . "<br>";
    }

    // Drop unique constraint from logs
    try {
        $db->exec("ALTER TABLE erp_invoice_reminder_logs DROP INDEX idx_invoice_reminder");
        echo "Successfully dropped unique constraint idx_invoice_reminder.<br>";
    } catch (\Exception $e) {
        echo "Unique constraint might have already been dropped. " . $e->getMessage() . "<br>";
    }

    echo "<br><b>Migration completed successfully!</b>";
} catch (\Exception $e) {
    die("Error running migration: " . $e->getMessage() . "<br>");
}

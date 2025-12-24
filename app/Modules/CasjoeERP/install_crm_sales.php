<?php
require_once __DIR__ . '/../../core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/crm_sales_install.sql');

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }
    echo "CRM Sales tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing CRM Sales tables: " . $e->getMessage() . "\n";
}

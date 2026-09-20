<?php
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$sql = file_get_contents(__DIR__ . '/sql/install_scheduler.sql');

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }
    echo "Scheduler tables installed successfully.\n";
} catch (PDOException $e) {
    echo "Error installing Scheduler tables: " . $e->getMessage() . "\n";
}

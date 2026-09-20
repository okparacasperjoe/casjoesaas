<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $tables = ['cm_lists', 'cm_subscribers', 'cm_templates', 'cm_campaigns'];
    foreach ($tables as $tb) {
        $stmt = $db->query("SHOW TABLES LIKE '$tb'");
        echo "$tb: " . ($stmt->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

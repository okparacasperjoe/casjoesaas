<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in users table:\n";
    foreach ($columns as $col) {
        echo "- " . $col . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

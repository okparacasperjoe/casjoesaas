<?php
require_once __DIR__ . '/app/Core/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, name, email, role FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

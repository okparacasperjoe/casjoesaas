<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$db = Database::getInstance();
try {
    $stmt = $db->query("DESC users");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo $e->getMessage();
}

<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT * FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($modules);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

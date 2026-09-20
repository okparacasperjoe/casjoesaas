<?php
require_once __DIR__ . '/../app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
echo "Inspecting academy_enrollments columns:\n";
try {
    $stmt = $pdo->query("DESCRIBE academy_enrollments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Table academy_enrollments not found or error: " . $e->getMessage() . "\n";
}

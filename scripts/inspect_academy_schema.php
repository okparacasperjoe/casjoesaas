<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Inspecting academy_courses columns:\n";
$stmt = $pdo->query("DESCRIBE academy_courses");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

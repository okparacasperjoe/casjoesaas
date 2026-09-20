<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Show Create Table:\n";
$stmt = $pdo->query("SHOW CREATE TABLE academy_lessons");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['Create Table'] . "\n";

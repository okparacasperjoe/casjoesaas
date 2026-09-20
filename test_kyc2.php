<?php
require_once __DIR__ . "/../app/Core/Database.php";
$pdo = \App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT * FROM cp_naira_card_users");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Naira Card Users Count: " . count($rows) . "\n";
print_r($rows);
@unlink(__FILE__);

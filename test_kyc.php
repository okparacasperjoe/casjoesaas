<?php
require_once __DIR__ . "/../app/Core/Database.php";
$pdo = \App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT * FROM cp_naira_card_users WHERE tenant_id = 14 OR user_id = 14");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Naira Card Users:\n";
print_r($rows);

$stmt2 = $pdo->query("SELECT id, tenant_id, is_verified FROM users WHERE id = 14 OR tenant_id = 14 LIMIT 5");
echo "\nUsers table:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));

@unlink(__FILE__);

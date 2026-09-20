<?php
require_once __DIR__ . '/app/Core/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE cp_virtual_cards");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

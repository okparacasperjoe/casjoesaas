<?php
require 'app/Core/bootstrap.php';
$db = App\Core\Database::getInstance();
$stmt = $db->query("SHOW COLUMNS FROM tenants");
header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);

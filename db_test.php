<?php
require 'app/Core/bootstrap.php';
$db = App\Core\Database::getInstance();
$stmt = $db->query("SHOW COLUMNS FROM tenants");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

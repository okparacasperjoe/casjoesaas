<?php
require 'app/Core/bootstrap.php';
$pdo = \App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("DESCRIBE tenants");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

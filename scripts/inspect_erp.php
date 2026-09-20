<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;
$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->query("DESCRIBE erp_employees");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

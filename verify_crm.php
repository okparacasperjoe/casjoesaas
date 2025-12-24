<?php
require 'app/core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query('SELECT name FROM erp_crm_stages');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

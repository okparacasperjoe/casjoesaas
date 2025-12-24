<?php
require 'app/core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("DESCRIBE erp_payroll");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $col) {
    echo $col['Field'] . "\n";
}

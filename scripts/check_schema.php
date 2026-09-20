<?php
require 'app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();

echo "--- cm_templates ---\n";
try {
    $rows = $pdo->query("DESCRIBE cm_templates")->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r) echo $r['Field'] . "\n";
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

echo "\n--- cm_subscribers ---\n";
try {
    $rows = $pdo->query("DESCRIBE cm_subscribers")->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r) echo $r['Field'] . "\n";
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

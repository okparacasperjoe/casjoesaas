<?php
// install_sops.php
require_once __DIR__ . '/../../../app/Core/bootstrap.php';

use App\Core\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    $sql = file_get_contents(__DIR__ . '/sql/sops_install.sql');
    
    $pdo->exec($sql);
    
    echo "SOPs module tables created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating SOPs tables: " . $e->getMessage() . "\n";
}

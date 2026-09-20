<?php
require_once __DIR__ . "/../app/Core/Database.php";
$pdo = \App\Core\Database::getInstance()->getConnection();

try {
    $pdo->query("TRUNCATE TABLE cp_naira_card_users");
    echo "Successfully cleared all KYC profiles in cp_naira_card_users.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

@unlink(__FILE__);

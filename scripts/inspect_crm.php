<?php
require_once 'app/core/bootstrap.php';
use App\Core\Database;
$pdo = Database::getInstance()->getConnection();
echo "Leads:\n";
print_r($pdo->query("DESCRIBE erp_crm_leads")->fetchAll(PDO::FETCH_ASSOC));
echo "\nStages:\n";
try {
    print_r($pdo->query("DESCRIBE erp_crm_stages")->fetchAll(PDO::FETCH_ASSOC));
} catch(Exception $e) { echo "Stages table missing.\n"; }

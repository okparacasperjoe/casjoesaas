<?php
// Run SQL Migration
require_once __DIR__ . '/app/Core/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = file_get_contents(__DIR__ . '/app/Modules/CasjoeERP/sql/chat_install.sql');
    
    $db->exec($sql);
    echo "Migration Successful!";
} catch (\Exception $e) {
    echo "Migration Failed: " . $e->getMessage();
}

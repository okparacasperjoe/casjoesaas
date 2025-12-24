<?php
require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Database;

$email = 'admin@localhost'; 

try {
    $db = Database::getInstance();
    $stmt = $db->query("UPDATE users SET two_factor_enabled = 0 WHERE email = ?", [$email]);
    
    echo "2FA has been disabled for user: $email\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

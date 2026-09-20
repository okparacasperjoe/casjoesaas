<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance()->getConnection();

try {
    echo "Testing SELECT referral_code...\n";
    $stmt = $db->query("SELECT referral_code FROM users LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Select Result: " . print_r($row, true) . "\n";

    echo "Testing UPDATE referral_code...\n";
    if ($row) {
        $db->query("UPDATE users SET referral_code = 'TEST1234' WHERE id = 1"); // Assuming id 1 exists or whatever
        echo "Update Success.\n";
    }

    echo "Testing INSERT with referral_code...\n";
    $email = "debug_" . uniqid() . "@test.com";
    $db->query("INSERT INTO users (tenant_id, email, password, referral_code) VALUES (1, '$email', 'password', 'REFDEBUG')");
    echo "Insert Success.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

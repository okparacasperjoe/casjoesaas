<?php
require_once __DIR__ . "/../app/Core/Database.php";
$pdo = \App\Core\Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'strowallet_public_key'");
echo "PUB: [" . $stmt->fetchColumn() . "]\n";
$stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'strowallet_secret_key'");
echo "SEC: [" . $stmt->fetchColumn() . "]\n";
@unlink(__FILE__);

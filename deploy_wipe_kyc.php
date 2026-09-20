<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

$script = '<?php
require_once __DIR__ . "/../app/Core/Database.php";
$pdo = \App\Core\Database::getInstance()->getConnection();

try {
    $pdo->query("TRUNCATE TABLE cp_naira_card_users");
    echo "Successfully cleared all KYC profiles in cp_naira_card_users.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

@unlink(__FILE__);
';

file_put_contents('wipe_kyc.php', $script);
ftp_put($conn, '/public/wipe_kyc.php', 'wipe_kyc.php', FTP_BINARY);
ftp_close($conn);

$context = stream_context_create([
    'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n'
    ]
]);
echo file_get_contents('https://app.casjoe.com/wipe_kyc.php', false, $context);

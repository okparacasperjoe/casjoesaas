<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

$script = '<?php
require_once __DIR__ . "/../app/Core/bootstrap.php";
require_once __DIR__ . "/../app/Modules/CasjoePay/Services/StroWalletService.php";

$strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
$res = $strowallet->createCustomer(
    "miracle" . time() . "@casjoe.com",
    "Miracle",
    "Casper",
    "+2347035323038",
    "1990-04-15",
    "85170798348",
    "https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg",
    "https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg"
);
echo "Create Customer Response:\n";
print_r($res);
@unlink(__FILE__);
';

file_put_contents('test_stro4.php', $script);
ftp_put($conn, '/public/test_stro4.php', 'test_stro4.php', FTP_BINARY);
ftp_close($conn);

$context = stream_context_create([
    'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n',
        'timeout' => 120
    ]
]);
echo file_get_contents('https://app.casjoe.com/test_stro4.php', false, $context);

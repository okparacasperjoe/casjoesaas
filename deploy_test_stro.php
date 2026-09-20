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
    "hello@casjoe.com",
    "Casper",
    "Okpara",
    "+2347035323038",
    "1990-04-15",
    "85170798348"
);
echo "Create Customer Response:\n";
print_r($res);

$cardRes = $strowallet->createCard("hello@casjoe.com", "USD", 10, "Casper Okpara");
echo "\nCreate Card Response:\n";
print_r($cardRes);

@unlink(__FILE__);
';

file_put_contents('test_strowallet.php', $script);
ftp_put($conn, '/public/test_strowallet.php', 'test_strowallet.php', FTP_BINARY);
ftp_close($conn);

$context = stream_context_create([
    'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n'
    ]
]);
echo file_get_contents('https://app.casjoe.com/test_strowallet.php', false, $context);

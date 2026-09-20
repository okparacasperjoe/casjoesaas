<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

if (ftp_get($conn, 'StroWalletService_live.php', '/app/Modules/CasjoePay/Services/StroWalletService.php', FTP_BINARY)) {
    echo "Downloaded live StroWalletService.php successfully.\n";
} else {
    echo "Failed to download.\n";
}
ftp_close($conn);

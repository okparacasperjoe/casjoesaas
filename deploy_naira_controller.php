<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

$file = "app/Modules/CasjoePay/Controllers/NairaCardController.php";
if (ftp_put($conn, "/$file", $file, FTP_BINARY)) {
    echo "Deployed NairaCardController.php to sandbox mode.\n";
} else {
    echo "Failed to deploy.\n";
}
ftp_close($conn);

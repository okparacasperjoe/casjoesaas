<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

if (ftp_get($conn, "sw_debug.log", "/public/sw_debug.log", FTP_BINARY)) {
    echo "Downloaded sw_debug.log successfully.";
} else {
    echo "Failed to download sw_debug.log.";
}
ftp_close($conn);

<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

$files = [
    "public/debug_card.php",
];

function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath) {
    @ftp_chdir($ftpcon, $ftpbasedir);
    $parts = explode('/', $ftpath);
    foreach ($parts as $part) {
        if (!$part) continue;
        if (!@ftp_chdir($ftpcon, $part)) {
            @ftp_mkdir($ftpcon, $part);
            ftp_chdir($ftpcon, $part);
        }
    }
}

$success = 0; $failed = 0;
foreach ($files as $file) {
    if (!file_exists($file)) { echo "[SKIP] $file not found\n"; continue; }
    $remotePath = "/" . $file;
    $remoteDir = dirname($remotePath);
    ftp_mksubdirs($conn, '/', trim($remoteDir, '/'));
    if (ftp_put($conn, $remotePath, $file, FTP_BINARY)) {
        echo "[SUCCESS] $file\n"; $success++;
    } else {
        echo "[ERROR] $file\n"; $failed++;
    }
}
ftp_close($conn);
echo "Done: $success ok, $failed failed.\n";

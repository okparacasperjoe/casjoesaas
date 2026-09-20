<?php
$conn = ftp_connect("ftp.casjoe.com");
if (!$conn) die("Could not connect\n");
if (!ftp_login($conn, "app@casjoe.com", "app@casjoe.com")) die("Login failed\n");
ftp_pasv($conn, true);

$files = [
    "app/Modules/CasjoeERP/Controllers/LeaveController.php",
    "app/Modules/CasjoeERP/Controllers/ProjectController.php",
    "app/Modules/CasjoeERP/Controllers/FinanceController.php",
    "app/Modules/CasjoeERP/Controllers/SystemController.php",
    "app/Modules/CasjoeERP/Controllers/PayrollController.php"
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

$success = 0;
$failed = 0;

foreach ($files as $file) {
    if (!file_exists($file)) continue;

    $remotePath = "/" . $file;
    $remoteDir = dirname($remotePath);
    ftp_mksubdirs($conn, '/', trim($remoteDir, '/'));

    if (ftp_put($conn, $remotePath, $file, FTP_BINARY)) {
        echo "[SUCCESS] Uploaded $file to $remotePath\n";
        $success++;
    } else {
        echo "[ERROR] Failed to upload $file\n";
        $failed++;
    }
}

ftp_close($conn);
echo "Deployment complete: $success uploaded, $failed failed.\n";

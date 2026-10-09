<?php
echo "========================================================\n";
echo "   DEPLOYING CORI AI EXECUTIVE BRIEFING POPUP TO LIVE   \n";
echo "========================================================\n\n";

$ftpHost = 'ftp.casjoe.com';
$ftpUser = 'app@casjoe.com';
$ftpPass = 'app@casjoe.com';

$conn = @ftp_connect($ftpHost);
if (!$conn) {
    echo "Direct connection to $ftpHost failed, falling back to ftp.gb.stackcp.com...\n";
    $conn = @ftp_connect('ftp.gb.stackcp.com');
}

if (!$conn) {
    die("ERROR: Could not connect to FTP host.\n");
}

if (!@ftp_login($conn, $ftpUser, $ftpPass)) {
    die("ERROR: FTP login failed.\n");
}

ftp_pasv($conn, true);
echo "FTP Connected & Authenticated successfully!\n\n";

function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath) {
    @ftp_chdir($ftpcon, $ftpbasedir);
    $parts = explode('/', $ftpath);
    foreach ($parts as $part) {
        if (!$part) continue;
        if (!@ftp_chdir($ftpcon, $part)) {
            @ftp_mkdir($ftpcon, $part);
            @ftp_chdir($ftpcon, $part);
        }
    }
    @ftp_chdir($ftpcon, $ftpbasedir);
}

$files = [
    'app/Core/Controllers/GlobalDashboardController.php',
    'app/Core/Views/global_dashboard.php',
];

$successCount = 0;
$failCount = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "[SKIP] Local file not found: $file\n";
        $failCount++;
        continue;
    }

    $remoteFile = '/' . str_replace('\\', '/', $file);
    $remoteDir = dirname($remoteFile);

    ftp_mksubdirs($conn, '/', trim($remoteDir, '/'));

    echo "Uploading: $file -> $remoteFile ... ";
    if (ftp_put($conn, $remoteFile, $file, FTP_BINARY)) {
        echo "[OK] (" . filesize($file) . " bytes)\n";
        $successCount++;
    } else {
        echo "[FAILED]\n";
        $failCount++;
    }
}

ftp_close($conn);

echo "\nDeployment Complete: $successCount uploaded, $failCount failed.\n";

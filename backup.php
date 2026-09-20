<?php
$backupDir = 'C:\\CasjoeBackups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$date = date('Y-m-d_Hi');
$dbFile = $backupDir . '\\casjoe_db_' . $date . '.sql';
$zipFile = $backupDir . '\\casjoe_files_' . $date . '.zip';

echo "===========================================\n";
echo "      CASJOE LOCALHOST BACKUP SCRIPT       \n";
echo "===========================================\n\n";

// 1. Database Backup
echo "[1/2] Backing up the MySQL database...\n";
$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
if (!file_exists($mysqldumpPath)) {
    $mysqldumpPath = 'mysqldump'; // Fallback to PATH
}

$dbName = 'saas_db';
$dbUser = 'root';
$command = "\"$mysqldumpPath\" -u $dbUser $dbName > \"$dbFile\"";

exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    echo "Database backup failed! Ensure MySQL is running.\n";
} else {
    echo "Database backed up successfully to: $dbFile\n";
}

echo "\n[2/2] Backing up the project files (ignoring heavy folders)...\n";

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $source = __DIR__;
    
    // Create recursive directory iterator
    $files = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            function ($file, $key, $iterator) use ($source) {
                $filename = $file->getFilename();
                // Exclude specific directories
                if ($iterator->hasChildren() && in_array($filename, ['.git', 'vendor', 'node_modules', 'tmp', 'storage'])) {
                    return false;
                }
                return true;
            }
        )
    );

    $count = 0;
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            $zip->addFile($filePath, $relativePath);
            $count++;
        }
    }
    $zip->close();
    echo "Project files zipped successfully! ($count files)\n";
    echo "Saved to: $zipFile\n";
} else {
    echo "Failed to create zip file!\n";
}

echo "\n===========================================\n";
echo "BACKUP COMPLETE!\n";
echo "Files saved in: $backupDir\n";
echo "===========================================\n";
?>

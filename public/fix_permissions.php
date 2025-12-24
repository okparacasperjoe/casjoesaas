<?php
// Fix Permissions Script
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Permission Fixer</h1>";

function fix_permissions($path) {
    if (!file_exists($path)) {
        echo "Path not found: $path<br>";
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $fullPath = $item->getRealPath();
        
        if ($item->isDir()) {
            if (chmod($fullPath, 0755)) {
                echo "DIR: $fullPath [FIXED]<br>";
            } else {
                echo "<span style='color:red'>DIR: $fullPath [FAILED]</span><br>";
            }
        } else {
            if (chmod($fullPath, 0644)) {
                // echo "FILE: $fullPath [FIXED]<br>"; // Too noisy
            } else {
                echo "<span style='color:red'>FILE: $fullPath [FAILED]</span><br>";
            }
        }
    }
}

// Fix Vendor
echo "<h2>Fixing Vendor...</h2>";
fix_permissions(__DIR__ . '/../vendor');

// Fix Storage
echo "<h2>Fixing Storage...</h2>";
fix_permissions(__DIR__ . '/../storage');

echo "<h2>Done! Try refreshing your site now.</h2>";

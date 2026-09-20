<?php
/**
 * Deployment Configuration
 * Used for automated uploads to the production server.
 */
return [
    'ftp_host'     => 'ftp.casjoe.com',
    'ftp_user'     => 'avellin@avellin.casjoe.com',
    'ftp_pass'     => 'casjoe.com',
    'ftp_root'     => '/public_html/avellin', // Correct public directory
    'ignore_files' => [
        '.git',
        '.env',
        'node_modules',
        'composer.lock',
        'database.local.php'
    ]
];

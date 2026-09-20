<?php
// Default database configuration
$config = [
    'host' => 'sdb-p.hosting.stackcp.net',
    'dbname' => 'casjoeapp-3139384215',
    'username' => 'casjoeapp-3139384215',
    'password' => 'casjoeapp2',
    'charset' => 'utf8mb4'
];

// Load local overrides if they exist
if (file_exists(__DIR__ . '/database.local.php')) {
    $localConfig = require __DIR__ . '/database.local.php';
    $config = array_merge($config, $localConfig);
}

return $config;

<?php

$config = [
    'host' => 'sdb-67.hosting.stackcp.net',
    'dbname' => 'casjoeapp-35303437c62a',
    'username' => 'casjoeapp-35303437c62a',
    'password' => '6xbr400e80',
    'charset' => 'utf8mb4'
];

if (file_exists(__DIR__ . '/database.local.php')) {
    $localConfig = require __DIR__ . '/database.local.php';
    $config = array_merge($config, $localConfig);
}

return $config;

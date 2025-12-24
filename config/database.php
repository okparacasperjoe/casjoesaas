<?php

$config = [
    'host' => '127.0.0.1',
    'dbname' => 'saas_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

if (file_exists(__DIR__ . '/database.local.php')) {
    $localConfig = require __DIR__ . '/database.local.php';
    $config = array_merge($config, $localConfig);
}

return $config;

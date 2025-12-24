<?php
require 'vendor/autoload.php';
use Minishlink\WebPush\VAPID;

try {
    $keys = VAPID::createVapidKeys();
    echo "Public: " . $keys['publicKey'] . "\n";
    echo "Private: " . $keys['privateKey'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

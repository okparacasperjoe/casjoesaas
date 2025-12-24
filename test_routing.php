<?php
// Simulate Browser Environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/support';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Header override removed

require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Router;

echo "--- Dispatching /support ---\n";
Router::dispatch();

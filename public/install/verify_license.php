<?php
session_start();
require __DIR__ . '/../../app/Core/Services/LicenseService.php';

use App\Core\Services\LicenseService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?step=0');
    exit;
}

$code = $_POST['purchase_code'] ?? '';

// We use the simulator in the service for now
$result = LicenseService::verify($code);

if ($result['success']) {
    $_SESSION['license_code'] = $code;
    $_SESSION['license_token'] = $result['token'];
    $_SESSION['license_verified'] = true;
    
    header('Location: index.php?step=1');
} else {
    die("<h1>Activation Failed</h1><p>{$result['message']}</p><a href='index.php?step=0'>Try Again</a>");
}

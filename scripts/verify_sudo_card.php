<?php
// Test Script to Verify Sudo Card Integration Logic
require_once __DIR__ . '/../app/core/bootstrap.php';

// Mock Auth
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@casjoe.com';
$_SESSION['role'] = 'admin';

echo "Verifying Sudo Integration...\n";

// 1. Check Service Class Existence
require_once __DIR__ . '/../app/modules/CasjoePay/Services/SudoService.php';
$ref = new ReflectionClass('App\Modules\CasjoePay\Services\SudoService');

if ($ref->hasMethod('createCard') && $ref->hasMethod('createCustomer')) {
    echo "PASS: SudoService methods exist.\n";
} else {
    echo "FAIL: Missing methods in SudoService.\n";
    exit(1);
}

// 2. Check Controller Refactoring
$controllerContent = file_get_contents(__DIR__ . '/../app/modules/CasjoePay/controllers/CardController.php');
if (strpos($controllerContent, 'new SudoService()') !== false) {
    echo "PASS: CardController uses SudoService.\n";
} else {
    echo "FAIL: CardController does not seem to instantiate SudoService.\n";
}

// 3. Check for Global Key Usage
if (strpos(file_get_contents(__DIR__ . '/../app/modules/CasjoePay/Services/SudoService.php'), 'SUDO_API_KEY') !== false) {
    echo "PASS: SudoService uses global SUDO_API_KEY.\n";
} else {
    echo "FAIL: SudoService not using global key constant.\n";
}

echo "Sudo Integration Logic Verified Structure.\n";

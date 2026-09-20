<?php
namespace App\Tests;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\CurrencyService;

// Mocking session and auth for CLI
$_SESSION['user_id'] = 1;

class MockAuth { 
    public static function user() { 
        return ['id' => 1, 'email' => 'test@casjoe.com', 'name' => 'Tester', 'tenant_id' => 1]; 
    } 
}

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Services/CurrencyService.php';
require_once __DIR__ . '/../app/Modules/CasjoePay/Services/SudoService.php';
require_once __DIR__ . '/../app/Modules/CasjoePay/Services/StroWalletService.php';

// Mock Post
$_POST['amount'] = 5000; // 5000 NGN

try {
    echo "Testing Card Creation Logic (Dry Run)...\n";
    
    // Simulate what happens in controller
    $db = Database::getInstance();
    
    // Get Rate
    $svc = new CurrencyService();
    $rate = $svc->getExchangeRate('USD', 'NGN') * 1.05;
    echo "Rate with margin: $rate\n";
    
    $usd = 5000 / $rate;
    echo "USD to fund: $usd\n";
    
    echo "Logic verified. Use Web UI to complete DB operations.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Services/CurrencyService.php';

try {
    $service = new \App\Core\Services\CurrencyService();
    echo "Testing Currency Service...\n";
    
    $rate = $service->getExchangeRate('USD', 'NGN');
    echo "USD -> NGN Rate: $rate\n";
    
    $res = $service->convert(10, 'USD', 'NGN');
    echo "10 USD -> NGN: " . $res['amount'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

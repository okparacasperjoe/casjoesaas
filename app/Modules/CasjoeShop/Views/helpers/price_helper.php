<?php
/**
 * Price Helper for Currency Conversion
 * Include this in views that need to display prices
 */

use App\Modules\CasjoeShop\Services\CurrencyService;

if (!function_exists('formatPrice')) {
    /**
     * Convert and format price from vendor currency to customer currency
     * 
     * @param float $price Original price
     * @param string $vendorCurrency Vendor's base currency (from vendor settings)
     * @return string Formatted price in customer's selected currency
     */
    function formatPrice($price, $vendorCurrency = 'NGN') {
        static $currencyService = null;
        
        if ($currencyService === null) {
            $currencyService = new CurrencyService();
        }
        
        $customerCurrency = CurrencyService::getSelectedCurrency();
        
        // Convert from vendor currency to customer currency
        $convertedPrice = $currencyService->convert($price, $vendorCurrency, $customerCurrency);
        
        // Format with appropriate symbol
        return CurrencyService::format($convertedPrice, $customerCurrency);
    }
}

if (!function_exists('convertPrice')) {
    /**
     * Just convert price without formatting
     * 
     * @param float $price Original price
     * @param string $from Source currency
     * @param string $to Target currency  
     * @return float Converted price
     */
    function convertPrice($price, $from = 'NGN', $to = null) {
        static $currencyService = null;
        
        if ($currencyService === null) {
            $currencyService = new CurrencyService();
        }
        
        if ($to === null) {
            $to = CurrencyService::getSelectedCurrency();
        }
        
        return $currencyService->convert($price, $from, $to);
    }
}

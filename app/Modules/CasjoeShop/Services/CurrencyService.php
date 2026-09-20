<?php

namespace App\Modules\CasjoeShop\Services;

use App\Core\Database;

class CurrencyService
{
    private $db;
    private const API_URL = 'https://api.exchangerate-api.com/v4/latest/';
    private const CACHE_HOURS = 24;
    
    // Supported currencies with symbols
    public static $currencies = [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦'],
        'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh'],
        'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R'],
        'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => 'GH₵'],
        'XOF' => ['name' => 'West African CFA Franc', 'symbol' => 'CFA'],
        'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh']
    ];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureRatesTable();
    }
    
    /**
     * Create currency_exchange_rates table if not exists
     */
    private function ensureRatesTable()
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS currency_exchange_rates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                base_currency VARCHAR(3) NOT NULL,
                target_currency VARCHAR(3) NOT NULL,
                rate DECIMAL(20, 8) NOT NULL,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_currencies (base_currency, target_currency)
            )");
        } catch (\Exception $e) {
            // Table might already exist
        }
    }
    
    /**
     * Get exchange rate from base to target currency
     * 
     * @param string $from Base currency code
     * @param string $to Target currency code
     * @return float Exchange rate
     */
    public function getRate($from, $to)
    {
        if ($from === $to) {
            return 1.0;
        }
        
        // Check cache first
        $cached = $this->getCachedRate($from, $to);
        if ($cached !== null) {
            return $cached;
        }
        
        // Fetch from API
        return $this->fetchAndCacheRate($from, $to);
    }
    
    /**
     * Convert amount from one currency to another
     * 
     * @param float $amount Amount to convert
     * @param string $from Source currency
     * @param string $to Target currency
     * @return float Converted amount
     */
    public function convert($amount, $from, $to)
    {
        $rate = $this->getRate($from, $to);
        return round($amount * $rate, 2);
    }
    
    /**
     * Get cached exchange rate
     */
    private function getCachedRate($from, $to)
    {
        $stmt = $this->db->query(
            "SELECT rate, last_updated FROM currency_exchange_rates 
             WHERE base_currency = ? AND target_currency = ? 
             AND last_updated > DATE_SUB(NOW(), INTERVAL ? HOUR)",
            [$from, $to, self::CACHE_HOURS]
        );
        
        $row = $stmt->fetch();
        return $row ? (float)$row['rate'] : null;
    }
    
    /**
     * Fetch rate from API and cache it
     */
    private function fetchAndCacheRate($from, $to)
    {
        try {
            $url = self::API_URL . $from;
            $response = @file_get_contents($url);
            
            if ($response === false) {
                // API failed, try reverse rate if cached
                return $this->getFallbackRate($from, $to);
            }
            
            $data = json_decode($response, true);
            
            if (!isset($data['rates'][$to])) {
                return $this->getFallbackRate($from, $to);
            }
            
            $rate = (float)$data['rates'][$to];
            
            // Cache the rate
            $this->cacheRate($from, $to, $rate);
            
            return $rate;
            
        } catch (\Exception $e) {
            return $this->getFallbackRate($from, $to);
        }
    }
    
    /**
     * Cache exchange rate in database
     */
    private function cacheRate($from, $to, $rate)
    {
        try {
            // Delete old rate
            $this->db->query(
                "DELETE FROM currency_exchange_rates WHERE base_currency = ? AND target_currency = ?",
                [$from, $to]
            );
            
            // Insert new rate
            $this->db->query(
                "INSERT INTO currency_exchange_rates (base_currency, target_currency, rate) VALUES (?, ?, ?)",
                [$from, $to, $rate]
            );
        } catch (\Exception $e) {
            // Silently fail cache write
        }
    }
    
    /**
     * Get fallback rate (try reverse or default to 1)
     */
    private function getFallbackRate($from, $to)
    {
        // Try getting reverse rate and calculating
        $reverseRate = $this->getCachedRate($to, $from);
        if ($reverseRate !== null && $reverseRate > 0) {
            return 1 / $reverseRate;
        }
        
        // Default fallback (assume 1:1 to prevent errors)
        error_log("Currency conversion failed: $from to $to, using 1:1 ratio");
        return 1.0;
    }
    
    /**
     * Format amount with currency symbol
     * 
     * @param float $amount Amount to format
     * @param string $currency Currency code
     * @return string Formatted currency string
     */
    public static function format($amount, $currency)
    {
        $symbol = self::$currencies[$currency]['symbol'] ?? $currency;
        
        // For currencies like KES, ZAR, put symbol before
        if (in_array($currency, ['KES', 'ZAR', 'GHS', 'XOF', 'UGX'])) {
            return $symbol . ' ' . number_format($amount, 2);
        }
        
        // For USD, GBP, EUR, symbol before with no space
        if (in_array($currency, ['USD', 'GBP', 'EUR'])) {
            return $symbol . number_format($amount, 2);
        }
        
        // NGN and others, symbol before
        return $symbol . number_format($amount, 2);
    }
    
    /**
     * Get user's selected currency from session (default NGN)
     */
    public static function getSelectedCurrency()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['shop_currency'] ?? 'NGN';
    }
    
    /**
     * Set user's selected currency
     */
    public static function setSelectedCurrency($currency)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset(self::$currencies[$currency])) {
            $_SESSION['shop_currency'] = $currency;
            return true;
        }
        return false;
    }
}

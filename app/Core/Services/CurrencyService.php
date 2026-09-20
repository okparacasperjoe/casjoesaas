<?php

namespace App\Core\Services;

class CurrencyService
{
    private $apiKey;
    private $baseUrl = 'https://v6.exchangerate-api.com/v6/';

    public function __construct()
    {
        // Fetch API Key from DB or Config
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'exchange_rate_api_key'");
        $this->apiKey = $stmt->fetchColumn();
    }

    public function getExchangeRate($from, $to)
    {
        // Custom Token Logic: CJC
        // Rate: 1 USD = 1000 CJC
        if ($from === 'CJC' && $to === 'USD') return 0.001;
        if ($from === 'USD' && $to === 'CJC') return 1000;

        // If converting CJC <-> Other (e.g. CJC -> NGN)
        // Convert CJC -> USD -> Target
        if ($from === 'CJC') {
            $usdRate = $this->getExchangeRate('USD', $to);
            return 0.001 * $usdRate;
        }
        // Convert Other -> CJC (e.g. NGN -> CJC)
        // Convert Other -> USD -> CJC
        if ($to === 'CJC') {
            $toUsd = $this->getExchangeRate($from, 'USD');
            return $toUsd * 1000;
        }

        if (empty($this->apiKey)) {
            // Fallback for testing/demo if no key provided
            if ($from === 'USD' && $to === 'NGN') return 1500;
            if ($from === 'NGN' && $to === 'USD') return 0.00067;
            return 1; // Default 1:1 if unknown
        }

        // Check Cache (File-based for simplicity)
        $cacheFile = __DIR__ . '/../../../../cache/rates_' . $from . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            $data = json_decode(file_get_contents($cacheFile), true);
        } else {
            // Fetch from API
            $url = $this->baseUrl . $this->apiKey . '/latest/' . $from;
            $response = @file_get_contents($url);
            
            if (!$response) return null; // API Error

            $data = json_decode($response, true);

            // Validate Response
            if (!isset($data['conversion_rates'])) {
                // Invalid Key or Quota exceeded?
                // Fallback to defaults if we have them
                if ($from === 'USD' && $to === 'NGN') return 1500;
                return 1;
            }
            
            // Save Cache
            if (!is_dir(dirname($cacheFile))) mkdir(dirname($cacheFile), 0777, true);
            file_put_contents($cacheFile, json_encode($data));
        }

        return $data['conversion_rates'][$to] ?? 1;
    }

    public function convert($amount, $from, $to)
    {
        $rate = $this->getExchangeRate($from, $to);
        return [
            'amount' => $amount * $rate,
            'rate' => $rate
        ];
    }

    public function getSymbol($currency)
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'NGN' => '₦',
            'GHS' => '₵',
            'KES' => 'KSh',
            'ZAR' => 'R',
            'UGX' => 'USh',
            'TZS' => 'TSh',
            'RWF' => 'FRw',
            'XOF' => 'CFA',
            'XAF' => 'FCFA',
            'JPY' => '¥',
            'CNY' => '¥',
            'INR' => '₹',
            'CAD' => 'C$',
            'AUD' => 'A$',
        ];

        return $symbols[$currency] ?? $currency;
    }
}

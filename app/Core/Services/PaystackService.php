<?php

namespace App\Core\Services;

class PaystackService
{
    private static function getSecretKey()
    {
        $key = defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ($_ENV['PAYSTACK_SECRET_KEY'] ?? getenv('PAYSTACK_SECRET_KEY') ?? '');
        $key = trim($key);
        if (stripos($key, 'Bearer ') === 0) {
            $key = trim(substr($key, 7));
        }
        return $key;
    }

    public static function request($endpoint, $method = 'GET', $data = null)
    {
        $secretKey = self::getSecretKey();
        if (empty($secretKey)) {
            throw new \Exception("Paystack Secret Key is not configured.");
        }

        $url = "https://api.paystack.co" . $endpoint;
        
        $curl = curl_init();
        
        $headers = [
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            throw new \Exception("Paystack Connection Error: " . $err);
        }

        return json_decode($response, true);
    }

    public static function verifyTransaction($reference)
    {
        return self::request("/transaction/verify/" . rawurlencode($reference), 'GET');
    }
}

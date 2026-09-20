<?php

namespace App\Core\Services;

class FlutterwaveService
{
    private static function getSecretKey()
    {
        if (defined('FLUTTERWAVE_SECRET_KEY')) return FLUTTERWAVE_SECRET_KEY;
        return $_ENV['FLUTTERWAVE_SECRET_KEY'] ?? getenv('FLUTTERWAVE_SECRET_KEY') ?? '';
    }

    public static function request($endpoint, $method = 'GET', $data = null)
    {
        $secretKey = self::getSecretKey();
        if (empty($secretKey)) {
            throw new \Exception("Flutterwave Secret Key is not configured.");
        }

        $url = "https://api.flutterwave.com/v3" . $endpoint;
        
        $curl = curl_init();
        
        $headers = [
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/json'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false // Fix for some shared environments
        ];

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);


        if ($err) {
            throw new \Exception("Flutterwave Connection Error: " . $err);
        }

        $res = json_decode($response, true);
        
        // Logging for debug
        // file_put_contents(__DIR__ . '/../../../flutterwave_v3_debug.log', "URL: $url\nResponse: $response\n", FILE_APPEND);

        return $res;
    }

    public static function verifyTransaction($transactionId)
    {
        return self::request("/transactions/{$transactionId}/verify", 'GET');
    }

    /**
     * Charge a saved card token for recurring payments.
     * Uses Flutterwave V3 /tokenized-charges endpoint.
     */
    public static function chargeCardToken($token, $email, $amount, $currency, $txRef)
    {
        $payload = [
            'token' => $token,
            'email' => $email,
            'currency' => $currency,
            'amount' => $amount,
            'tx_ref' => $txRef,
            'narration' => 'Casjoe Subscription Renewal'
        ];

        return self::request('/tokenized-charges', 'POST', $payload);
    }
}

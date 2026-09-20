<?php

namespace App\Core\Services;

class MonnifyService
{
    private static function getCredentials()
    {
        return [
            'api_key' => defined('MONNIFY_API_KEY') ? MONNIFY_API_KEY : '',
            'secret_key' => defined('MONNIFY_SECRET_KEY') ? MONNIFY_SECRET_KEY : '',
            'contract_code' => defined('MONNIFY_CONTRACT_CODE') ? MONNIFY_CONTRACT_CODE : '',
            'mode' => defined('MONNIFY_MODE') ? MONNIFY_MODE : 'test'
        ];
    }

    private static function getBaseUrl()
    {
        $creds = self::getCredentials();
        return ($creds['mode'] === 'live') 
            ? 'https://api.monnify.com' 
            : 'https://sandbox.monnify.com';
    }

    private static function getAccessToken()
    {
        $creds = self::getCredentials();
        $apiKey = $creds['api_key'];
        $secretKey = $creds['secret_key'];

        if (empty($apiKey) || empty($secretKey)) {
            return null;
        }

        $url = self::getBaseUrl() . '/api/v1/auth/login';
        $auth = base64_encode("$apiKey:$secretKey");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic $auth"
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            error_log("Monnify Auth Error: $err");
            return null;
        }

        $result = json_decode($response, true);
        return $result['responseBody']['accessToken'] ?? null;
    }

    public static function request($endpoint, $method = 'GET', $data = [])
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            return ['status' => false, 'message' => 'Failed to authenticate with Monnify'];
        }

        $url = self::getBaseUrl() . $endpoint;
        
        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            return ['status' => false, 'message' => "cURL Error: $err"];
        }

        return json_decode($response, true);
    }

    public static function initializeTransaction($email, $name, $amount, $reference, $callbackUrl)
    {
        $creds = self::getCredentials();
        if (empty($creds['contract_code'])) {
            return ['status' => false, 'message' => 'Monnify Contract Code not configured'];
        }

        $payload = [
            'amount' => $amount,
            'customerName' => $name,
            'customerEmail' => $email,
            'paymentReference' => $reference,
            'paymentDescription' => 'Subscription Payment',
            'currencyCode' => 'NGN',
            'contractCode' => $creds['contract_code'],
            'redirectUrl' => $callbackUrl,
            'paymentMethods' => ['CARD', 'ACCOUNT_TRANSFER']
        ];

        return self::request('/api/v1/merchant/transactions/init-transaction', 'POST', $payload);
    }

    public static function verifyTransaction($reference)
    {
        // Note: Monnify verify endpoint requires urlencoding the reference
        return self::request("/api/v1/merchant/transactions/query?paymentReference=" . rawurlencode($reference), 'GET');
    }

    public static function chargeCardToken($token, $amount, $email, $name, $reference, $description)
    {
        $creds = self::getCredentials();
        if (empty($creds['contract_code'])) {
            return ['status' => false, 'message' => 'Monnify Contract Code not configured'];
        }

        $payload = [
            'amount' => $amount,
            'customerName' => $name,
            'customerEmail' => $email,
            'paymentReference' => $reference,
            'paymentDescription' => $description,
            'currencyCode' => 'NGN',
            'contractCode' => $creds['contract_code'],
            'cardToken' => $token,
            'paymentMethod' => 'CARD'
        ];

        return self::request('/api/v1/merchant/transactions/pay-with-card-token', 'POST', $payload);
    }
}

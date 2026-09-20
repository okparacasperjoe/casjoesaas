<?php

namespace App\Core\Services;

class StroWalletService
{
    private $apiKey;
    private $secretKey;
    private $baseUrl = 'https://api.strowallet.com/v1'; // Example URL

    public function __construct()
    {
        // Load keys from config/env
        $this->apiKey = $_ENV['STROWALLET_PUBLIC_KEY'] ?? (defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : '');
        $this->secretKey = $_ENV['STROWALLET_SECRET_KEY'] ?? (defined('STROWALLET_SECRET_KEY') ? STROWALLET_SECRET_KEY : '');
    }

    private function request($method, $endpoint, $data = [])
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Strowallet API keys are not configured.'
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        // Strowallet often requires public_key in the payload
        $data['public_key'] = $this->apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->secretKey
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            return [
                'success' => false,
                'message' => 'cURL Error: ' . $err
            ];
        }

        $decoded = json_decode($response, true);
        return $decoded ?: [
            'success' => false,
            'message' => 'Invalid response from Strowallet',
            'raw' => $response
        ];
    }

    public function createVirtualBankAccount($email, $accountName, $phone)
    {
        $payload = [
            'customer_email' => $email,
            'customer_name'  => $accountName,
            'customer_phone' => $phone
        ];
        
        // Typical Strowallet endpoint for Virtual Banks
        return $this->request('POST', '/virtual-bank-accounts/create', $payload);
    }

    public function createCard($user, $amount, $currency = 'USD')
    {
        $payload = [
            'name_on_card' => $user['name'] ?? 'User',
            'email' => $user['email'],
            'amount' => $amount,
            'currency' => $currency
        ];
        
        return $this->request('POST', '/cards/create', $payload);
    }

    public function fundCard($cardId, $amount)
    {
        return $this->request('POST', "/cards/$cardId/fund", ['amount' => $amount]);
    }
}

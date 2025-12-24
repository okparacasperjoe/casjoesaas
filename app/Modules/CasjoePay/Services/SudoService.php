<?php

namespace App\Modules\CasjoePay\Services;

class SudoService
{
    private $apiKey;
    private $baseUrl = 'https://api.sudo.africa/v2';

    public function __construct()
    {
        // Keys should be loaded via bootstrap from system_settings
        $this->apiKey = defined('SUDO_API_KEY') ? SUDO_API_KEY : '';
    }

    private function request($endpoint, $method = 'GET', $data = [])
    {
        $curl = curl_init();
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $options = [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === 'POST' || $method === 'PUT') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception("Sudo API Error: " . $err);
        }

        return json_decode($response, true);
    }

    public function createCustomer($email, $name, $phone = null)
    {
        // Check if customer exists or create new
        // For simplicity, we just try to create. Sudo usually returns existing if dup.
        return $this->request('/customers', 'POST', [
            'type' => 'individual',
            'email' => $email,
            'name' => $name,
            'phone' => $phone
            // Add identity/KYC fields if required by Sudo (BVN etc)
        ]);
    }

    public function createCard($customerId, $currency = 'USD', $amount = 0)
    {
        return $this->request('/cards', 'POST', [
            'customer_id' => $customerId,
            'currency' => $currency,
            'type' => 'virtual',
            'amount' => $amount, // Initial funding
            'issuer' => 'MasterCard' // or VISA
        ]);
    }

    public function fundCard($cardId, $amount)
    {
        return $this->request("/cards/{$cardId}/fund", 'PUT', [
            'amount' => $amount
        ]);
    }

    public function getCard($cardId)
    {
        return $this->request("/cards/{$cardId}", 'GET');
    }
}

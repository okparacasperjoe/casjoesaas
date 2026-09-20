<?php

namespace App\Modules\CasjoeERP\Services;

use Exception;

class MoniepointService
{
    private $baseUrl = 'https://channel.moniepoint.com';
    private $clientId;
    private $clientSecret;
    private $terminalSerial;

    public function __construct($clientId, $clientSecret, $terminalSerial)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->terminalSerial = $terminalSerial;
    }

    /**
     * Generate access token for Moniepoint API
     * @return string Access Token
     * @throws Exception
     */
    public function generateToken()
    {
        // Path might need adjusting based on exact Moniepoint routing
        $url = $this->baseUrl . '/api/v1/auth/token';
        
        $payload = [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new Exception("Failed to generate Moniepoint auth token. Response: " . $response);
        }

        $data = json_decode($response, true);
        if (isset($data['accessToken'])) {
            return $data['accessToken'];
        }

        throw new Exception("Access token not found in Moniepoint response.");
    }

    /**
     * Push a payment request to the Moniepoint POS terminal
     * @param float $amount
     * @param string $merchantReference
     * @param string $transactionType
     * @return array
     * @throws Exception
     */
    public function pushPayment($amount, $merchantReference, $transactionType = 'PURCHASE')
    {
        $token = $this->generateToken();
        
        // Path might need adjusting based on exact Moniepoint routing
        $url = $this->baseUrl . '/api/v1/transactions/push';
        
        $payload = [
            'terminalSerial' => $this->terminalSerial,
            'amount' => (int) $amount,
            'merchantReference' => $merchantReference,
            'transactionType' => $transactionType
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || !$response) {
            throw new Exception("Failed to push payment to Moniepoint terminal. Response: " . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Check transaction status
     * @param string $merchantReference
     * @return array
     * @throws Exception
     */
    public function getTransactionStatus($merchantReference)
    {
        $token = $this->generateToken();
        
        $url = $this->baseUrl . '/api/v1/transactions/status/' . urlencode($merchantReference);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || !$response) {
            throw new Exception("Failed to get transaction status. Response: " . $response);
        }

        return json_decode($response, true);
    }
}

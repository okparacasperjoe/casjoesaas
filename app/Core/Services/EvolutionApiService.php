<?php

namespace App\Core\Services;

class EvolutionApiService
{
    private static function getBaseUrl()
    {
        return rtrim($_ENV['EVOLUTION_API_URL'] ?? 'http://localhost:8080', '/');
    }

    private static function getApiKey()
    {
        return $_ENV['EVOLUTION_API_KEY'] ?? 'your_global_api_key_here';
    }

    private static function request($endpoint, $method = 'GET', $data = [])
    {
        $url = self::getBaseUrl() . $endpoint;
        
        $headers = [
            'apikey: ' . self::getApiKey(),
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method !== 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (is_resource($ch)) {

        }

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    public static function createInstance($instanceName)
    {
        // Creates the instance if it doesn't exist
        $payload = [
            'instanceName' => $instanceName,
            'token' => $instanceName,
            'qrcode' => true
        ];
        
        return self::request('/instance/create', 'POST', $payload);
    }

    public static function getConnectionState($instanceName)
    {
        return self::request('/instance/connectionState/' . $instanceName, 'GET');
    }
    
    public static function connect($instanceName)
    {
        // Returns QR code in base64 if not connected
        return self::request('/instance/connect/' . $instanceName, 'GET');
    }

    public static function logout($instanceName)
    {
        return self::request('/instance/logout/' . $instanceName, 'DELETE');
    }

    public static function sendMessage($instanceName, $phone, $text)
    {
        // Format phone: remove + and non-numeric chars
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        $payload = [
            'number' => $phone,
            'textMessage' => [
                'text' => $text
            ],
            'options' => [
                'delay' => 1200,
                'presence' => 'composing'
            ]
        ];

        return self::request('/message/sendText/' . $instanceName, 'POST', $payload);
    }
}

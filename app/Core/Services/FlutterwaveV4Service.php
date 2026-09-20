<?php

namespace App\Core\Services;

class FlutterwaveV4Service
{
    private static $token = null;
    private static $expiry = 0;

    public static function getAccessToken()
    {
        // Use session-based caching for the token to avoid repeated calls within the same session
        if (isset($_SESSION['fw_v4_token']) && isset($_SESSION['fw_v4_expiry']) && $_SESSION['fw_v4_expiry'] > time()) {
            return $_SESSION['fw_v4_token'];
        }

        $clientId = defined('FLUTTERWAVE_PUBLIC_KEY') ? FLUTTERWAVE_PUBLIC_KEY : '';
        $clientSecret = defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : '';

        if (empty($clientId) || empty($clientSecret)) {
            return null;
        }

        $payload = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://idp.flutterwave.com/realms/flutterwave/protocol/openid-connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);


        if ($err) {
            return null;
        }

        file_put_contents(__DIR__ . '/../../../flutterwave_debug.log', "Oauth Response: " . $response . "\n", FILE_APPEND);
        $res = json_decode($response, true);
        if (isset($res['access_token'])) {
            file_put_contents(__DIR__ . '/../../../flutterwave_debug.log', "Token obtained successfully. Expires in: " . ($res['expires_in'] ?? 'N/A') . "\n", FILE_APPEND);
            $_SESSION['fw_v4_token'] = $res['access_token'];
            $_SESSION['fw_v4_expiry'] = time() + ($res['expires_in'] ?? 3600) - 60; // Buffer of 1 minute
            return $res['access_token'];
        }

        file_put_contents(__DIR__ . '/../../../flutterwave_debug.log', "Token retrieval failed: " . $response . "\n", FILE_APPEND);
        return null;
    }

    public static function getBaseUrl()
    {
        $mode = defined('FLUTTERWAVE_MODE_OVERRIDE') ? FLUTTERWAVE_MODE_OVERRIDE : (defined('FLUTTERWAVE_MODE') ? FLUTTERWAVE_MODE : 'live');
        return $mode === 'test' 
            ? 'https://developersandbox-api.flutterwave.com' 
            : 'https://f4bexperience.flutterwave.com';
    }

    public static function request($endpoint, $method = 'GET', $data = null)
    {
        $token = self::getAccessToken();
        if (!$token) {
            throw new \Exception("Could not obtain Flutterwave access token.");
        }

        if (strpos($endpoint, 'http') === 0) {
            $url = $endpoint;
        } else {
            $url = self::getBaseUrl() . $endpoint;
        }
        $curl = curl_init();
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'X-F4B-Account-ID: ' . (defined('FLUTTERWAVE_PARTY_ID') ? FLUTTERWAVE_PARTY_ID : '3307587')
            ],
        ];

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);


        if ($err) {
            throw new \Exception("Flutterwave API Error: " . $err);
        }

        $res = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents(__DIR__ . '/../../../flutterwave_debug.log', "URL: $url\nResponse: $response\nError: Invalid JSON\n", FILE_APPEND);
            return ['status' => 'error', 'message' => 'Invalid JSON response', 'raw' => $response];
        }

        if (!isset($res['status']) || $res['status'] !== 'success') {
            file_put_contents(__DIR__ . '/../../../flutterwave_debug.log', "URL: $url\nPayload: " . json_encode($data) . "\nResponse: " . $response . "\n", FILE_APPEND);
        }

        return $res;
    }
}

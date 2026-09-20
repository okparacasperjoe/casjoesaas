<?php

namespace App\Core\Services;

use App\Core\Database;
use Exception;

class WhatsAppService
{
    private $provider;
    private $metaToken;
    private $metaPhoneId;
    private $unofficialUrl;
    private $unofficialToken;

    public function __construct()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('whatsapp_provider', 'whatsapp_meta_token', 'whatsapp_meta_phone_id', 'whatsapp_unofficial_url', 'whatsapp_unofficial_token')");
        $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->provider = $settings['whatsapp_provider'] ?? 'meta';
        $this->metaToken = $settings['whatsapp_meta_token'] ?? '';
        $this->metaPhoneId = $settings['whatsapp_meta_phone_id'] ?? '';
        $this->unofficialUrl = $settings['whatsapp_unofficial_url'] ?? '';
        $this->unofficialToken = $settings['whatsapp_unofficial_token'] ?? '';
    }

    public function sendMessage(string $phone, string $message): bool
    {
        // Sanitize phone number (remove +, spaces, hyphens)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        switch ($this->provider) {
            case 'unofficial':
                return $this->sendUnofficial($phone, $message);
            case 'meta':
            default:
                return $this->sendMeta($phone, $message);
        }
    }

    private function sendMeta(string $phone, string $message): bool
    {
        if (empty($this->metaToken) || empty($this->metaPhoneId)) {
            error_log("WhatsApp Meta API not fully configured.");
            return false;
        }

        $url = "https://graph.facebook.com/v17.0/{$this->metaPhoneId}/messages";
        
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->metaToken
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log("WhatsApp Meta API Error: " . $response);
        return false;
    }

    private function sendUnofficial(string $phone, string $message): bool
    {
        if (empty($this->unofficialUrl) || empty($this->unofficialToken)) {
            error_log("WhatsApp Unofficial API not fully configured.");
            return false;
        }

        $url = rtrim($this->unofficialUrl, '/');
        
        // This assumes a common Evolution-API or WAPP shape.
        // May need adjustment based on the exact unofficial provider chosen.
        $data = [
            'number' => $phone,
            'text' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $this->unofficialToken,
            'Authorization: Bearer ' . $this->unofficialToken
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log("WhatsApp Unofficial API Error: " . $response);
        return false;
    }
}

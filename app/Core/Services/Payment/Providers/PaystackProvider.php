<?php

namespace App\Core\Services\Payment\Providers;

use App\Core\Services\Payment\GatewayInterface;

class PaystackProvider implements GatewayInterface
{
    private $secretKey;

    public function __construct($settings)
    {
        $key = trim($settings['paystack_secret_key'] ?? (defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ''));
        if (stripos($key, 'Bearer ') === 0) {
            $key = trim(substr($key, 7));
        }
        $this->secretKey = $key;
    }

    public function initiate($amount, $currency, $reference, $email, $metadata = [])
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // Paystack uses kobo/cents
            'currency' => $currency,
            'reference' => $reference,
            'metadata' => $metadata
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->secretKey,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        $response = json_decode($result, true);

        if ($response && $response['status']) {
            return [
                'status' => 'success',
                'checkout_url' => $response['data']['authorization_url'],
                'gateway' => 'paystack'
            ];
        }

        return ['status' => 'error', 'message' => $response['message'] ?? 'Initialization failed'];
    }

    public function verify($data)
    {
        $reference = $data['reference'] ?? '';
        $url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->secretKey,
            "Cache-Control: no-cache"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        $response = json_decode($result, true);
        return ($response && $response['status'] && $response['data']['status'] === 'success');
    }

    public function getSlug() { return 'paystack'; }
    public function getName() { return 'Paystack'; }
}

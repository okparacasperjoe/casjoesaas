<?php

namespace App\Core\Services\Payment\Providers;

use App\Core\Services\Payment\GatewayInterface;

class FlutterwaveProvider implements GatewayInterface
{
    private $publicKey;
    private $secretKey;

    public function __construct($config)
    {
        $this->publicKey = $config['flutterwave_public_key'] ?? '';
        $this->secretKey = $config['flutterwave_secret_key'] ?? '';
    }

    public function initiate($amount, $currency, $metadata = [])
    {
        // Placeholder for Flutterwave Standard Payment API
        return [
            'status' => 'success',
            'checkout_url' => 'https://checkout.flutterwave.com/v3/hosted/pay/' . uniqid(),
            'reference' => 'FLW-' . strtoupper(uniqid())
        ];
    }

    public function verify($reference)
    {
        // Placeholder for Flutterwave Transaction Verification API
        return true;
    }

    public function getSlug() { return 'flutterwave'; }
    public function getName() { return 'Flutterwave'; }
}

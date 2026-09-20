<?php

namespace App\Core\Services\Payment;

interface GatewayInterface
{
    /**
     * Initialize payment and get redirect URL or payload
     */
    public function initiate($amount, $currency, $reference, $email, $metadata = []);

    /**
     * Handle webhook or callback to verify payment
     */
    public function verify($data);

    /**
     * Get unique identifier for the gateway (e.g. 'stripe')
     */
    public function getSlug();

    /**
     * Get human readable name
     */
    public function getName();
}

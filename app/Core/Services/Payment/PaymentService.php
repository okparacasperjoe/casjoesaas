<?php

namespace App\Core\Services\Payment;

class PaymentService
{
    private static $providers = [];

    public static function register(GatewayInterface $provider)
    {
        self::$providers[$provider->getSlug()] = $provider;
    }

    public static function get($slug)
    {
        return self::$providers[$slug] ?? null;
    }

    public static function getAll()
    {
        return self::$providers;
    }

    /**
     * Factory method to load all enabled gateways from DB
     */
    public static function init()
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE '%_enabled' OR setting_key LIKE '%_secret_key' OR setting_key LIKE '%_public_key'");
        $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        // Stripe
        if (($settings['stripe_enabled'] ?? '0') === '1') {
            self::register(new \App\Core\Services\Payment\Providers\StripeProvider($settings));
        }

        // Paystack
        if (($settings['paystack_enabled'] ?? '0') === '1') {
            self::register(new \App\Core\Services\Payment\Providers\PaystackProvider($settings));
        }

        // PayPal
        if (($settings['paypal_enabled'] ?? '0') === '1') {
            self::register(new \App\Core\Services\Payment\Providers\PayPalProvider($settings));
        }

        // Flutterwave
        if (($settings['flutterwave_enabled'] ?? '0') === '1') {
            self::register(new \App\Core\Services\Payment\Providers\FlutterwaveProvider($settings));
        }

        // Razorpay
        if (($settings['razorpay_enabled'] ?? '0') === '1') {
            self::register(new \App\Core\Services\Payment\Providers\RazorpayProvider($settings));
        }
    }
}

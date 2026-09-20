<?php

namespace App\Core\Services;

use App\Core\Database;

class PaymentGatewayService
{
    private $db;
    private $settings;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM system_settings");
        $this->settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function initializePayment($email, $amount, $currency, $reference, $callbackUrl, $meta = [])
    {
        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $email,
                'name' => $meta['name'] ?? 'Customer'
            ],
            'customizations' => [
                'title' => 'Casjoe Pay',
                'description' => 'Payment for ' . ($meta['description'] ?? 'Product')
            ],
            'meta' => $meta
        ];

        try {
            // Updated to use V3 Service
            $res = \App\Core\Services\FlutterwaveService::request('/payments', 'POST', $payload);
            
            if (($res['status'] ?? 'error') === 'success') {
                return $res['data']['link'];
            }
            throw new \Exception($res['message'] ?? 'Unknown Gateway Error');
        } catch (\Exception $e) {
            throw new \Exception("Gateway Init Failed: " . $e->getMessage());
        }
    }

    public function verifyTransaction($idOrRef, $isId = true)
    {
        try {
            // V3 Verification: Verify by Transaction ID
            // If ref provided, we might need to find ID first, but V3 verify endpoint typically wants transaction_id.
            // However, a common pattern is to just call verify with ID.
            // If we only have reference, we might need a different endpoint, but V3 "Verify Transaction" usually takes ID.
            
            // Re-using the static helper if available, or direct request
            // FlutterwaveService::verifyTransaction takes transactionId
            
            return \App\Core\Services\FlutterwaveService::verifyTransaction($idOrRef);
            
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

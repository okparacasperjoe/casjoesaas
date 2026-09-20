<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;

class PaymentProcessor
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create payment link for funnel payment step
     * 
     * @param int $funnelId
     * @param int $sessionId
     * @param array $paymentConfig Step configuration
     * @param int $contactId
     * @return array Payment details
     */
    public function createPayment($funnelId, $sessionId, $paymentConfig, $contactId = null)
    {
        $amount = $paymentConfig['amount'] ?? 0;
        $currency = $paymentConfig['currency'] ?? 'NGN';
        $productId = $paymentConfig['product_id'] ?? null;
        $description = $paymentConfig['description'] ?? 'Funnel Payment';
        
        // Get product details if product_id is set
        if ($productId) {
            $stmt = $this->db->query(
                "SELECT name, price FROM shop_products WHERE id = ?",
                [$productId]
            );
            $product = $stmt->fetch();
            
            if ($product) {
                $amount = $product['price'];
                $description = $product['name'];
            }
        }
        
        // Create payment record
        $stmt = $this->db->prepare(
            "INSERT INTO funnel_payments 
            (funnel_id, session_id, contact_id, product_id, amount, currency, description, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
        );
        $stmt->execute([
            $funnelId,
            $sessionId,
            $contactId,
            $productId,
            $amount,
            $currency,
            $description
        ]);
        
        $paymentId = $this->db->getConnection()->lastInsertId();
        
        return [
            'payment_id' => $paymentId,
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'product_id' => $productId
        ];
    }

    /**
     * Process successful payment
     */
    public function handlePaymentSuccess($paymentId, $transactionRef, $dealId = null)
    {
        // Update payment status
        $stmt = $this->db->prepare(
            "UPDATE funnel_payments 
            SET status = 'completed', transaction_ref = ?, completed_at = NOW() 
            WHERE id = ?"
        );
        $stmt->execute([$transactionRef, $paymentId]);
        
        // Get payment details
        $stmt = $this->db->query(
            "SELECT * FROM funnel_payments WHERE id = ?",
            [$paymentId]
        );
        $payment = $stmt->fetch();
        
        if (!$payment) return false;
        
        // Mark deal as won if dealId is provided
        if ($dealId) {
            require_once __DIR__ . '/DealAutomation.php';
            $dealAutomation = new DealAutomation();
            $dealAutomation->markAsWon($dealId, $payment['amount']);
            
            // Attach product to deal if product_id exists
            if ($payment['product_id']) {
                $dealAutomation->attachProduct($dealId, $payment['product_id'], 1);
            }
        }
        
        // Update funnel session
        if ($payment['session_id']) {
            $this->db->query(
                "UPDATE funnel_sessions SET payment_completed = 1 WHERE id = ?",
                [$payment['session_id']]
            );
        }
        
        return true;
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailed($paymentId, $reason = '')
    {
        $stmt = $this->db->prepare(
            "UPDATE funnel_payments 
            SET status = 'failed', failure_reason = ? 
            WHERE id = ?"
        );
        $stmt->execute([$reason, $paymentId]);
        
        return true;
    }

    /**
     * Get payment by ID
     */
    public function getPayment($paymentId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM funnel_payments WHERE id = ?",
            [$paymentId]
        );
        return $stmt->fetch();
    }

    /**
     * Get products for funnel payment step
     */
    public function getProducts($tenantId, $category = null)
    {
        if ($category) {
            $stmt = $this->db->query(
                "SELECT id, name, description, price, image 
                FROM shop_products 
                WHERE tenant_id = ? AND status = 'active' AND category = ? 
                ORDER BY name ASC",
                [$tenantId, $category]
            );
        } else {
            $stmt = $this->db->query(
                "SELECT id, name, description, price, image 
                FROM shop_products 
                WHERE tenant_id = ? AND status = 'active' 
                ORDER BY name ASC",
                [$tenantId]
            );
        }
        
        return $stmt->fetchAll();
    }
}

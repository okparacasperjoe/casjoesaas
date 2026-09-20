<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;

class ActivityLogger
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Log funnel activity in ERP activity feed
     * 
     * @param int $tenantId
     * @param string $activityType Type of activity
     * @param string $description Activity description
     * @param array $metadata Additional metadata
     */
    public function logActivity($tenantId, $activityType, $description, $metadata = [])
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO erp_activity_log 
                (tenant_id, activity_type, description, metadata, created_at) 
                VALUES (?, ?, ?, ?, NOW())"
            );
            
            $stmt->execute([
                $tenantId,
                $activityType,
                $description,
                json_encode($metadata)
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log funnel view
     */
    public function logFunnelView($tenantId, $funnelId, $funnelName, $sessionId = null)
    {
        return $this->logActivity(
            $tenantId,
            'funnel_view',
            "Funnel viewed: {$funnelName}",
            [
                'funnel_id' => $funnelId,
                'funnel_name' => $funnelName,
                'session_id' => $sessionId,
                'source' => 'sales_funnel'
            ]
        );
    }

    /**
     * Log form submission with lead info
     */
    public function logFormSubmission($tenantId, $funnelId, $funnelName, $leadId, $leadName, $dealId = null)
    {
        return $this->logActivity(
            $tenantId,
            'funnel_form_submit',
            "Lead captured via {$funnelName}: {$leadName}",
            [
                'funnel_id' => $funnelId,
                'funnel_name' => $funnelName,
                'lead_id' => $leadId,
                'lead_name' => $leadName,
                'deal_id' => $dealId,
                'source' => 'sales_funnel'
            ]
        );
    }

    /**
     * Log payment initiation
     */
    public function logPaymentStarted($tenantId, $funnelId, $funnelName, $amount, $dealId = null)
    {
        return $this->logActivity(
            $tenantId,
            'funnel_payment_started',
            "Payment initiated for {$funnelName}: ₦" . number_format($amount, 2),
            [
                'funnel_id' => $funnelId,
                'funnel_name' => $funnelName,
                'amount' => $amount,
                'deal_id' => $dealId,
                'source' => 'sales_funnel'
            ]
        );
    }

    /**
     * Log successful payment
     */
    public function logPaymentSuccess($tenantId, $funnelId, $funnelName, $amount, $transactionRef, $dealId = null)
    {
        return $this->logActivity(
            $tenantId,
            'funnel_payment_success',
            "Payment completed for {$funnelName}: ₦" . number_format($amount, 2) . " (Ref: {$transactionRef})",
            [
                'funnel_id' => $funnelId,
                'funnel_name' => $funnelName,
                'amount' => $amount,
                'transaction_ref' => $transactionRef,
                'deal_id' => $dealId,
                'source' => 'sales_funnel'
            ]
        );
    }

    /**
     * Log funnel completion
     */
    public function logFunnelComplete($tenantId, $funnelId, $funnelName, $leadId = null, $dealId = null)
    {
        return $this->logActivity(
            $tenantId,
            'funnel_complete',
            "Funnel completed: {$funnelName}",
            [
                'funnel_id' => $funnelId,
                'funnel_name' => $funnelName,
                'lead_id' => $leadId,
                'deal_id' => $dealId,
                'source' => 'sales_funnel'
            ]
        );
    }

    /**
     * Get activity log for a funnel
     */
    public function getFunnelActivities($funnelId, $limit = 100)
    {
        $stmt = $this->db->query(
            "SELECT * FROM erp_activity_log 
             WHERE JSON_EXTRACT(metadata, '$.funnel_id') = ? 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$funnelId, $limit]
        );
        
        return $stmt->fetchAll();
    }

    /**
     * Get activity log for a lead
     */
    public function getLeadActivities($leadId, $limit = 50)
    {
        $stmt = $this->db->query(
            "SELECT * FROM erp_activity_log 
             WHERE JSON_EXTRACT(metadata, '$.lead_id') = ? 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$leadId, $limit]
        );
        
        return $stmt->fetchAll();
    }
}

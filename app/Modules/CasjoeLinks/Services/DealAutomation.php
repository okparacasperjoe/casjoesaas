<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;

class DealAutomation
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Auto-create deal from funnel submission
     * 
     * @param array $funnel Funnel details
     * @param int $contactId Contact ID
     * @param array $options Additional options (product_id, amount, etc.)
     * @return int Deal ID
     */
    public function createDeal($funnel, $contactId, $options = [])
    {
        $tenantId = $funnel['tenant_id'];
        $pipelineId = $funnel['pipeline_id'];
        $stageId = $funnel['default_stage_id'];
        $ownerId = $funnel['owner_id'];
        
        // Generate deal title based on funnel type
        $dealTitle = $this->generateDealTitle($funnel, $contactId);
        
        // Get contact details for deal
        $stmt = $this->db->query(
            "SELECT name, email, company FROM erp_crm_leads WHERE id = ?",
            [$contactId]
        );
        $contact = $stmt->fetch();
        
        // Prepare deal value
        $dealValue = $options['amount'] ?? 0;
        $currency = $options['currency'] ?? 'NGN';
        
        // Create deal (opportunity)
        $stmt = $this->db->prepare(
            "INSERT INTO erp_crm_opportunities 
            (tenant_id, lead_id, title, value, stage, source, metadata, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        
        $metadata = json_encode([
            'funnel_id' => $funnel['id'],
            'funnel_name' => $funnel['name'],
            'funnel_type' => $funnel['type'],
            'product_id' => $options['product_id'] ?? null,
            'created_via' => 'funnel_automation',
            'form_data' => $options['form_data'] ?? []
        ]);
        
        $stmt->execute([
            $tenantId,
            $contactId,
            $dealTitle,
            $dealValue,
            'prospecting',
            $funnel['lead_source'],
            $metadata
        ]);
        
        $dealId = $this->db->getConnection()->lastInsertId();
        
        // Log deal creation activity
        $this->logDealActivity($dealId, 'deal_created', "Deal automatically created from funnel: {$funnel['name']}");
        
        return $dealId;
    }

    /**
     * Progress deal to next stage
     */
    public function progressDeal($dealId, $event)
    {
        // Get current deal
        $stmt = $this->db->query(
            "SELECT o.*, o.stage as current_stage_name 
             FROM erp_crm_opportunities o 
             WHERE o.id = ?",
            [$dealId]
        );
        $deal = $stmt->fetch();
        
        if (!$deal) return false;
        
        // Determine next stage based on event
        $nextStage = $this->getNextStage($deal, $event);
        
        if ($nextStage) {
            $this->db->query(
                "UPDATE erp_crm_opportunities SET stage = ?, updated_at = NOW() WHERE id = ?",
                [$nextStage, $dealId]
            );
            
            $this->logDealActivity(
                $dealId, 
                'stage_changed', 
                "Deal moved from '{$deal['current_stage_name']}' to '{$nextStage['name']}' (Event: $event)"
            );
            
            return true;
        }
        
        return false;
    }

    /**
     * Mark deal as won
     */
    public function markAsWon($dealId, $amount = null)
    {
        $updates = ['status' => 'won', 'closed_at' => date('Y-m-d H:i:s')];
        
        if ($amount !== null) {
            $this->db->query(
                "UPDATE erp_crm_opportunities SET stage = 'won', value = ?, closed_at = ?, updated_at = NOW() WHERE id = ?",
                [$amount, $updates['closed_at'], $dealId]
            );
        } else {
            $this->db->query(
                "UPDATE erp_crm_opportunities SET stage = 'won', closed_at = ?, updated_at = NOW() WHERE id = ?",
                [$updates['closed_at'], $dealId]
            );
        }
        
        $this->logDealActivity($dealId, 'deal_won', "Deal marked as won" . ($amount ? " with value: ₦" . number_format($amount, 2) : ""));
        
        return true;
    }

    /**
     * Mark deal as lost
     */
    public function markAsLost($dealId, $reason = '')
    {
        $this->db->query(
            "UPDATE erp_crm_opportunities SET stage = 'lost', closed_at = NOW(), updated_at = NOW() WHERE id = ?",
            [$dealId]
        );
        
        $this->logDealActivity($dealId, 'deal_lost', "Deal marked as lost" . ($reason ? ": $reason" : ""));
        
        return true;
    }

    /**
     * Attach product to deal
     */
    public function attachProduct($dealId, $productId, $quantity = 1)
    {
        // Get deal metadata
        $stmt = $this->db->query("SELECT metadata FROM erp_crm_opportunities WHERE id = ?", [$dealId]);
        $deal = $stmt->fetch();
        
        $metadata = json_decode($deal['metadata'] ?? '{}', true);
        $metadata['product_id'] = $productId;
        $metadata['quantity'] = $quantity;
        
        $this->db->query(
            "UPDATE erp_crm_opportunities SET metadata = ? WHERE id = ?",
            [json_encode($metadata), $dealId]
        );
        
        return true;
    }

    /**
     * Get deal by ID
     */
    public function getDeal($dealId)
    {
        $stmt = $this->db->query("SELECT * FROM erp_crm_opportunities WHERE id = ?", [$dealId]);
        return $stmt->fetch();
    }

    /**
     * Generate deal title based on funnel type
     */
    private function generateDealTitle($funnel, $contactId)
    {
        $stmt = $this->db->query("SELECT name FROM erp_crm_leads WHERE id = ?", [$contactId]);
        $contact = $stmt->fetch();
        $contactName = $contact['name'] ?? 'Unknown Contact';
        
        $titles = [
            'lead' => "New Lead: $contactName",
            'service' => "Service Request: $contactName",
            'product' => "Product Order: $contactName",
            'event' => "Event Registration: $contactName"
        ];
        
        return $titles[$funnel['type']] ?? "New Deal: $contactName";
    }

    /**
     * Get next stage based on event
     */
    private function getNextStage($deal, $event)
    {
        // Event-based stage progression logic
        $stageMap = [
            'checkout_started' => 'negotiation',
            'payment_success' => 'won',
            'payment_failed' => 'contacted'
        ];
        
        $targetStage = $stageMap[$event] ?? null;
        
        if ($targetStage) {
            return $targetStage;
        }
        
        return null;
    }

    /**
     * Log deal activity
     */
    private function logDealActivity($dealId, $type, $description)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO erp_crm_activities (opportunity_id, type, description, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$dealId, $type, $description]);
        } catch (\Exception $e) {
            // Silently fail if activities table doesn't exist
            error_log("Failed to log deal activity: " . $e->getMessage());
        }
    }
}

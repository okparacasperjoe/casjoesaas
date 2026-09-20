<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;

class ContactMatcher
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find or create contact from funnel submission
     * 
     * @param int $tenantId
     * @param array $data Form data (email, phone, name, etc.)
     * @param array $funnelMetadata Funnel info to attach
     * @return int Contact ID
     */
    public function findOrCreateContact($tenantId, $data, $funnelMetadata = [])
    {
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        
        // Try to find existing contact
        $contactId = $this->findExistingContact($tenantId, $email, $phone);
        
        if ($contactId) {
            // Update existing contact
            $this->updateContact($contactId, $data, $funnelMetadata);
            return $contactId;
        }
        
        // Create new contact
        return $this->createContact($tenantId, $data, $funnelMetadata);
    }

    /**
     * Find existing contact by email or phone
     */
    private function findExistingContact($tenantId, $email, $phone)
    {
        if ($email) {
            $stmt = $this->db->query(
                "SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND email = ? LIMIT 1",
                [$tenantId, $email]
            );
            $result = $stmt->fetch();
            if ($result) return $result['id'];
        }

        if ($phone) {
            // Clean phone for comparison
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            
            $stmt = $this->db->query(
                "SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+', '') = ? LIMIT 1",
                [$tenantId, $cleanPhone]
            );
            $result = $stmt->fetch();
            if ($result) return $result['id'];
        }

        return null;
    }

    /**
     * Create new contact
     */
    private function createContact($tenantId, $data, $funnelMetadata)
    {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $company = $data['company'] ?? '';
        
        // Prepare funnel metadata
        $metadata = json_encode([
            'source' => 'funnel',
            'funnel_id' => $funnelMetadata['funnel_id'] ?? null,
            'funnel_name' => $funnelMetadata['funnel_name'] ?? null,
            'funnel_type' => $funnelMetadata['funnel_type'] ?? null,
            'entry_url' => $funnelMetadata['entry_url'] ?? null,
            'captured_at' => date('Y-m-d H:i:s')
        ]);

        $stmt = $this->db->prepare(
            "INSERT INTO erp_crm_leads 
            (tenant_id, name, email, phone, company, source, status, metadata, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'new', ?, NOW())"
        );
        
        $stmt->execute([
            $tenantId,
            $name,
            $email,
            $phone,
            $company,
            $funnelMetadata['lead_source'] ?? 'Funnel',
            $metadata
        ]);

        $contactId = $this->db->getConnection()->lastInsertId();
        
        // Log activity
        $this->logActivity($contactId, 'contact_created', "Contact created from funnel: " . ($funnelMetadata['funnel_name'] ?? 'Unknown'));
        
        return $contactId;
    }

    /**
     * Update existing contact
     */
    private function updateContact($contactId, $data, $funnelMetadata)
    {
        // Get existing contact
        $stmt = $this->db->query(
            "SELECT metadata FROM erp_crm_leads WHERE id = ?",
            [$contactId]
        );
        $contact = $stmt->fetch();
        
        // Merge metadata
        $existingMetadata = json_decode($contact['metadata'] ?? '{}', true);
        $existingMetadata['funnels'] = $existingMetadata['funnels'] ?? [];
        $existingMetadata['funnels'][] = [
            'funnel_id' => $funnelMetadata['funnel_id'] ?? null,
            'funnel_name' => $funnelMetadata['funnel_name'] ?? null,
            'visited_at' => date('Y-m-d H:i:s')
        ];
        
        // Update contact
        $updates = [];
        $params = [];
        
        // Update name if provided and current is empty
        if (!empty($data['name'])) {
            $updates[] = "name = ?";
            $params[] = $data['name'];
        }
        
        // Update company if provided
        if (!empty($data['company'])) {
            $updates[] = "company = ?";
            $params[] = $data['company'];
        }
        
        // Always update metadata
        $updates[] = "metadata = ?";
        $params[] = json_encode($existingMetadata);
        
        // $updates[] = "updated_at = NOW()"; // Column does not exist
        
        $params[] = $contactId;
        
        if (!empty($updates)) {
            $sql = "UPDATE erp_crm_leads SET " . implode(', ', $updates) . " WHERE id = ?";
            $this->db->query($sql, $params);
        }
        
        // Log activity
        $this->logActivity($contactId, 'funnel_revisit', "Revisited funnel: " . ($funnelMetadata['funnel_name'] ?? 'Unknown'));
    }

    /**
     * Log contact activity
     */
    private function logActivity($contactId, $type, $description)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO erp_crm_activities (lead_id, type, description, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$contactId, $type, $description]);
        } catch (\Exception $e) {
            // Silently fail if activities table doesn't exist yet
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }

    /**
     * Get contact by ID
     */
    public function getContact($contactId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM erp_crm_leads WHERE id = ?",
            [$contactId]
        );
        return $stmt->fetch();
    }

    /**
     * Attach additional data to contact
     */
    public function attachData($contactId, $key, $value)
    {
        $stmt = $this->db->query(
            "SELECT metadata FROM erp_crm_leads WHERE id = ?",
            [$contactId]
        );
        $contact = $stmt->fetch();
        
        $metadata = json_decode($contact['metadata'] ?? '{}', true);
        $metadata[$key] = $value;
        
        $this->db->query(
            "UPDATE erp_crm_leads SET metadata = ? WHERE id = ?",
            [json_encode($metadata), $contactId]
        );
    }
}

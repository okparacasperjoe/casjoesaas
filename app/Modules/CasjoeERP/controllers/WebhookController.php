<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use PDO;

class WebhookController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function handle($params)
    {
        // Add CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $secret = $params['secret'] ?? null;
        if (!$secret) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing secret']);
            return;
        }

        // 1. Validate Integration
        $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_integrations WHERE webhook_secret = ? AND is_active = 1");
        $stmt->execute([$secret]);
        $integration = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$integration) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Integration not found or inactive']);
            return;
        }

        // 2. Parse Payload
        $rawPayload = file_get_contents('php://input');
        $payload = json_decode($rawPayload, true);

        if (!$payload) {
            $this->logWebhook($integration['id'], $rawPayload, 'failed', 'Invalid JSON payload');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            return;
        }

        // 3. Process Lead (Universal mapping)
        $leadData = $this->mapPayload($payload, $integration);
        
        if (empty($leadData['name'])) {
             $this->logWebhook($integration['id'], $rawPayload, 'failed', 'Missing required field: name');
             http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Name is required']);
             return;
        }

        $leadId = $this->processLead($leadData, $integration);

        if ($leadId) {
            $this->logWebhook($integration['id'], $rawPayload, 'processed');

            // --- Lead Capture Email Notification ---
            try {
                $stmt = $this->pdo->prepare("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = 'company_email'");
                $stmt->execute([$integration['tenant_id']]);
                $adminEmail = $stmt->fetchColumn();

                if (!$adminEmail) {
                    $stmt = $this->pdo->prepare("SELECT email FROM users WHERE tenant_id = ? ORDER BY id ASC LIMIT 1");
                    $stmt->execute([$integration['tenant_id']]);
                    $adminEmail = $stmt->fetchColumn();
                }

                if ($adminEmail) {
                    $emailBody = "<h2>New Lead Captured!</h2>";
                    $emailBody .= "<p>A new lead has been captured from your " . htmlspecialchars($leadData['source']) . " integration.</p>";
                    $emailBody .= "<ul>";
                    $emailBody .= "<li><strong>Name:</strong> " . htmlspecialchars($leadData['name'] ?? 'N/A') . "</li>";
                    $emailBody .= "<li><strong>Email:</strong> " . htmlspecialchars($leadData['email'] ?? 'N/A') . "</li>";
                    $emailBody .= "<li><strong>Phone:</strong> " . htmlspecialchars($leadData['phone'] ?? 'N/A') . "</li>";
                    $emailBody .= "<li><strong>Company:</strong> " . htmlspecialchars($leadData['company'] ?? 'N/A') . "</li>";
                    $emailBody .= "<li><strong>Notes:</strong> " . htmlspecialchars($leadData['notes'] ?? 'N/A') . "</li>";
                    $emailBody .= "</ul>";
                    $emailBody .= "<p>Log in to your CRM to view and manage this lead.</p>";

                    \App\Core\Mailer::send($adminEmail, "New Lead Captured: " . ($leadData['name'] ?? 'Unknown'), $emailBody);
                }
            } catch (\Exception $e) {
                error_log("Failed to send lead capture email: " . $e->getMessage());
            }
            // ----------------------------------------

            echo json_encode(['success' => true, 'lead_id' => $leadId]);
        } else {
            $this->logWebhook($integration['id'], $rawPayload, 'failed', 'Failed to create lead');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Internal error']);
        }
    }

    private function mapPayload($payload, $integration)
    {
        // Simple mapping for now. Can be expanded via $integration['config']
        return [
            'name' => $payload['name'] ?? $payload['full_name'] ?? $payload['contact_name'] ?? null,
            'email' => $payload['email'] ?? $payload['work_email'] ?? $payload['email_address'] ?? null,
            'phone' => $payload['phone'] ?? $payload['phone_number'] ?? $payload['tel'] ?? null,
            'company' => $payload['company'] ?? $payload['business_name'] ?? $payload['business_type'] ?? null,
            'notes' => $payload['notes'] ?? $payload['message'] ?? $payload['how_can_we_help'] ?? $payload['how_can_we_help?'] ?? null,
            'source' => $payload['source'] ?? ucfirst($integration['provider']) . ' Integration'
        ];
    }

    private function processLead($data, $integration)
    {
        $tenantId = $integration['tenant_id'];
        $config = json_decode($integration['config'] ?? '{}', true);
        
        // 1. Check for duplicates (Optional based on config)
        $existing = null;
        if (!empty($data['email'])) {
              $stmt = $this->pdo->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND email = ?");
              $stmt->execute([$tenantId, $data['email']]);
              $existing = $stmt->fetch();
        }
        if (!$existing && !empty($data['phone'])) {
              $stmt = $this->pdo->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND phone = ?");
              $stmt->execute([$tenantId, $data['phone']]);
              $existing = $stmt->fetch();
        }

        if ($existing) {
            return $existing['id'];
        }

        // 2. Create Lead
        $status = 'new';
        $assignedTo = $config['assigned_to'] ?? null;

        try {
            if ($assignedTo) {
                $stmt = $this->pdo->prepare("INSERT INTO erp_crm_leads (tenant_id, name, email, phone, company, source, status, notes, assigned_to, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $tenantId, 
                    $data['name'], 
                    $data['email'] ?? '', 
                    $data['phone'] ?? '', 
                    $data['company'] ?? '',
                    $data['source'] ?? 'Webhook', 
                    $status,
                    $data['notes'] ?? '',
                    $assignedTo
                ]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO erp_crm_leads (tenant_id, name, email, phone, company, source, status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $tenantId, 
                    $data['name'], 
                    $data['email'] ?? '', 
                    $data['phone'] ?? '', 
                    $data['company'] ?? '',
                    $data['source'] ?? 'Webhook', 
                    $status,
                    $data['notes'] ?? ''
                ]);
            }
            return $this->pdo->lastInsertId();
        } catch (\Exception $e) {
            error_log("Webhook lead creation error: " . $e->getMessage());
            try {
                $stmt = $this->pdo->prepare("INSERT INTO erp_crm_leads (tenant_id, name, email, phone, company, source, status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $tenantId, 
                    $data['name'], 
                    $data['email'] ?? '', 
                    $data['phone'] ?? '', 
                    $data['company'] ?? '',
                    $data['source'] ?? 'Webhook', 
                    $status,
                    $data['notes'] ?? ''
                ]);
                return $this->pdo->lastInsertId();
            } catch (\Exception $e2) {
                error_log("Webhook lead creation fallback error: " . $e2->getMessage());
                return null;
            }
        }
    }

    private function logWebhook($integrationId, $payload, $status, $error = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO erp_crm_webhook_logs (integration_id, payload, status, error_message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$integrationId, $payload, $status, $error]);
    }
}

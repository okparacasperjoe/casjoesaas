<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\Services\AIService;
use Exception;

class CrmWebhookController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function captureLead($tenantId)
    {
        // 1. Authenticate Request (Basic security, could be improved with API tokens)
        if (empty($tenantId) || !is_numeric($tenantId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid tenant ID']);
            return;
        }

        // 2. Read Payload
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON payload']);
            return;
        }

        try {
            $name = $payload['name'] ?? 'Unknown Lead';
            $email = $payload['email'] ?? null;
            $phone = $payload['phone'] ?? null;
            $company = $payload['company'] ?? null;
            $source = $payload['source'] ?? 'api';
            $rawNotes = $payload['message'] ?? $payload['notes'] ?? '';

            // 3. AI Extraction & Scoring
            $aiService = new AIService($tenantId);
            $prompt = "Analyze this lead capture data. Extract the following if present: industry, budget, timeline, interest_level (Low/Medium/High). Also assign an ai_score (1-100) based on how qualified this lead seems (budget, urgency, and details provided). Return ONLY a JSON object with keys: industry, budget (number), timeline, interest_level, ai_score.\n\nLead Data:\nName: $name\nCompany: $company\nNotes: $rawNotes";
            
            $aiResponse = $aiService->generateText($prompt);
            $aiData = json_decode(trim(str_replace(['```json', '```'], '', $aiResponse)), true) ?: [];

            $industry = $aiData['industry'] ?? null;
            $budget = is_numeric($aiData['budget'] ?? null) ? $aiData['budget'] : null;
            $timeline = $aiData['timeline'] ?? null;
            $interestLevel = $aiData['interest_level'] ?? 'Medium';
            $aiScore = (int)($aiData['ai_score'] ?? 50);

            // 4. Insert Lead
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_crm_leads 
                (tenant_id, name, email, phone, company, source, industry, budget, timeline, interest_level, ai_score, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')
            ");
            
            $stmt->execute([
                $tenantId, $name, $email, $phone, $company, $source,
                $industry, $budget, $timeline, $interestLevel, $aiScore
            ]);

            $leadId = $this->pdo->lastInsertId();

            // --- Unified Inbox Integration ---
            try {
                $convId = \App\Core\Services\InboxService::createConversation([
                    'tenant_id' => $tenantId,
                    'contact_name' => $name,
                    'contact_email' => $email ?: null,
                    'contact_phone' => $phone ?: null,
                    'contact_id' => $leadId,
                    'channel' => 'webhook',
                    'subject' => 'New lead from webhook',
                    'source_type' => 'erp_crm_leads',
                    'source_id' => $leadId,
                ]);
                \App\Core\Services\InboxService::addMessage($convId, [
                    'tenant_id' => $tenantId,
                    'direction' => 'inbound',
                    'sender_type' => 'contact',
                    'sender_name' => $name,
                    'content' => $rawNotes ?: 'New lead captured via webhook',
                    'channel' => 'webhook',
                ]);
            } catch (\Exception $e) {
                error_log('Inbox integration error (webhook): ' . $e->getMessage());
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'lead_id' => $leadId,
                'ai_analysis' => $aiData
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

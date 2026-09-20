<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\AIService;
use Exception;
use PDO;

class ProposalController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        
        $role = \App\Core\Auth::user()['role'] ?? '';
        if (!in_array($role, ['admin', 'sales', 'manager'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function generateAiProposal($leadId)
    {
        try {
            // 1. Fetch Lead
            $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$leadId, $this->tenantId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lead) {
                throw new Exception("Lead not found.");
            }

            // 2. Fetch Tenant / Company Details
            $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $companyName = $settings['company_name'] ?? 'Our Company';

            // 3. Generate Proposal via AI
            $aiService = new AIService($this->tenantId);
            
            $prompt = "You are a professional B2B Sales Executive working for '{$companyName}'.
            Write a formal, persuasive business proposal for the following lead:
            - Client Name: {$lead['name']}
            - Client Company: {$lead['company']}
            - Industry: {$lead['industry']}
            - Budget: {$lead['budget']}
            - Timeline: {$lead['timeline']}
            - Research/Notes: {$lead['ai_research_notes']}
            
            The proposal MUST include:
            1. Executive Summary
            2. Scope of Work (proposed solutions)
            3. Pricing & Investment (based on budget)
            4. Next Steps
            
            Format the response as clean HTML. Use <h1>, <h2>, <p>, <ul>, and <table> tags. Do not wrap in ```html block.";

            $proposalHtml = $aiService->generateText($prompt);
            $proposalHtml = str_replace(['```html', '```'], '', $proposalHtml); // Clean markdown

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'proposal_html' => $proposalHtml
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

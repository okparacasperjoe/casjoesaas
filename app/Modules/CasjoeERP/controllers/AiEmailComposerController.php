<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\AIService;
use Exception;
use PDO;

class AiEmailComposerController
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

    public function generateEmail($leadId)
    {
        $type = $_POST['type'] ?? 'follow_up'; // cold, follow_up, welcome, reminder, thank_you
        $tone = $_POST['tone'] ?? 'professional';

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$leadId, $this->tenantId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lead) {
                throw new Exception("Lead not found.");
            }

            $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM erp_settings WHERE tenant_id = ?");
            $stmt->execute([$this->tenantId]);
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $companyName = $settings['company_name'] ?? 'Our Company';

            $aiService = new AIService($this->tenantId);
            
            $prompt = "You are a Sales Manager at '{$companyName}'. 
            Write a '{$type}' email in a {$tone} tone to a lead named {$lead['name']} from {$lead['company']}.
            Industry: {$lead['industry']}
            Budget: {$lead['budget']}
            Research/Notes: {$lead['ai_research_notes']}
            
            Write ONLY the email subject and body. Format it cleanly.";

            $emailText = $aiService->generateText($prompt);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'email' => nl2br(trim($emailText))
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

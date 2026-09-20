<?php

namespace App\Core\Services;

use App\Core\Database;
use App\Core\Mailer;
use App\Core\Services\WhatsAppService;
use App\Core\Services\LeadScoringService;
use Exception;
use PDO;

class WorkflowEngineService
{
    public static function dispatch(int $tenantId, string $triggerType, array $context): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM crm_workflows WHERE tenant_id = :tenant_id AND trigger_type = :trigger_type AND is_active = 1");
        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':trigger_type' => $triggerType
        ]);
        
        $workflows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($workflows as $workflow) {
            $triggerConfig = json_decode($workflow['trigger_config'] ?? '{}', true) ?: [];
            
            $isMatch = true;
            if ($triggerType === 'score_threshold') {
                $isMatch = ($context['score'] ?? 0) >= ($triggerConfig['min_score'] ?? 0);
            } elseif ($triggerType === 'stage_changed') {
                $isMatch = empty($triggerConfig['stage_id']) || $triggerConfig['stage_id'] == ($context['stage_id'] ?? null);
            } elseif ($triggerType === 'form_submitted') {
                $isMatch = empty($triggerConfig['form_id']) || $triggerConfig['form_id'] == ($context['form_id'] ?? null);
            }
            
            if ($isMatch) {
                self::executeWorkflow($tenantId, (int)$workflow['id'], $context, $triggerType);
            }
        }
    }
    
    private static function executeWorkflow(int $tenantId, int $workflowId, array $context, string $triggerType): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Update run count
        $stmt = $db->prepare("UPDATE crm_workflows SET runs_count = runs_count + 1, last_run_at = NOW() WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute([
            ':id' => $workflowId,
            ':tenant_id' => $tenantId
        ]);
        
        $stmt = $db->prepare("SELECT * FROM crm_workflow_steps WHERE workflow_id = :workflow_id AND tenant_id = :tenant_id ORDER BY step_order ASC");
        $stmt->execute([
            ':workflow_id' => $workflowId,
            ':tenant_id' => $tenantId
        ]);
        
        $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($steps as $step) {
            self::executeStep($tenantId, $step, $context, $workflowId, $triggerType);
        }
    }
    
    private static function executeStep(int $tenantId, array $step, array $context, int $workflowId, string $triggerType): void
    {
        $db = Database::getInstance()->getConnection();
        $actionType = $step['action_type'];
        $config = json_decode($step['action_config'], true) ?: [];
        $leadId = $context['lead_id'] ?? null;
        
        $status = 'success';
        $outputMessage = 'Action executed successfully';
        
        try {
            switch ($actionType) {
                case 'send_email':
                    self::actionSendEmail($tenantId, $config, $context);
                    break;
                case 'send_whatsapp':
                    self::actionSendWhatsApp($tenantId, $config, $context);
                    break;
                case 'change_stage':
                    self::actionChangeStage($tenantId, $config, $context);
                    break;
                case 'add_score':
                    self::actionAddScore($tenantId, $config, $context);
                    break;
                case 'notify_user':
                    self::actionNotifyUser($tenantId, $config, $context);
                    break;
                default:
                    $status = 'failed';
                    $outputMessage = 'Unknown action type: ' . $actionType;
                    break;
            }
        } catch (Exception $e) {
            $status = 'failed';
            $outputMessage = $e->getMessage();
        }
        
        $stmt = $db->prepare("
            INSERT INTO crm_workflow_logs (workflow_id, tenant_id, lead_id, trigger_event, step_id, action_taken, status, output_message)
            VALUES (:workflow_id, :tenant_id, :lead_id, :trigger_event, :step_id, :action_taken, :status, :output_message)
        ");
        $stmt->execute([
            ':workflow_id' => $workflowId,
            ':tenant_id' => $tenantId,
            ':lead_id' => $leadId,
            ':trigger_event' => $triggerType,
            ':step_id' => $step['id'],
            ':action_taken' => $actionType,
            ':status' => $status,
            ':output_message' => $outputMessage
        ]);
    }
    
    private static function getLeadDetails(int $tenantId, int $leadId): ?array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$leadId, $tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private static function actionSendEmail(int $tenantId, array $config, array $context): void
    {
        $lead = null;
        if (!empty($context['lead_id']) && (empty($context['email']) || empty($context['name']))) {
            $lead = self::getLeadDetails($tenantId, (int)$context['lead_id']);
            if ($lead) {
                $context['email'] = $context['email'] ?? $lead['email'];
                $context['name'] = $context['name'] ?? $lead['name'];
                $context['phone'] = $context['phone'] ?? $lead['phone'];
            }
        }

        $toRaw = $config['to'] ?? '{{email}}';
        $to = self::replaceVariables($toRaw, $context);
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Recipient email is empty or invalid: " . $to);
        }

        $subject = self::replaceVariables($config['subject'] ?? 'Notification from CasjoeSaaS', $context);
        $body = self::replaceVariables($config['body'] ?? '', $context);
        
        Mailer::send($to, $subject, $body);
    }
    
    private static function actionSendWhatsApp(int $tenantId, array $config, array $context): void
    {
        $lead = null;
        if (!empty($context['lead_id']) && empty($context['phone'])) {
            $lead = self::getLeadDetails($tenantId, (int)$context['lead_id']);
            if ($lead) {
                $context['phone'] = $lead['phone'];
                $context['name'] = $context['name'] ?? $lead['name'];
            }
        }

        $toRaw = $config['to'] ?? '{{phone}}';
        $to = self::replaceVariables($toRaw, $context);
        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        if (empty($cleanPhone)) {
            throw new Exception("Recipient phone number is empty or invalid: " . $to);
        }
        
        $message = self::replaceVariables($config['message'] ?? '', $context);
        
        WhatsAppService::sendMessage($cleanPhone, $message);
    }
    
    private static function actionChangeStage(int $tenantId, array $config, array $context): void
    {
        $newStageId = $config['new_stage_id'] ?? $config['stage_id'] ?? null;
        if (empty($newStageId) || empty($context['lead_id'])) {
            throw new Exception("Missing new_stage_id or lead_id.");
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE erp_crm_leads SET stage_id = :stage_id WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute([
            ':stage_id' => $newStageId,
            ':id' => $context['lead_id'],
            ':tenant_id' => $tenantId
        ]);
    }
    
    private static function actionAddScore(int $tenantId, array $config, array $context): void
    {
        if (empty($config['points']) || empty($context['lead_id'])) {
            throw new Exception("Missing points or lead_id.");
        }
        
        $points = (int) $config['points'];
        $reason = $config['reason'] ?? 'Workflow automated scoring';
        
        // Correct signature: addPoints(tenantId, leadId, reason, points)
        LeadScoringService::addPoints($tenantId, (int)$context['lead_id'], $reason, $points);
    }
    
    private static function actionNotifyUser(int $tenantId, array $config, array $context): void
    {
        $userId = !empty($config['user_id']) ? (int)$config['user_id'] : (int)($_SESSION['user_id'] ?? 1);
        $title = $config['title'] ?? 'Workflow Automation Alert';
        $message = self::replaceVariables($config['message'] ?? 'A workflow triggered an action.', $context);
        $link = $config['link'] ?? '/erp/crm/leads';
        
        \App\Core\Notification::send($userId, $title, $message, $link);
    }
    
    private static function replaceVariables(string $template, array $context): string
    {
        $result = $template;
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                $result = str_replace('{{' . $key . '}}', (string) $value, $result);
            }
        }
        return $result;
    }
}

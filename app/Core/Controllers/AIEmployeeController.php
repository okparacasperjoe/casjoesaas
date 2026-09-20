<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Services\AI\Employees\SalesManagerAgent;
use App\Core\Services\AI\Employees\AccountantAgent;
use App\Core\Services\AI\Employees\SupportAgent;
use PDO;

class AIEmployeeController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        // Assuming simple tenant isolation via user_id for MVP
        $this->tenantId = $_SESSION['user_id'];
        $this->ensureAISchema();
    }

    private function ensureAISchema()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `tenant_ai_employees` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `employee_type` varchar(50) NOT NULL,
              `status` enum('active','suspended','cancelled') NOT NULL DEFAULT 'active',
              `subscription_id` varchar(100) DEFAULT NULL,
              `monthly_fee` decimal(10,2) NOT NULL DEFAULT 10000.00,
              `currency` varchar(3) NOT NULL DEFAULT 'NGN',
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              `expires_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Exception $e) {}

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `tenant_ai_tokens` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `token_balance` bigint(20) NOT NULL DEFAULT 0,
              `lifetime_purchased` bigint(20) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              UNIQUE KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Exception $e) {}

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `ai_action_queue` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `agent` varchar(50) NOT NULL,
              `action_type` varchar(50) NOT NULL,
              `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
              `status` enum('pending','approved','rejected','executed') DEFAULT 'pending',
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Exception $e) {}

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `ai_automations` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `trigger_event` varchar(100) NOT NULL,
              `action_type` varchar(50) NOT NULL,
              `agent_prompt` text NOT NULL,
              `is_active` tinyint(1) DEFAULT 1,
              `created_at` datetime DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Exception $e) {}

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `system_events` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `module` varchar(50) NOT NULL,
              `event_type` varchar(100) NOT NULL,
              `payload` longtext,
              `priority` varchar(20) DEFAULT 'low',
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (\Exception $e) {}
    }

    public function index()
    {
        // Fetch active subscriptions
        $stmt = $this->pdo->prepare("SELECT employee_type, status FROM tenant_ai_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $activeEmployees = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $activeTab = $_GET['agent'] ?? 'dashboard';

        // Fake subscription check for MVP
        $isSubscribed = true; // In production, check subscriptions table
        
        // Tokens
        $tokensAvailable = 0;
        try {
            $stmtSub = $this->pdo->prepare("SELECT ai_credits, ai_tokens_limit, ai_tokens_used FROM subscriptions WHERE tenant_id = ? LIMIT 1");
            $stmtSub->execute([$this->tenantId]);
            $subRow = $stmtSub->fetch();
            if ($subRow) {
                $tokensAvailable = max(0, ($subRow['ai_tokens_limit'] ?? 0) - ($subRow['ai_tokens_used'] ?? 0)) + ($subRow['ai_credits'] ?? 0);
            } else {
                $tokensAvailable = 50000; // Default starter tokens
            }
        } catch (\Exception $e) {}

        $actionQueue = [];
        if ($activeTab === 'queue') {
            $stmt = $this->pdo->prepare("SELECT * FROM ai_action_queue WHERE tenant_id = ? AND status = 'pending' ORDER BY created_at DESC");
            $stmt->execute([$this->tenantId]);
            $actionQueue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $automations = [];
        if ($activeTab === 'automations' || $activeTab === 'dashboard') {
            $stmt = $this->pdo->prepare("SELECT * FROM ai_automations WHERE tenant_id = ? ORDER BY created_at DESC");
            $stmt->execute([$this->tenantId]);
            $automations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $recentEvents = [];
        if ($activeTab === 'dashboard') {
            try {
                // Fetch recent context events across the ecosystem
                $stmt = $this->pdo->prepare("SELECT * FROM system_events WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 5");
                $stmt->execute([$this->tenantId]);
                $recentEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        
        $agent = [];
        if (!in_array($activeTab, ['dashboard', 'queue', 'automations'])) {
            if ($activeTab === 'sales_manager') {
                $agent = ['name' => 'AI Sales Manager', 'model' => 'Casjoe-CX-1.0', 'icon' => 'trending-up-outline', 'greeting' => "Hello! I am your AI Sales Manager. I can draft emails to leads, analyze your sales pipeline, and propose follow-up strategies. What would you like to do?"];
            } elseif ($activeTab === 'accountant') {
                $agent = ['name' => 'AI Accountant', 'model' => 'Casjoe-Fin-1.0', 'icon' => 'calculator-outline', 'greeting' => "Good day. I am your AI Accountant. I can help reconcile transactions, draft invoices, and analyze financial reports. How can I assist?"];
            } elseif ($activeTab === 'support_agent') {
                $agent = ['name' => 'AI Support Agent', 'model' => 'Casjoe-Support-1.0', 'icon' => 'headset-outline', 'greeting' => "Hello! I am your AI Support Agent. I can check your open support tickets and draft replies for you. What would you like to look at today?"];
            } else {
                $agent = ['name' => 'AI Employee', 'model' => 'GPT-4o', 'icon' => 'person-outline', 'greeting' => "How can I help you today?"];
            }
        }

        require __DIR__ . '/../Views/ai/office.php';
    }

    public function chat()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $agentType = $_POST['agent'] ?? 'sales_manager';
        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['error' => 'Empty message']);
            return;
        }

        // Check subscription (MVP bypassed)
        /*
        $stmt = $this->pdo->prepare("SELECT status FROM tenant_ai_employees WHERE tenant_id = ? AND employee_type = ? AND status = 'active'");
        $stmt->execute([$this->tenantId, $agentType]);
        if (!$stmt->fetch()) {
            echo json_encode(['error' => 'You do not have an active subscription for this AI Employee.']);
            return;
        }
        */

        $agent = null;
        if ($agentType === 'sales_manager') {
            $agent = new SalesManagerAgent();
        } elseif ($agentType === 'accountant') {
            $agent = new AccountantAgent();
        } elseif ($agentType === 'support_agent') {
            $agent = new SupportAgent();
        } else {
            echo json_encode(['error' => 'Unknown agent']);
            return;
        }

        try {
            $response = $agent->processMessage($this->tenantId, $message);
            echo json_encode(['response' => $response]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function approveQueueAction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = $_POST['action_id'] ?? 0;

        // Fetch action
        $stmt = $this->pdo->prepare("SELECT * FROM ai_action_queue WHERE id = ? AND tenant_id = ? AND status = 'pending'");
        $stmt->execute([$id, $this->tenantId]);
        $action = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$action) {
            echo json_encode(['error' => 'Action not found or already processed']);
            return;
        }

        // Execute logic based on action_type
        // For phase 3 MVP, we support whatsapp_message
        if ($action['action_type'] === 'send_whatsapp') {
            $payload = json_decode($action['payload'], true);
            $whatsappService = new \App\Core\Services\WhatsAppService();
            $success = $whatsappService->sendMessage($payload['to'], $payload['message']);
            
            if ($success) {
                // Update status
                $uStmt = $this->pdo->prepare("UPDATE ai_action_queue SET status = 'executed' WHERE id = ?");
                $uStmt->execute([$id]);

                // Also log to EventBus if needed
                \App\Core\EventBus::dispatch('ai_action_executed', $this->tenantId, [
                    'agent' => $action['agent'],
                    'action' => 'send_whatsapp',
                    'to' => $payload['to']
                ]);

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Failed to send WhatsApp message. Check integration settings.']);
            }
        } else {
            // Generic approve
            $uStmt = $this->pdo->prepare("UPDATE ai_action_queue SET status = 'executed' WHERE id = ?");
            $uStmt->execute([$id]);
            echo json_encode(['success' => true]);
        }
    }

    public function rejectQueueAction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = $_POST['action_id'] ?? 0;

        $uStmt = $this->pdo->prepare("UPDATE ai_action_queue SET status = 'rejected' WHERE id = ? AND tenant_id = ? AND status = 'pending'");
        if ($uStmt->execute([$id, $this->tenantId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to reject']);
        }
    }

    public function createAutomation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $prompt = trim($_POST['prompt'] ?? '');
        $name = trim($_POST['name'] ?? 'New Automation');
        $trigger = trim($_POST['trigger_event'] ?? 'custom_event');
        $agentRole = trim($_POST['agent_role'] ?? 'sales_manager');

        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt is required']);
            return;
        }

        $action = 'webhook';
        if (stripos($prompt, 'whatsapp') !== false) {
            $action = 'send_whatsapp';
        } elseif (stripos($prompt, 'email') !== false) {
            $action = 'send_email';
        }

        $stmt = $this->pdo->prepare("INSERT INTO ai_automations (tenant_id, name, trigger_event, action_type, agent_role, agent_prompt) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$this->tenantId, $name, $trigger, $action, $agentRole, $prompt])) {
            header('Location: /ai-office?agent=automations');
            exit;
        } else {
            echo json_encode(['error' => 'Failed to save automation']);
        }
    }

    public function deleteAutomation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = $_POST['id'] ?? 0;

        $stmt = $this->pdo->prepare("DELETE FROM ai_automations WHERE id = ? AND tenant_id = ?");
        if ($stmt->execute([$id, $this->tenantId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to delete automation']);
        }
    }
}

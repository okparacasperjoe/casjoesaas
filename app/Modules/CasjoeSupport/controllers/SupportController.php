<?php

namespace App\Modules\CasjoeSupport\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class SupportController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct()
    {
        // Init moved to helper to ensure context valid when needed
    }

    private function init() {
        $this->tenantId = TenantContext::getTenantId() ?: 1;
        $this->pdo = Database::getInstance()->getConnection();
        
        if (!isset($_SESSION['user_id'])) {
             header('Location: /login');
             exit;
        }
        $this->userId = $_SESSION['user_id'];
    }

    public function index()
    {
        $this->init();
        
        // Fetch Tickets
        $stmt = $this->pdo->prepare("SELECT * FROM cs_tickets WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId, $this->userId]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/index.php';
    }

    public function create()
    {
        $this->init();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';

            if ($subject && $message) {
                // Create Ticket
                $stmt = $this->pdo->prepare("INSERT INTO cs_tickets (tenant_id, user_id, subject, priority) VALUES (?, ?, ?, ?)");
                $stmt->execute([$this->tenantId, $this->userId, $subject, $priority]);
                $ticketId = $this->pdo->lastInsertId();

                // Create First Message
                $stmt = $this->pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
                $stmt->execute([$ticketId, $this->userId, $message]);

                // --- Unified Inbox Integration ---
                try {
                    $userName = $_SESSION['user_name'] ?? 'User';
                    $userEmail = $_SESSION['user_email'] ?? null;
                    $convId = \App\Core\Services\InboxService::createConversation([
                        'tenant_id' => $this->tenantId,
                        'contact_name' => $userName,
                        'contact_email' => $userEmail,
                        'channel' => 'support',
                        'subject' => $subject,
                        'source_type' => 'cs_tickets',
                        'source_id' => $ticketId,
                    ]);
                    \App\Core\Services\InboxService::addMessage($convId, [
                        'tenant_id' => $this->tenantId,
                        'direction' => 'inbound',
                        'sender_type' => 'contact',
                        'sender_name' => $userName,
                        'content' => $message,
                        'channel' => 'support',
                    ]);
                } catch (\Exception $e) {
                    error_log('Inbox integration error (support): ' . $e->getMessage());
                }

                header('Location: /support/view?id=' . $ticketId);
                exit;
            }
        }

        require __DIR__ . '/../Views/create.php';
    }

    public function view()
    {
        $this->init();
        $id = $_GET['id'] ?? 0;

        // Fetch Ticket
        $stmt = $this->pdo->prepare("SELECT * FROM cs_tickets WHERE id = ? AND tenant_id = ? AND user_id = ?");
        $stmt->execute([$id, $this->tenantId, $this->userId]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            die("Ticket not found or access denied.");
        }

        // Handle Reply
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $message = $_POST['message'] ?? '';
             if ($message) {
                 $stmt = $this->pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
                 $stmt->execute([$id, $this->userId, $message]);
                 
                 // --- Unified Inbox Integration ---
                 try {
                     $userName = $_SESSION['user_name'] ?? 'User';
                     $conv = \App\Core\Services\InboxService::findConversationBySource($this->tenantId, 'cs_tickets', $id);
                     if ($conv) {
                         \App\Core\Services\InboxService::addMessage($conv['id'], [
                             'tenant_id' => $this->tenantId,
                             'direction' => 'inbound',
                             'sender_type' => 'contact',
                             'sender_name' => $userName,
                             'content' => $message,
                             'channel' => 'support',
                         ]);
                     }
                 } catch (\Exception $e) {
                     error_log('Inbox integration error (support reply): ' . $e->getMessage());
                 }

                 // Update Status to Open if closed
                 if($ticket['status'] === 'closed') {
                     $this->pdo->prepare("UPDATE cs_tickets SET status = 'open' WHERE id = ?")->execute([$id]);
                 }
                 
                 header('Location: /support/view?id=' . $id);
                 exit;
             }
        }

        // Fetch Messages
        $stmt = $this->pdo->prepare("SELECT * FROM cs_messages WHERE ticket_id = ? ORDER BY created_at ASC");
        $stmt->execute([$id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/view.php';
    }

    public function aiChat()
    {
        $this->init();
        header('Content-Type: application/json');

        $message = trim($_POST['message'] ?? '');
        $ticketId = (int)($_POST['ticket_id'] ?? 0);

        if (empty($message)) {
            @ob_clean();
            echo json_encode(['success' => false, 'error' => 'Please enter a message or question for Cori AI.']);
            exit;
        }

        // Check token balance explicitly using AICreditService
        $creditSvc = new \App\Core\AI\AICreditService();
        if (!$creditSvc->hasCredits($this->tenantId, 50)) {
            @ob_clean();
            echo json_encode([
                'success' => false, 
                'error' => '⚡ You have exhausted your Cori AI Tokens balance. Please go to Billing (/billing) to top up AI credits.',
                'exhausted' => true
            ]);
            exit;
        }

        try {
            $ai = new \App\Core\AI\AIService();
            
            $ticketContext = '';
            if ($ticketId > 0) {
                $stmt = $this->pdo->prepare("SELECT subject, status FROM cs_tickets WHERE id = ? AND tenant_id = ?");
                $stmt->execute([$ticketId, $this->tenantId]);
                $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($ticket) {
                    $ticketContext = "Current Ticket Context: Subject='{$ticket['subject']}' (Status: {$ticket['status']}). ";
                }
            }

            $prompt = "You are Cori AI, the intelligent, highly capable technical support specialist and AI helpdesk agent for Casjoe SaaS platform. "
                . "You are assisting a customer directly inside their Support & Helpdesk center. "
                . $ticketContext
                . "Answer their question clearly, provide step-by-step guidance if troubleshooting, explain features accurately, and be professional, concise, and helpful. "
                . "Customer Question/Issue: {$message}";

            // AIService::generate automatically checks and deducts tokens via AICreditService::consume!
            $reply = $ai->generate($prompt, ['max_tokens' => 450], 'ai_support_agent', $this->userId);

            @ob_clean();
            echo json_encode(['success' => true, 'reply' => $reply]);
            exit;
        } catch (\App\Core\AI\CreditExhaustedException $e) {
            @ob_clean();
            echo json_encode([
                'success' => false, 
                'error' => '⚡ You have exhausted your Cori AI Tokens balance. Please go to Billing (/billing) to top up AI credits.',
                'exhausted' => true
            ]);
            exit;
        } catch (\Exception $e) {
            @ob_clean();
            echo json_encode(['success' => false, 'error' => 'AI Assistant encountered an issue: ' . $e->getMessage()]);
            exit;
        }
    }
}

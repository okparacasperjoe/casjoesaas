<?php

namespace App\Core\Controllers;

use App\Core\Auth;

class SupportController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance()->getConnection();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tenantId = \App\Core\TenantContext::getTenantId();

        $stmt = $this->pdo->prepare("SELECT * FROM cs_tickets WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Support";
        require __DIR__ . '/../Views/support/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $title = "New Ticket";
        require __DIR__ . '/../Views/support/create.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $tenantId = \App\Core\TenantContext::getTenantId(); // May be null for global user
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';

            if ($subject && $message) {
                // Determine tenant_id (use 0/1 or actual tenant if user belongs to one)
                // For global support, maybe tenant_id isn't critical or we fetch from user
                // Assuming standard schema expects tenant_id
                if (!$tenantId) $tenantId = 1; 

                $stmt = $this->pdo->prepare("INSERT INTO cs_tickets (tenant_id, user_id, subject, priority) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tenantId, $userId, $subject, $priority]);
                $ticketId = $this->pdo->lastInsertId();

                $stmt = $this->pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
                $stmt->execute([$ticketId, $userId, $message]);

                header('Location: /support?success=1');
                exit;
            }
        }
        header('Location: /support/create?error=missing_fields');
        exit;
    }

    public function view()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $userId = $_SESSION['user_id'];
        $tenantId = \App\Core\TenantContext::getTenantId();

        $stmt = $this->pdo->prepare("SELECT * FROM cs_tickets WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$ticket) {
            header('Location: /support');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
            $message = trim($_POST['message']);
            $stmt = $this->pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
            $stmt->execute([$id, $userId, $message]);

            if ($ticket['status'] === 'closed') {
                $stmt = $this->pdo->prepare("UPDATE cs_tickets SET status = 'open' WHERE id = ?");
                $stmt->execute([$id]);
            }
            header("Location: /support/view?id=" . $id);
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cs_messages WHERE ticket_id = ? ORDER BY created_at ASC");
        $stmt->execute([$id]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/support/view.php';
    }

    public function aiChat()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            @ob_clean();
            echo json_encode(['success' => false, 'error' => 'Please login to use AI helpdesk.']);
            exit;
        }

        header('Content-Type: application/json');
        $message = trim($_POST['message'] ?? '');
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $userId = $_SESSION['user_id'];
        $tenantId = \App\Core\TenantContext::getTenantId();

        if (empty($message)) {
            @ob_clean();
            echo json_encode(['success' => false, 'error' => 'Please enter a message or question for Cori AI.']);
            exit;
        }

        $creditSvc = new \App\Core\AI\AICreditService();
        if (!$creditSvc->hasCredits($tenantId, 50)) {
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
                $stmt = $this->pdo->prepare("SELECT subject, status FROM cs_tickets WHERE id = ?");
                $stmt->execute([$ticketId]);
                $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($ticket) {
                    $ticketContext = "Current Ticket Context: Subject='{$ticket['subject']}' (Status: {$ticket['status']}). ";
                }
            }

            $prompt = "You are Cori AI, the intelligent, highly capable technical support specialist and AI helpdesk agent for Casjoe SaaS platform. "
                . "You are assisting a customer directly inside their Support & Helpdesk center. "
                . $ticketContext
                . "Answer their question clearly, provide step-by-step guidance if troubleshooting, explain features accurately, and be professional, concise, and helpful. "
                . "Customer Question/Issue: {$message}";

            $reply = $ai->generate($prompt, ['max_tokens' => 450], 'ai_support_agent', $userId);
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

    public function reply($id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $id ?? $_POST['ticket_id'] ?? $_GET['id'] ?? 0;
        $userId = $_SESSION['user_id'];
        $message = trim($_POST['message'] ?? '');

        if ($id && $message) {
            $stmt = $this->pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
            $stmt->execute([$id, $userId, $message]);

            $stmt = $this->pdo->prepare("UPDATE cs_tickets SET status = 'open' WHERE id = ?");
            $stmt->execute([$id]);
        }

        header("Location: /support/ticket/" . $id);
        exit;
    }
}

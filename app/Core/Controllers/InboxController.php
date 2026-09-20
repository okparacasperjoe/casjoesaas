<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\InboxService;
use App\Core\Mailer;
use App\Core\Services\WhatsAppService;
use PDO;

class InboxController
{
    private PDO $pdo;
    private int $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        $channel = $_GET['channel'] ?? 'all';
        $status = $_GET['status'] ?? 'open';
        $search = $_GET['search'] ?? $_GET['q'] ?? '';

        $filters = [
            'status' => $status,
        ];
        if ($channel && $channel !== 'all') {
            $filters['channel'] = $channel;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        $conversations = InboxService::getConversations($this->tenantId, $filters);
        $currentChannel = $channel;
        $currentStatus = $status;
        $searchQuery = $search;

        require_once __DIR__ . '/../Views/inbox/index.php';
    }

    public function conversation()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: /inbox");
            exit;
        }

        $conversationId = (int)$id;

        // Mark as read
        InboxService::markAsRead($conversationId, $this->tenantId);

        $messages = InboxService::getMessages($conversationId, $this->tenantId);
        
        // Fetch conversation details
        $stmt = $this->pdo->prepare("SELECT * FROM inbox_conversations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$conversationId, $this->tenantId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$conversation) {
            header("Location: /inbox");
            exit;
        }

        $lead = null;
        if (!empty($conversation['contact_id'])) {
            $stmt = $this->pdo->prepare("SELECT * FROM erp_crm_leads WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$conversation['contact_id'], $this->tenantId]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../Views/inbox/conversation.php';
    }

    public function reply()
    {
        $conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : null;
        $message = trim($_POST['message'] ?? '');

        if (!$conversationId || $message === '') {
            header("Location: /inbox");
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM inbox_conversations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$conversationId, $this->tenantId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conversation) {
            header("Location: /inbox");
            exit;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $userName = $_SESSION['name'] ?? 'Staff';

        InboxService::addMessage($conversationId, [
            'tenant_id' => $this->tenantId,
            'direction' => 'outbound',
            'sender_type' => 'user',
            'sender_id' => $userId,
            'sender_name' => $userName,
            'content' => $message,
            'channel' => $conversation['channel'] ?? 'chat',
        ]);

        $channel = $conversation['channel'];
        if ($channel === 'support' && $conversation['source_type'] === 'cs_tickets') {
            $ticketId = $conversation['source_id'];
            $stmt = $this->pdo->prepare("INSERT INTO cs_messages (tenant_id, ticket_id, user_id, message, is_staff, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
            $stmt->execute([$this->tenantId, $ticketId, $userId, $message]);
            
            $stmt = $this->pdo->prepare("UPDATE cs_tickets SET status = 'answered' WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$ticketId, $this->tenantId]);
        } elseif ($channel === 'email' && !empty($conversation['contact_email'])) {
            Mailer::send(
                $conversation['contact_email'],
                'Reply to your message',
                $message
            );
        } elseif ($channel === 'whatsapp' && !empty($conversation['contact_phone'])) {
            try {
                WhatsAppService::sendMessage($conversation['contact_phone'], $message);
            } catch (\Exception $e) {
                error_log("Failed to send WhatsApp message: " . $e->getMessage());
            }
        }

        header("Location: /inbox/conversation?id=" . $conversationId);
        exit;
    }

    public function close()
    {
        $conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : null;
        if ($conversationId) {
            InboxService::closeConversation($conversationId, $this->tenantId);
        }
        header("Location: /inbox");
        exit;
    }

    public function apiUnreadCount()
    {
        header('Content-Type: application/json');
        
        $stmt = $this->pdo->prepare("SELECT SUM(unread_count) as total FROM inbox_conversations WHERE tenant_id = ? AND status = 'open'");
        $stmt->execute([$this->tenantId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $count = $result['total'] ? (int)$result['total'] : 0;
        
        echo json_encode(['count' => $count]);
        exit;
    }
}

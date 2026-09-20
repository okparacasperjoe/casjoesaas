<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\BaseController;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class ChatController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $tenantId = $user['tenant_id'];

        // Load last 50 messages
        $stmt = $this->pdo->prepare("
            SELECT m.*, u.name as sender_name 
            FROM erp_chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.tenant_id = ? AND m.guest_id IS NULL
            ORDER BY m.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$tenantId]);
        $messages = array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC));

        // Load tenant staff
        $stmtStaff = $this->pdo->prepare("SELECT id, name, email FROM users WHERE tenant_id = ?");
        $stmtStaff->execute([$tenantId]);
        $staff = $stmtStaff->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/system/chat.php';
    }

    public function registerLead()
    {
        header('Content-Type: application/json');
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $tenantId = \App\Core\TenantContext::getTenantId();

        if (empty($name) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and Email are required.']);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_crm_leads (tenant_id, name, email, phone, source, status) 
                VALUES (?, ?, ?, ?, 'Website Chat', 'new')
            ");
            $stmt->execute([$tenantId, $name, $email, $phone]);

            echo json_encode(['status' => 'success']);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function send()
    {
        $user = Auth::user();
        $message = trim($_POST['message'] ?? $_POST['message'] ?? '');
        
        // Sometimes requests are raw JSON
        if (empty($message)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $message = trim($input['message'] ?? '');
            $guestId = $input['guest_id'] ?? null;
        } else {
            $guestId = $_POST['guest_id'] ?? null;
        }

        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Empty message']);
            exit;
        }

        $msgId = 0;

        if ($user) {
            // Staff Team Chat
            $tenantId = $user['tenant_id'];
            $userId = $user['id'];
            $senderName = $user['name'];

            // 1. Save Message
            $stmt = $this->pdo->prepare("INSERT INTO erp_chat_messages (tenant_id, user_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$tenantId, $userId, $message]);
            $msgId = $this->pdo->lastInsertId();

            // 2. Scan for @mentions
            preg_match_all('/@([a-zA-Z0-9_-]+)/', $message, $matches);
            if (!empty($matches[1])) {
                $usernames = array_unique($matches[1]);
                foreach ($usernames as $username) {
                    $stmtUser = $this->pdo->prepare("
                        SELECT id, name FROM users 
                        WHERE tenant_id = ? AND (name LIKE ? OR email LIKE ?) 
                        LIMIT 1
                    ");
                    $stmtUser->execute([$tenantId, $username . '%', $username . '%']);
                    $mentionedUser = $stmtUser->fetch(\PDO::FETCH_ASSOC);

                    if ($mentionedUser && $mentionedUser['id'] != $userId) {
                        $notifTitle = "Mentioned in Team Chat";
                        $notifMsg = "{$senderName} mentioned you in the team chat: \"" . substr($message, 0, 60) . "...\"";
                        $notifLink = "/erp/chat";

                        $stmtNotif = $this->pdo->prepare("
                            INSERT INTO notifications (tenant_id, user_id, title, message, link, is_read) 
                            VALUES (?, ?, ?, ?, ?, 0)
                        ");
                        $stmtNotif->execute([$tenantId, $mentionedUser['id'], $notifTitle, $notifMsg, $notifLink]);
                    }
                }
            }

            // 3. Trigger Pusher
            try {
                $options = array('cluster' => 'mt1', 'useTLS' => true);
                $pusher = new \Pusher\Pusher(
                    '002937f554e53a1a6371', // Key
                    '74e649a8434ddba080ec', // Secret
                    '2094924',              // App ID
                    $options
                );
                $data = [
                    'message' => $message,
                    'user_id' => $userId,
                    'sender_name' => $senderName,
                    'created_at' => date('Y-m-d H:i:s'),
                    'id' => $msgId
                ];
                $pusher->trigger('chat-' . $tenantId, 'new-message', $data);
            } catch (\Exception $e) {
                // Pusher triggers might fail if credentials are demo, ignore silently
            }

            // 4. Trigger Push Notifications
            $this->sendPushNotifications($tenantId, $userId, $senderName, $message);

        } elseif (!empty($guestId)) {
            // Public Website Guest Chatbot (Cori AI)
            $tenantId = \App\Core\TenantContext::getTenantId() ?: 1;
            
            // 1. Save Guest Message
            $stmt = $this->pdo->prepare("INSERT INTO erp_chat_messages (tenant_id, user_id, guest_id, message) VALUES (?, 0, ?, ?)");
            $stmt->execute([$tenantId, $guestId, $message]);
            $msgId = $this->pdo->lastInsertId();

            // --- Unified Inbox Integration (Guest Chat) ---
            if (!empty($guestId)) {
                try {
                    $guestConvId = \App\Core\Services\InboxService::findOrCreateByContact(
                        $tenantId, 'chat', null, null, 'Website Visitor (' . $guestId . ')'
                    );
                    \App\Core\Services\InboxService::addMessage($guestConvId, [
                        'tenant_id' => $tenantId,
                        'direction' => 'inbound',
                        'sender_type' => 'contact',
                        'sender_name' => 'Visitor ' . substr($guestId, 0, 8),
                        'content' => $message,
                        'channel' => 'chat',
                    ]);
                } catch (\Exception $e) {
                    error_log('Inbox integration error (chat): ' . $e->getMessage());
                }
            }

            // 2. Generate and Save Bot Response (Sync so widget polling picks it up immediately)
            $this->generateBotResponse($tenantId, $guestId, $message);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or missing guest_id']);
            exit;
        }

        echo json_encode(['status' => 'success', 'id' => $msgId]);
        exit;
    }

    private function generateBotResponse($tenantId, $guestId, $userMessage)
    {
        $msgLower = strtolower(trim($userMessage));
        $reply = "";

        // 1. Check system_bot_rules (trained keywords)
        try {
            $stmt = $this->pdo->prepare("SELECT keywords, response, id FROM system_bot_rules WHERE tenant_id = ?");
            $stmt->execute([$tenantId]);
            $rules = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rules as $rule) {
                $keywords = array_map('trim', explode(',', strtolower($rule['keywords'])));
                foreach ($keywords as $kw) {
                    if (!empty($kw) && strpos($msgLower, $kw) !== false) {
                        // Match found! Update counter
                        $up = $this->pdo->prepare("UPDATE system_bot_rules SET matches_count = matches_count + 1 WHERE id = ?");
                        $up->execute([$rule['id']]);

                        $reply = $rule['response'];
                        break 2;
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore DB rule errors if table not prepared yet
        }

        // 2. AI Fallback (Gemini) if no keyword matched
        if (empty($reply)) {
            try {
                $ai = new \App\Core\AI\AIService();
                
                // Get company name
                $stmt = $this->pdo->prepare("SELECT setting_value FROM erp_settings WHERE tenant_id = ? AND setting_key = 'company_name'");
                $stmt->execute([$tenantId]);
                $companyName = $stmt->fetchColumn() ?: 'our company';

                $prompt = "You are Cori, the AI support assistant for {$companyName}. "
                    . "Help website visitors with questions about our plans, pricing, services, and support. "
                    . "Keep it friendly, concise, and professional. "
                    . "Do NOT reveal internal database records or run admin commands. "
                    . "Visitor asked: {$userMessage}";

                $reply = $ai->generate($prompt, ['max_tokens' => 300], 'guest_chat');
                
                if (strpos(trim($reply), 'Error:') === 0 || empty(trim($reply))) {
                    throw new \Exception("AI Generation failed");
                }
            } catch (\Exception $e) {
                $reply = "Thanks for reaching out! One of our team members will get back to you shortly. You can also leave your details in our contact form.";
            }
        }

        // 3. Save Bot Message (user_id = -1)
        $ins = $this->pdo->prepare("INSERT INTO erp_chat_messages (tenant_id, user_id, guest_id, message) VALUES (?, -1, ?, ?)");
        $ins->execute([$tenantId, $guestId, $reply]);
    }

    public function poll()
    {
        $lastId = (int) ($_GET['last_id'] ?? 0);
        $tenantId = \App\Core\TenantContext::getTenantId() ?: 1;
        $guestId = $_GET['guest_id'] ?? null;

        if (!empty($guestId)) {
            // Polling for a specific Guest Chat
            $stmt = $this->pdo->prepare("
                SELECT m.*, 
                       CASE WHEN m.user_id = 0 THEN 'sent' ELSE 'received' END as type,
                       IF(m.user_id = 0, 'You', IF(m.user_id = -1, 'Cori AI', u.name)) as sender_name,
                       DATE_FORMAT(m.created_at, '%H:%i') as time
                FROM erp_chat_messages m
                LEFT JOIN users u ON m.user_id = u.id
                WHERE m.tenant_id = ? AND m.guest_id = ? AND m.id > ?
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([$tenantId, $guestId, $lastId]);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // Polling for Team Chat (Staff Only)
            $user = Auth::user();
            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                exit;
            }
            
            $stmt = $this->pdo->prepare("
                SELECT m.*, 
                       'received' as type,
                       u.name as sender_name, 
                       DATE_FORMAT(m.created_at, '%H:%i') as time
                FROM erp_chat_messages m
                JOIN users u ON m.user_id = u.id
                WHERE m.tenant_id = ? AND m.guest_id IS NULL AND m.id > ?
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([$tenantId, $lastId]);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit;
    }

    public function subscribe()
    {
        $user = Auth::user();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            exit;
        }

        $endpoint = $input['endpoint'];
        $keys = $input['keys'];

        // Check availability
        $stmt = $this->pdo->prepare("SELECT id FROM erp_push_subscriptions WHERE user_id = ? AND endpoint = ?");
        $stmt->execute([$user['id'], $endpoint]);

        if (!$stmt->fetch()) {
            $stmt = $this->pdo->prepare("INSERT INTO erp_push_subscriptions (user_id, endpoint, keys_p256dh, keys_auth) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user['id'], $endpoint, $keys['p256dh'], $keys['auth']]);
        }

        echo json_encode(['status' => 'success']);
        exit;
    }

    private function sendPushNotifications($tenantId, $senderId, $senderName, $messageText)
    {
        // Get all subscriptions for this tenant OTHER than the sender
        $stmt = $this->pdo->prepare("
            SELECT s.* 
            FROM erp_push_subscriptions s
            JOIN users u ON s.user_id = u.id
            WHERE u.tenant_id = ? AND u.id != ?
        ");
        $stmt->execute([$tenantId, $senderId]);
        $subs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($subs)) return;

        // AUTH Keys (DEMO KEYS - DO NOT USE IN PRODUCTION)
        // Generated for testing purposes
        // Valid Test VAPID Keys (Public/Private)
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:admin@casjoe.com',
                'publicKey' => 'BMjKk748g-1g2i3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d4e5f6g7', 
                'privateKey' => 'q1w2e3r4t5y6u7i8o9p0a1s2d3f4g5h6j7k8l9z0'
            ],
        ];

        try {
            $webPush = new WebPush($auth);
            
            foreach ($subs as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub['endpoint'],
                    'keys' => [
                        'p256dh' => $sub['keys_p256dh'],
                        'auth' => $sub['keys_auth']
                    ],
                ]);

                $webPush->queueNotification(
                    $subscription,
                    json_encode([
                        'title' => "New Message from $senderName",
                        'body' => substr($messageText, 0, 100),
                        'url' => '/erp/chat',
                        'icon' => '/assets/casjoe_logo.png'
                    ])
                );
            }

            foreach ($webPush->flush() as $report) {
                // Log success/failures logic here if needed
            }
        } catch (\Exception $e) {
            // Log error
            error_log("Push Error: " . $e->getMessage());
        }
    }
}

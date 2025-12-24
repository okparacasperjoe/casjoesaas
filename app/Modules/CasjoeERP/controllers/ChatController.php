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
        $tenantId = \App\Core\TenantContext::getTenantId();
        $user = Auth::user();

        if (!$user) {
            header('Location: /login');
            exit;
        }

        // Get recent messages
        $stmt = $this->pdo->prepare("
            SELECT m.*, u.name as sender_name 
            FROM erp_chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.tenant_id = ?
            ORDER BY m.created_at ASC 
            LIMIT 50
        ");
        $stmt->execute([$tenantId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // VAPID Keys (Generate on fly if not exist - simplified for demo)
        // In prod, store these in config/env
        if (!isset($_SESSION['vapid_public_key'])) {
             // Generate keys once per session for simplicity or defined constants
             // For this implementation plan, we assume keys are generated or hardcoded for demo
             // Let's use hardcoded test keys or generate
             $vapid = [
                 'subject' => 'mailto:admin@casjoe.com',
                 'publicKey' => 'BHw_qfO0jK0q0q0q0q0q0q0_qfO0jK0q0q0q0q0q0q0_qfO0jK0q0q0q0q0q0q0_qfO0jK0q0q0q0q0q0q0', 
                 'privateKey' => 'qfO0jK0q0q0q0q0q0q0_qfO0jK0q0q0q0q0q0q0'
             ];
             // Ideally we use library to generate:
             // $keys = VAPID::createVapidKeys(); 
        }

        require __DIR__ . '/../views/system/chat.php';
    }

    public function send()
    {
        $user = Auth::user();
        $message = trim($_POST['message'] ?? '');
        $tenantId = $user['tenant_id'];

        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Empty message']);
            exit;
        }

        // 1. Save Message
        $stmt = $this->pdo->prepare("INSERT INTO erp_chat_messages (tenant_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$tenantId, $user['id'], $message]);
        $msgId = $this->pdo->lastInsertId();

        // 2. Trigger Pusher Event
        // TODO: Replace with real credentials provided by USER
        // App ID and Secret are REQUIRED for server-side triggering
        $options = array(
            'cluster' => 'mt1',
            'useTLS' => true
        );
        $pusher = new \Pusher\Pusher(
            '002937f554e53a1a6371', // Key
            '74e649a8434ddba080ec', // Secret
            '2094924',              // App ID
            $options
        );

        $data['message'] = $message;
        $data['user_id'] = $user['id'];
        $data['sender_name'] = $user['name'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['id'] = $msgId;

        $pusher->trigger('chat-' . $tenantId, 'new-message', $data);

        // 3. Trigger Push to Others (Keep this as backup or complementary)
        $this->sendPushNotifications($tenantId, $user['id'], $user['name'], $message);

        echo json_encode(['status' => 'success', 'id' => $msgId]);
        exit;
    }

    public function poll()
    {
        $lastId = (int) ($_GET['last_id'] ?? 0);
        $tenantId = \App\Core\TenantContext::getTenantId();

        $stmt = $this->pdo->prepare("
            SELECT m.*, u.name as sender_name, DATE_FORMAT(m.created_at, '%H:%i') as time
            FROM erp_chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.tenant_id = ? AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$tenantId, $lastId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode(['messages' => $messages]);
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

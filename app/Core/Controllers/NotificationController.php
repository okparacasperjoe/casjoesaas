<?php

namespace App\Core\Controllers;

use App\Core\Notification;

class NotificationController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $notifications = Notification::getAll($userId);

        require __DIR__ . '/../Views/notifications.php';
    }

    public function markRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['id'] ?? null;
            if ($notificationId) {
                Notification::markAsRead($notificationId);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
    }
    
    public function markAllRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$userId]);
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false]);
    }
}

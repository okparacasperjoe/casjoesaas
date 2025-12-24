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
        $this->tenantId = TenantContext::getTenantId();
        SubscriptionManager::requireActive($this->tenantId);
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

        require __DIR__ . '/../views/index.php';
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

                header('Location: /support/view?id=' . $ticketId);
                exit;
            }
        }

        require __DIR__ . '/../views/create.php';
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

        require __DIR__ . '/../views/view.php';
    }
}

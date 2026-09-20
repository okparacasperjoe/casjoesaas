<?php

namespace App\Modules\CasjoeSupport\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class TicketController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        $userId = $_SESSION['user_id'];

        $stmt = $db->query("SELECT * FROM support_tickets WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC", [$tenantId, $userId]);
        $tickets = $stmt->fetchAll();

        require __DIR__ . '/../Views/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/create.php';
    }

    public function store()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        $userId = $_SESSION['user_id'];

        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $priority = $_POST['priority'];

        // Create Ticket
        $stmt = $db->prepare("INSERT INTO support_tickets (tenant_id, user_id, subject, priority, status) VALUES (?, ?, ?, ?, 'open')");
        $stmt->execute([$tenantId, $userId, $subject, $priority]);
        $ticketId = $db->lastInsertId();

        // Add Initial Message
        $stmt = $db->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
        $stmt->execute([$ticketId, $userId, $message]);

        header('Location: /support');
    }

    public function show($id)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        // $userId = $_SESSION['user_id']; // For security check ownership later

        $stmt = $db->query("SELECT * FROM support_tickets WHERE id = ?", [$id]);
        $ticket = $stmt->fetch();

        // Get Messages
        $stmt = $db->query("SELECT m.*, u.email FROM support_messages m JOIN users u ON m.user_id = u.id WHERE m.ticket_id = ? ORDER BY m.created_at ASC", [$id]);
        $messages = $stmt->fetchAll();

        require __DIR__ . '/../Views/show.php';
    }

    public function reply()
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $ticketId = $_POST['ticket_id'];
        $message = $_POST['message'];

        $stmt = $db->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 0)");
        $stmt->execute([$ticketId, $userId, $message]);

        header("Location: /support/view/" . $ticketId);
    }
}

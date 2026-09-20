<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;

class AdminSupportController
{
    private $db;

    public function __construct()
    {
        // Ensure Admin or Moderator
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!Auth::isSuperAdmin() && !Auth::isModerator()) {
            Auth::denySuperAdminAccess('Customer Support Admin');
        }

        $this->db = Database::getInstance();
    }

    public function index()
    {
        // Get all tickets from all tenants
        $stmt = $this->db->query("
            SELECT t.*, u.name as user_name, u.email as user_email, tn.domain as tenant_domain
            FROM support_tickets t
            JOIN users u ON t.user_id = u.id
            JOIN tenants tn ON t.tenant_id = tn.id
            ORDER BY t.created_at DESC
        ");
        $tickets = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/admin/support_tickets.php';
    }
    
    public function migrate()
    {
        require __DIR__ . '/../Views/admin/migrate_support.php';
    }
    
    public function view($params)
    {
        $ticketId = $params['id'] ?? 0;
        
        // Get ticket details
        $stmt = $this->db->query("
            SELECT t.*, u.name as user_name, u.email as user_email, tn.domain as tenant_domain
            FROM support_tickets t
            JOIN users u ON t.user_id = u.id
            JOIN tenants tn ON t.tenant_id = tn.id
            WHERE t.id = ?
        ", [$ticketId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            die("Ticket not found");
        }
        
        // Get messages
        $stmt = $this->db->query("
            SELECT m.*, u.name as author_name, u.email as author_email
            FROM support_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.ticket_id = ?
            ORDER BY m.created_at ASC
        ", [$ticketId]);
        $messages = $stmt->fetchAll();
        
        // Get attachments
        $stmt = $this->db->query("
            SELECT a.*, c.name as file_name, c.path as file_url
            FROM support_attachments a
            JOIN cloud_files c ON a.file_id = c.id
            WHERE a.ticket_id = ?
        ", [$ticketId]);
        $attachments = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/admin/support_view.php';
    }
    
    public function reply()
    {
        $ticketId = $_POST['ticket_id'];
        $reply = $_POST['reply'];
        $userId = Auth::user()['id'];
        
        // Add admin reply
        $this->db->query("INSERT INTO support_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 1)", 
            [$ticketId, $userId, $reply]);
        
        // Get ticket and user info for email
        $stmt = $this->db->query("
            SELECT t.subject, u.name, u.email
            FROM support_tickets t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = ?
        ", [$ticketId]);
        $ticketInfo = $stmt->fetch();
        
        // Send email to user
        try {
            $renderedEmail = \App\Core\EmailTemplate::render('support_ticket_reply', [
                'ticketId' => $ticketId,
                'subject' => $ticketInfo['subject'],
                'adminReply' => $reply,
                'userName' => $ticketInfo['name']
            ]);

            \App\Core\Mailer::send(
                $ticketInfo['email'],
                "Reply to Your Support Ticket #$ticketId",
                $renderedEmail,
                true // isRaw
            );
        } catch (\Exception $e) {
            error_log("Failed to send reply notification: " . $e->getMessage());
        }
        
        header("Location: /casper-joe/support/$ticketId");
    }
    
    public function updateStatus()
    {
        $ticketId = $_POST['ticket_id'];
        $status = $_POST['status'];
        
        $this->db->query("UPDATE support_tickets SET status = ? WHERE id = ?", [$status, $ticketId]);
        
        header("Location: /casper-joe/support/$ticketId");
    }
}

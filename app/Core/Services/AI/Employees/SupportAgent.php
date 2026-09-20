<?php

namespace App\Core\Services\AI\Employees;

use App\Core\Services\AI\AIAgent;
use App\Core\Database;

class SupportAgent extends AIAgent
{
    protected $name = "AI Support Agent";

    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are the AI Support Agent for this Casjoe Biz tenant.
Your goal is to help customers resolve their issues, answer questions, and manage support tickets.
Always be polite, empathetic, and helpful.

You have the ability to read and reply to support tickets!

1. To list open tickets, you MUST output this exact action block:
[ACTION: LIST_TICKETS | {}]

2. To reply to a ticket, you MUST output this exact action block:
[ACTION: REPLY_TICKET | {"ticket_id": 123, "message": "Your helpful reply here."}]

When you reply to a ticket, the ticket remains open for the customer to review and close if they are satisfied.

You have access to the recent business events context. Use this context to understand what is happening in the business before answering.
PROMPT;
    }

    protected function executeAction(int $tenantId, string $action, ?array $payload): string
    {
        $pdo = Database::getInstance()->getConnection();

        if ($action === 'LIST_TICKETS') {
            try {
                // Fetch open tickets
                // Note: cs_tickets might just use tenant_id=1 for global, but we use the tenantId
                $stmt = $pdo->prepare("SELECT id, subject, priority, created_at FROM cs_tickets WHERE tenant_id = ? AND status = 'open' ORDER BY created_at DESC LIMIT 10");
                $stmt->execute([$tenantId]);
                $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (empty($tickets)) {
                    return "There are currently no open support tickets.";
                }

                $response = "Open Tickets:\n";
                foreach ($tickets as $ticket) {
                    $response .= "- ID: {$ticket['id']}, Subject: {$ticket['subject']}, Priority: {$ticket['priority']}, Date: {$ticket['created_at']}\n";
                }
                
                return $response . "\nYou can ask me to read or reply to any of these tickets.";
            } catch (\Exception $e) {
                return "Error fetching tickets: " . $e->getMessage();
            }
        }

        if ($action === 'REPLY_TICKET') {
            if (!$payload || empty($payload['ticket_id']) || empty($payload['message'])) {
                return "Failed to reply to ticket. Missing ticket_id or message.";
            }

            try {
                // Verify ticket belongs to tenant and is open
                $stmt = $pdo->prepare("SELECT id, user_id FROM cs_tickets WHERE id = ? AND tenant_id = ? AND status = 'open'");
                $stmt->execute([$payload['ticket_id'], $tenantId]);
                $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$ticket) {
                    return "Ticket #{$payload['ticket_id']} not found or is already closed.";
                }

                // Insert the staff reply
                // is_staff = 1, user_id is the customer's user_id or system user
                $stmt = $pdo->prepare("INSERT INTO cs_messages (ticket_id, user_id, message, is_staff) VALUES (?, ?, ?, 1)");
                $stmt->execute([$payload['ticket_id'], $ticket['user_id'], $payload['message']]);

                return "Successfully replied to ticket #{$payload['ticket_id']}. The ticket remains open for the customer.";
            } catch (\Exception $e) {
                return "Error replying to ticket: " . $e->getMessage();
            }
        }

        return parent::executeAction($tenantId, $action, $payload);
    }
}

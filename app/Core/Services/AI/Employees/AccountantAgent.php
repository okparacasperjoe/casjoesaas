<?php

namespace App\Core\Services\AI\Employees;

use App\Core\Services\AI\AIAgent;
use App\Core\Database;

class AccountantAgent extends AIAgent
{
    protected $name = "AI Accountant";

    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are the AI Accountant and Financial Controller for this Casjoe Biz tenant.
Your goal is to monitor cash flow, flag anomalies in expenses, and help with financial planning.

You have the ability to generate and send invoices!
If the user asks you to create or send an invoice, you MUST output this exact action block in your response:
[ACTION: CREATE_INVOICE | {"client_email": "email@example.com", "amount": 100.00, "description": "Services"}]

If the user asks for a financial summary, you can read the context and summarize the health of the business.
You have access to the recent business events context. Use this context to understand what is happening in the business before answering.

If the user asks about revenue or recent expenses, analyze the context provided and give a professional, accurate financial breakdown.
PROMPT;
    }

    protected function executeAction(int $tenantId, string $action, ?array $payload): string
    {
        if ($action === 'CREATE_INVOICE') {
            if (!$payload || empty($payload['client_email']) || empty($payload['amount'])) {
                return "Failed to create invoice. Missing email or amount.";
            }

            try {
                $pdo = Database::getInstance()->getConnection();
                $token = bin2hex(random_bytes(32));
                
                $stmt = $pdo->prepare("INSERT INTO erp_invoices (tenant_id, client_email, amount, description, token, due_date) VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 14 DAY))");
                $stmt->execute([
                    $tenantId, 
                    $payload['client_email'], 
                    $payload['amount'], 
                    $payload['description'] ?? 'Invoice from Casjoe Biz',
                    $token
                ]);

                // Generate a public link for the invoice
                $invoiceLink = "https://app.casjoe.com/invoice/view/" . $token;

                // Send Email via Mailer
                $mailer = new \App\Core\Services\Mailer();
                $subject = "You have received a new invoice";
                $htmlBody = "<h2>New Invoice</h2><p>You have a new invoice for $" . number_format($payload['amount'], 2) . ".</p><p>" . htmlspecialchars($payload['description'] ?? '') . "</p><p><a href=\"{$invoiceLink}\" style=\"background:#FFA600; color:#000; padding:10px 20px; text-decoration:none; border-radius:5px;\">View & Pay Invoice</a></p>";
                $mailer->sendEmail($payload['client_email'], $subject, $htmlBody);

                return "Invoice created successfully for {$payload['client_email']} for $" . number_format($payload['amount'], 2) . ". The email has been dispatched with a secure link.";
            } catch (\Exception $e) {
                return "Error creating invoice: " . $e->getMessage();
            }
        }

        return parent::executeAction($tenantId, $action, $payload);
    }
}

<?php
/**
 * AI Sales Manager Sequence Engine (Cron Job)
 * Recommended Schedule: Run once every hour (0 * * * *)
 */

require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Mailer;
use App\Core\Services\WhatsAppService;
use App\Core\Services\AIService;

if (php_sapi_name() !== 'cli') {
    $token = $_GET['token'] ?? '';
    if ($token !== 'casjoe_ai_sales_key_9911') {
        die("Unauthorized access.");
    }
}

echo "--- AI Sales Manager Sequence Engine ---\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";

try {
    $db = Database::getInstance()->getConnection();

    // 1. Auto-Enrollment: Find unpaid invoices that don't have a sequence yet
    // Skip draft or cancelled invoices. Only enroll 'sent' or 'unpaid'
    $stmtEnroll = $db->query("
        INSERT IGNORE INTO erp_ai_sales_sequences (tenant_id, invoice_id, customer_id, step, status, next_run_at)
        SELECT i.tenant_id, i.id, i.customer_id, 1, 'active', DATE_ADD(i.issue_date, INTERVAL 1 DAY)
        FROM erp_invoices i
        WHERE i.status IN ('unpaid', 'sent', 'overdue')
        AND NOT EXISTS (
            SELECT 1 FROM erp_ai_sales_sequences s WHERE s.invoice_id = i.id
        )
    ");
    echo "Auto-enrolled unpaid invoices: " . $stmtEnroll->rowCount() . "\n";

    // 2. Fetch pending sequences
    $stmt = $db->prepare("
        SELECT s.*, i.status as invoice_status, i.total_amount, i.client_email, i.client_name, i.due_date, c.phone
        FROM erp_ai_sales_sequences s
        JOIN erp_invoices i ON s.invoice_id = i.id
        LEFT JOIN erp_customers c ON i.customer_id = c.id
        WHERE s.status = 'active' AND s.next_run_at <= NOW()
        LIMIT 50
    ");
    $stmt->execute();
    $sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($sequences) . " sequence(s) to process.\n";

    $waService = new WhatsAppService();

    foreach ($sequences as $seq) {
        echo "Processing sequence ID {$seq['id']} for Invoice #{$seq['invoice_id']}...\n";

        // Check if invoice was paid
        if (in_array(strtolower($seq['invoice_status']), ['paid', 'partially_paid', 'cancelled'])) {
            $db->prepare("UPDATE erp_ai_sales_sequences SET status = 'completed' WHERE id = ?")->execute([$seq['id']]);
            echo " -> Invoice is paid or cancelled. Sequence marked as completed.\n";
            continue;
        }

        // Generate AI Content
        try {
            $aiService = new AIService($seq['tenant_id']);
            
            // Email generation
            $emailPrompt = "You are the automated Sales Manager for our company. Write a polite, professional, and concise follow-up email to a customer named {$seq['client_name']}. They have an unpaid invoice #{$seq['invoice_id']} for the amount of {$seq['total_amount']}, which was due on {$seq['due_date']}. This is follow-up #{$seq['step']}. Do not include subject line in body, just the email body.";
            $emailBody = $aiService->generateText($emailPrompt);

            // WhatsApp generation
            $waPrompt = "You are the automated Sales Manager for our company. Write a very short, friendly WhatsApp message (under 30 words) to a customer named {$seq['client_name']} reminding them about unpaid invoice #{$seq['invoice_id']} of {$seq['total_amount']}. This is follow-up #{$seq['step']}.";
            $waBody = $aiService->generateText($waPrompt);

            // Send Email
            if (!empty($seq['client_email'])) {
                $subject = "Follow up: Invoice #{$seq['invoice_id']} - Action Required";
                Mailer::send($seq['client_email'], $subject, nl2br($emailBody));
                echo " -> Email sent to {$seq['client_email']}.\n";
            }

            // Send WhatsApp
            if (!empty($seq['phone'])) {
                try {
                    $waService->sendMessage($seq['phone'], $waBody);
                    echo " -> WhatsApp sent to {$seq['phone']}.\n";
                } catch (Exception $e) {
                    echo " -> WhatsApp failed: " . $e->getMessage() . "\n";
                }
            }

            // Update sequence
            $nextRunAt = date('Y-m-d H:i:s', strtotime('+2 days'));
            $db->prepare("
                UPDATE erp_ai_sales_sequences 
                SET step = step + 1, last_run_at = NOW(), next_run_at = ?
                WHERE id = ?
            ")->execute([$nextRunAt, $seq['id']]);

        } catch (Exception $e) {
            echo " -> Error processing sequence: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}

echo "Finished processing.\n";

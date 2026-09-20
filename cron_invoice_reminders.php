<?php
require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Mailer;

/**
 * Casjoe Invoice Reminders CRON Job
 * Recommended Schedule: Run once per day at a specific time (e.g. 8:00 AM)
 */

if (php_sapi_name() !== 'cli') {
    // Basic security for web access
    $token = $_GET['token'] ?? '';
    if ($token !== 'casjoe_invoice_key_9912') {
        die("Unauthorized access.");
    }
}

echo "--- Casjoe Invoice Reminders Cron --- \n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all active reminders joined with unpaid/sent/overdue invoices
    // An invoice is eligible for a reminder if:
    // 1. the invoice is unpaid (status NOT 'paid', 'cancelled', 'draft')
    // 2. CURDATE() >= invoice.due_date + reminder.days_offset
    // 3. The reminder hasn't been sent yet for this invoice
    
    $sql = "
        SELECT 
            i.id as invoice_id, 
            i.uuid,
            i.client_name, 
            i.client_email, 
            i.total_amount, 
            i.due_date,
            r.id as reminder_id, 
            r.subject, 
            r.body,
            r.frequency_days,
            t.name as company_name,
            t.domain,
            (SELECT MAX(sent_at) FROM erp_invoice_reminder_logs 
             WHERE invoice_id = i.id AND reminder_id = r.id) as last_sent_at
        FROM erp_invoices i
        JOIN erp_invoice_reminders r ON i.tenant_id = r.tenant_id
        JOIN tenants t ON i.tenant_id = t.id
        WHERE i.status IN ('sent', 'overdue') 
          AND r.is_active = 1
          AND CURDATE() >= DATE_ADD(i.due_date, INTERVAL r.days_offset DAY)
    ";

    $stmt = $db->query($sql);
    $remindersToSendRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $remindersToSend = [];
    $now = new DateTime();
    
    foreach ($remindersToSendRaw as $item) {
        $shouldSend = false;
        
        if (empty($item['last_sent_at'])) {
            $shouldSend = true;
        } else {
            if ($item['frequency_days'] > 0) {
                $lastSentDate = new DateTime($item['last_sent_at']);
                $diff = $now->diff($lastSentDate)->days;
                if ($diff >= $item['frequency_days']) {
                    $shouldSend = true;
                }
            }
        }
        
        if ($shouldSend) {
            $remindersToSend[] = $item;
        }
    }

    $count = 0;
    foreach ($remindersToSend as $item) {
        // Handle URL generation for CLI (as cron jobs run in CLI)
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $protocol = $isSecure ? "https://" : "http://";
        
        // If domain is explicitly set in tenant record, use it. 
        // Otherwise, if in CLI, use the known production domain 'app.casjoe.com'
        $host = '';
        if (!empty($item['domain'])) {
            $host = $item['domain'];
        } elseif (php_sapi_name() === 'cli') {
            $host = 'app.casjoe.com';
            $protocol = "https://"; // Force HTTPS for production CLI
        } else {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        }

        $invoiceUrl = $protocol . $host . "/invoice/" . $item['uuid'];
        
        // Parse template
        $body = $item['body'];
        $body = str_replace('{client_name}', htmlspecialchars($item['client_name']), $body);
        $body = str_replace('{invoice_amount}', number_format($item['total_amount'], 2), $body);
        $body = str_replace('{invoice_link}', '<a href="'.$invoiceUrl.'">' . $invoiceUrl . '</a>', $body);
        $body = str_replace('{due_date}', $item['due_date'], $body);

        $subject = str_replace('{client_name}', htmlspecialchars($item['client_name']), $item['subject']);

        echo "[" . date('H:i:s') . "] Sending reminder '{$item['subject']}' to {$item['client_email']} for Invoice ID {$item['invoice_id']}...\n";
        
        // Convert newlines to breaks for HTML email
        $htmlBody = nl2br($body);
        
        $sent = Mailer::send($item['client_email'], $subject, $htmlBody);
        
        if ($sent) {
            $logStmt = $db->prepare("INSERT INTO erp_invoice_reminder_logs (invoice_id, reminder_id) VALUES (?, ?)");
            $logStmt->execute([$item['invoice_id'], $item['reminder_id']]);
            $count++;
            echo " -> SUCCESS: Sent.\n";
        } else {
            echo " -> FAILED: Mailer could not send email.\n";
        }
    }
    
    echo "\n--- Cron Summary ---\n";
    echo "Total Reminders Processed: " . count($remindersToSend) . "\n";
    echo "Successfully Sent: $count\n";
    echo "--------------------\n";
} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}

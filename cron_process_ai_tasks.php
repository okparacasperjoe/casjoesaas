<?php
require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Mailer;

/**
 * CORI AI Scheduled Tasks Processor (Cron Job)
 * Recommended Schedule: Run once every minute or 5 minutes (e.g. * * * * *)
 */

if (php_sapi_name() !== 'cli') {
    // Basic security for web access
    $token = $_GET['token'] ?? '';
    if ($token !== 'casjoe_ai_task_key_8821') {
        die("Unauthorized access.");
    }
}

echo "--- CORI AI Scheduled Tasks Processor ---\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";

try {
    $db = Database::getInstance()->getConnection();

    // Select tasks that are pending and ready to run
    $stmt = $db->prepare("
        SELECT * FROM ai_scheduled_tasks
        WHERE status = 'pending' AND next_run_at <= NOW()
        LIMIT 20
    ");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($tasks) . " pending task(s) to process.\n";

    foreach ($tasks as $task) {
        echo "Processing task ID {$task['id']} (Type: {$task['task_type']}, Label: '{$task['task_label']}') for Tenant ID {$task['tenant_id']}...\n";

        // Mark task as 'active' (in progress) to prevent double-execution
        $db->prepare("UPDATE ai_scheduled_tasks SET status = 'active' WHERE id = ?")->execute([$task['id']]);

        try {
            $payload = json_decode($task['payload'], true);
            if (!$payload) {
                throw new Exception("Invalid JSON payload.");
            }

            if ($task['task_type'] === 'invoice_reminder') {
                // Wait, the payload from ChatController has:
                // recipient_name, recipient_email, amount, description, etc.
                $recipientName = $payload['recipient_name'] ?? 'Valued Customer';
                $recipientEmail = $payload['recipient_email'] ?? '';
                $amount = floatval($payload['amount'] ?? 0);
                $description = $payload['description'] ?? 'Product/Service Delivery';

                if ($amount <= 0) {
                    throw new Exception("Amount must be greater than zero.");
                }

                $invoiceUuid = bin2hex(random_bytes(16));
                $issueDate = date('Y-m-d');
                $dueDate = date('Y-m-d', strtotime('+7 days'));

                $db->beginTransaction();

                // 1. Insert into erp_invoices
                $stmtInv = $db->prepare("
                    INSERT INTO erp_invoices 
                    (tenant_id, uuid, client_name, client_email, issue_date, due_date, total_amount, notes, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'sent')
                ");
                $stmtInv->execute([
                    $task['tenant_id'],
                    $invoiceUuid,
                    $recipientName,
                    $recipientEmail,
                    $issueDate,
                    $dueDate,
                    $amount,
                    "Generated via AI: " . $description,
                    'sent'
                ]);
                $invoiceId = $db->lastInsertId();

                // 2. Insert into erp_invoice_items
                $stmtItem = $db->prepare("
                    INSERT INTO erp_invoice_items 
                    (invoice_id, description, quantity, unit_price, amount) 
                    VALUES (?, ?, 1, ?, ?)
                ");
                $stmtItem->execute([
                    $invoiceId,
                    $description,
                    $amount,
                    $amount
                ]);

                $db->commit();
                echo " -> Created Invoice ID #{$invoiceId} in DB.\n";

                // 3. Dispatch Email if email address is provided
                if (!empty($recipientEmail)) {
                    // Fetch Tenant name and domain for the link
                    $stmtTenant = $db->prepare("SELECT name, domain FROM tenants WHERE id = ?");
                    $stmtTenant->execute([$task['tenant_id']]);
                    $tenant = $stmtTenant->fetch(PDO::FETCH_ASSOC);

                    $companyName = $tenant['name'] ?? 'Our Company';
                    $host = !empty($tenant['domain']) ? $tenant['domain'] : 'app.casjoe.com';
                    $publicUrl = "https://" . $host . "/invoice/" . $invoiceUuid;

                    $subject = "Invoice #" . str_pad($invoiceId, 5, '0', STR_PAD_LEFT) . " from " . $companyName;
                    $message = "
                        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 8px;'>
                            <h2 style='color: #000066;'>New Invoice from " . htmlspecialchars($companyName) . "</h2>
                            <p>Hello " . htmlspecialchars($recipientName) . ",</p>
                            <p>You have received a new invoice. Please see the details below and use the link to view or make a payment.</p>
                            
                            <div style='background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                                <p><strong>Invoice ID:</strong> #" . str_pad($invoiceId, 5, '0', STR_PAD_LEFT) . "</p>
                                <p><strong>Amount Due:</strong> NGN " . number_format($amount, 2) . "</p>
                                <p><strong>Due Date:</strong> " . $dueDate . "</p>
                                <p><strong>Description:</strong> " . htmlspecialchars($description) . "</p>
                            </div>
            
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='$publicUrl' style='background: #000066; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>View & Pay Invoice</a>
                            </div>
            
                            <p style='color: #666; font-size: 0.9em;'>If you have any questions, please reply to this email.</p>
                            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                            <p style='color: #999; font-size: 0.8em; text-align: center;'>Powered by Casjoe - Business, Connected.</p>
                        </div>
                    ";

                    if (Mailer::send($recipientEmail, $subject, $message)) {
                        echo " -> Sent invoice email to {$recipientEmail}.\n";
                    } else {
                        echo " -> Warning: Mailer could not send email to {$recipientEmail}.\n";
                    }
                } else {
                    echo " -> No recipient email provided. Invoice generated, but not sent.\n";
                }
            } else {
                // Custom or unsupported task type
                throw new Exception("Unsupported task type: {$task['task_type']}");
            }

            // Calculate next run time and update status
            $nextRunAt = null;
            $nextStatus = 'completed';

            if ($task['interval_type'] === 'daily') {
                $nextRunAt = date('Y-m-d H:i:s', strtotime('+1 day'));
                $nextStatus = 'pending';
            } elseif ($task['interval_type'] === 'weekly') {
                $nextRunAt = date('Y-m-d H:i:s', strtotime('+7 days'));
                $nextStatus = 'pending';
            } elseif ($task['interval_type'] === 'every_x_days' && $task['interval_days'] > 0) {
                $nextRunAt = date('Y-m-d H:i:s', strtotime('+' . (int)$task['interval_days'] . ' days'));
                $nextStatus = 'pending';
            }

            if ($nextStatus === 'pending' && $nextRunAt) {
                $stmtUpdate = $db->prepare("
                    UPDATE ai_scheduled_tasks
                    SET status = 'pending', next_run_at = ?, last_run_at = NOW()
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$nextRunAt, $task['id']]);
                echo " -> Scheduled next run for {$nextRunAt}.\n";
            } else {
                $stmtUpdate = $db->prepare("
                    UPDATE ai_scheduled_tasks
                    SET status = 'completed', last_run_at = NOW()
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$task['id']]);
                echo " -> Completed successfully.\n";
            }

        } catch (Exception $taskEx) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            echo " -> FAILED: " . $taskEx->getMessage() . "\n";
            $db->prepare("UPDATE ai_scheduled_tasks SET status = 'failed' WHERE id = ?")->execute([$task['id']]);
        }
    }

    echo "Finished processing.\n";

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}

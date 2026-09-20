<?php
/**
 * Sequence Processor Cron Job
 * Run this script every hour via cron or task scheduler
 * Example cron: 0 * * * * php /path/to/process_sequences.php
 */

require __DIR__ . '/../../../Core/Database.php';
require __DIR__ . '/../../../Core/Mailer.php';

use App\Core\Database;
use App\Core\Mailer;

$db = Database::getInstance()->getConnection();

echo "========================================\n";
echo "  Sequence Processor - " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

try {
    // Find all enrollments ready to send (next_send_at <= NOW and status = 'active')
    $stmt = $db->prepare("
        SELECT e.*, s.tenant_id, s.name as sequence_name,
               sub.email, sub.first_name, sub.last_name,
               step.subject, step.content, step.stop_on_reply, step.stop_on_click,
               step.step_order
        FROM cm_sequence_enrollments e
        JOIN cm_sequences s ON e.sequence_id = s.id
        JOIN cm_subscribers sub ON e.subscriber_id = sub.id
        JOIN cm_sequence_steps step ON e.sequence_id = step.sequence_id 
            AND step.step_order = e.current_step + 1
        WHERE e.status = 'active'
          AND e.next_send_at <= NOW()
          AND s.is_active = 1
        ORDER BY e.next_send_at ASC
        LIMIT 100
    ");
    $stmt->execute();
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($enrollments)) {
        echo "✓ No sequences ready to process\n\n";
        exit(0);
    }

    echo "Found " . count($enrollments) . " enrollments to process\n\n";

    $sent = 0;
    $failed = 0;
    $stopped = 0;

    foreach ($enrollments as $enrollment) {
        echo "Processing: {$enrollment['email']} - {$enrollment['sequence_name']} - Step {$enrollment['step_order']}\n";

        // Prepare email
        $subject = str_replace(
            ['{first_name}', '{last_name}', '{email}'],
            [$enrollment['first_name'], $enrollment['last_name'], $enrollment['email']],
            $enrollment['subject']
        );
        
        $body = str_replace(
            ['{first_name}', '{last_name}', '{email}'],
            [$enrollment['first_name'], $enrollment['last_name'], $enrollment['email']],
            $enrollment['content']
        );

        // Send email
        try {
            $mailer = new Mailer($enrollment['tenant_id']);
            $result = $mailer->send($enrollment['email'], $subject, $body);

            if ($result) {
                echo "  ✓ Sent successfully\n";
                
                // Check if there's a next step
                $stmt = $db->prepare("
                    SELECT * FROM cm_sequence_steps 
                    WHERE sequence_id = ? AND step_order = ?
                ");
                $stmt->execute([$enrollment['sequence_id'], $enrollment['step_order'] + 1]);
                $nextStep = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($nextStep) {
                    // Calculate next send time
                    $delaySeconds = ($nextStep['delay_days'] * 86400) + ($nextStep['delay_hours'] * 3600);
                    $nextSendAt = date('Y-m-d H:i:s', time() + $delaySeconds);

                    // Update enrollment to next step
                    $stmt = $db->prepare("
                        UPDATE cm_sequence_enrollments 
                        SET current_step = current_step + 1,
                            last_sent_at = NOW(),
                            next_send_at = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$nextSendAt, $enrollment['id']]);
                    echo "  → Next step scheduled for: $nextSendAt\n";
                } else {
                    // Sequence complete
                    $stmt = $db->prepare("
                        UPDATE cm_sequence_enrollments 
                        SET status = 'completed',
                            current_step = current_step + 1,
                            last_sent_at = NOW(),
                            next_send_at = NULL
                        WHERE id = ?
                    ");
                    $stmt->execute([$enrollment['id']]);
                    echo "  ✓ Sequence completed\n";
                }

                $sent++;
            } else {
                echo "  ✗ Failed to send\n";
                $failed++;
            }
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            $failed++;
        }

        echo "\n";
    }

    echo "========================================\n";
    echo "Summary:\n";
    echo "  Sent: $sent\n";
    echo "  Failed: $failed\n";
    echo "  Stopped: $stopped\n";
    echo "========================================\n\n";

} catch (Exception $e) {
    echo "✗ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}

<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;
use App\Core\Mailer;

class MailTrigger
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Send funnel email based on event
     * 
     * @param string $event Event type (view, form_submit, payment_success, complete)
     * @param int $funnelId Funnel ID
     * @param string $recipientEmail Recipient email
     * @param array $data Additional data for email template
     */
    public function sendFunnelEmail($event, $funnelId, $recipientEmail, $data = [])
    {
        // Get funnel email trigger configuration
        $stmt = $this->db->query(
            "SELECT * FROM funnel_mail_triggers WHERE funnel_id = ? AND event_type = ? AND enabled = 1",
            [$funnelId, $event]
        );
        $trigger = $stmt->fetch();
        
        if (!$trigger) {
            return false; // No email configured for this event
        }
        
        // Get funnel details
        $stmt = $this->db->query("SELECT name, type FROM sales_funnels WHERE id = ?", [$funnelId]);
        $funnel = $stmt->fetch();
        
        // Prepare email content
        $subject = $this->replacePlaceholders($trigger['subject'], array_merge($data, [
            'funnel_name' => $funnel['name'],
            'funnel_type' => $funnel['type']
        ]));
        
        $body = $this->replacePlaceholders($trigger['body'], array_merge($data, [
            'funnel_name' => $funnel['name'],
            'funnel_type' => $funnel['type']
        ]));
        
        // Add delay if configured
        $delayMinutes = $trigger['delay_minutes'] ?? 0;
        if ($delayMinutes > 0) {
            // For now, send immediately. In production, queue this.
            // You could use a job queue or scheduled task
        }
        
        // Send email
        try {
            Mailer::send($recipientEmail, $subject, $body);
            
            // Log sent email
            $this->logEmailSent($funnelId, $recipientEmail, $event, $subject);
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to send funnel email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email when viewing funnel
     */
    public function sendWelcomeEmail($funnelId, $recipientEmail, $recipientName = '')
    {
        return $this->sendFunnelEmail('view', $funnelId, $recipientEmail, [
            'recipient_name' => $recipientName
        ]);
    }

    /**
     * Send form submission confirmation
     */
    public function sendFormConfirmation($funnelId, $recipientEmail, $formData = [])
    {
        return $this->sendFunnelEmail('form_submit', $funnelId, $recipientEmail, [
            'recipient_name' => $formData['name'] ?? 'Valued Customer',
            'form_data' => json_encode($formData)
        ]);
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation($funnelId, $recipientEmail, $amount, $transactionRef)
    {
        return $this->sendFunnelEmail('payment_success', $funnelId, $recipientEmail, [
            'amount' => number_format($amount, 2),
            'transaction_ref' => $transactionRef
        ]);
    }

    /**
     * Send funnel completion email
     */
    public function sendCompletionEmail($funnelId, $recipientEmail, $recipientName = '')
    {
        return $this->sendFunnelEmail('complete', $funnelId, $recipientEmail, [
            'recipient_name' => $recipientName
        ]);
    }

    /**
     * Replace placeholders in email template
     */
    private function replacePlaceholders($text, $data)
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        
        // Default placeholders
        $text = str_replace('{{site_name}}', 'Casjoe', $text);
        $text = str_replace('{{current_year}}', date('Y'), $text);
        
        return $text;
    }

    /**
     * Log sent email
     */
    private function logEmailSent($funnelId, $recipient, $event, $subject)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO funnel_email_log (funnel_id, recipient, event_type, subject, sent_at) 
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$funnelId, $recipient, $event, $subject]);
        } catch (\Exception $e) {
            error_log("Failed to log email: " . $e->getMessage());
        }
    }

    /**
     * Get email triggers for a funnel
     */
    public function getTriggers($funnelId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM funnel_mail_triggers WHERE funnel_id = ? ORDER BY event_type ASC",
            [$funnelId]
        );
        return $stmt->fetchAll();
    }

    /**
     * Save email trigger
     */
    public function saveTrigger($funnelId, $eventType, $subject, $body, $enabled = true, $delayMinutes = 0)
    {
        // Check if trigger exists
        $stmt = $this->db->query(
            "SELECT id FROM funnel_mail_triggers WHERE funnel_id = ? AND event_type = ?",
            [$funnelId, $eventType]
        );
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing
            $stmt = $this->db->prepare(
                "UPDATE funnel_mail_triggers 
                 SET subject = ?, body = ?, enabled = ?, delay_minutes = ? 
                 WHERE id = ?"
            );
            $stmt->execute([$subject, $body, $enabled ? 1 : 0, $delayMinutes, $existing['id']]);
        } else {
            // Create new
            $stmt = $this->db->prepare(
                "INSERT INTO funnel_mail_triggers 
                (funnel_id, event_type, subject, body, enabled, delay_minutes) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$funnelId, $eventType, $subject, $body, $enabled ? 1 : 0, $delayMinutes]);
        }
        
        return true;
    }
}

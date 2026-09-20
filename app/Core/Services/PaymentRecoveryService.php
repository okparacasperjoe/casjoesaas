<?php

namespace App\Core\Services;

use App\Core\Database;
use App\Core\Mailer;

class PaymentRecoveryService
{
    private $pdo;
    private $mailer;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->mailer = new Mailer();
    }

    public function processAbandonedPayments() {
        echo "Checking for abandoned payments...\n";

        // Criteria: 'pending' status, created > 30 minutes ago, created < 24 hours ago, reminder not sent
        $sql = "SELECT t.*, u.name as vendor_name, u.email as vendor_email 
                FROM cp_transactions t
                JOIN users u ON t.user_id = u.id
                WHERE t.status = 'pending' 
                AND t.payer_email IS NOT NULL 
                AND t.reminder_sent = 0 
                AND t.created_at < (NOW() - INTERVAL 30 MINUTE)
                AND t.created_at > (NOW() - INTERVAL 24 HOUR)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $abandoned = $stmt->fetchAll();

        echo "Found " . count($abandoned) . " abandoned transactions.\n";

        foreach ($abandoned as $txn) {
            $this->sendRecoveryEmail($txn);
        }
    }

    private function sendRecoveryEmail($txn) {
        $email = $txn['payer_email'];
        $vendorName = $txn['vendor_name'] ?? 'The Merchant';
        $amount = number_format($txn['amount'], 2);
        $currency = $txn['currency'];
        
        // Construct a generic recovery link (assuming the user can restart the process)
        // Ideally, we'd have the original slug. If we stored it in 'description' or 'meta', we could be more precise.
        // For now, we'll suggest they visit the merchant's page or contact support if we can't reconstruct the link.
        // Wait, LinkController stores description as "Payment initialization for [Title]". 
        
        $subject = "Complete your payment to $vendorName";
        $body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2>Hello,</h2>
                <p>We noticed you started a payment of <strong>$currency $amount</strong> to <strong>$vendorName</strong> but didn't complete it.</p>
                <p>If you experienced an issue or simply forgot, please click the link below to try again:</p>
                <p>
                    <a href='https://app.casjoe.com/pay/links' style='display: inline-block; padding: 10px 20px; background-color: #000066; color: #ffffff; text-decoration: none; border-radius: 5px;'>Return to Payment</a>
                </p>
                <p><small>(If you have already completed this payment, please ignore this message.)</small></p>
                <br>
                <p>Regards,<br>Casjoe Pay Team</p>
            </div>
        ";

        try {
            // Send Email
            $sent = $this->mailer->send($email, $subject, $body);

            if ($sent) {
                // Mark as sent
                $update = $this->pdo->prepare("UPDATE cp_transactions SET reminder_sent = 1 WHERE id = ?");
                $update->execute([$txn['id']]);
                echo "Reminder sent to $email for txn #{$txn['id']}\n";
            } else {
                echo "Failed to send email to $email\n";
            }

        } catch (\Exception $e) {
            echo "Error processing txn #{$txn['id']}: " . $e->getMessage() . "\n";
        }
    }
}

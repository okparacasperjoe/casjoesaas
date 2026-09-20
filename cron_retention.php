<?php
require_once __DIR__ . '/app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Mailer;

// This script should be run daily via Cron.

try {
    $db = Database::getInstance()->getConnection();
    $mailer = new Mailer();

    echo "Running Inactivity Retention Cron...\n";

    // Find users who:
    // 1. Have last_login older than 7 days
    // 2. Have not received a retention email yet (retention_email_sent IS NULL)
    // 3. Are not deleted/inactive (assuming standard active logic)
    
    $stmt = $db->query("
        SELECT id, name, email, tenant_id 
        FROM users 
        WHERE last_login IS NOT NULL 
          AND last_login <= DATE_SUB(NOW(), INTERVAL 7 DAY)
          AND retention_email_sent IS NULL
    ");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "No users found requiring retention emails today.\n";
        exit;
    }

    echo "Found " . count($users) . " inactive users. Sending emails...\n";

    foreach ($users as $user) {
        $name = $user['name'] ?: 'there';
        $email = $user['email'];

        $subject = "We miss you at Casjoe! Let us help you get set up for free";
        
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333; line-height: 1.6; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h2 style='color: #2563eb; margin: 0; font-size: 24px;'>We're here to help you succeed.</h2>
            </div>
            <p>Hi {$name},</p>
            <p>We noticed you haven't logged in for a few days. We know that getting a new platform set up can feel a bit daunting, but you are not alone!</p>
            <p>Did you know we offer completely <strong>FREE onboarding assistance</strong>?</p>
            <p>Our dedicated team can help you:</p>
            <ul style='background: #f8fafc; padding: 20px 40px; border-radius: 8px; list-style-type: none;'>
                <li style='margin-bottom: 10px;'>✔️ Set up your account and company profile</li>
                <li style='margin-bottom: 10px;'>✔️ Onboard your staff and team members</li>
                <li>✔️ Configure your modules and import data</li>
            </ul>
            <p>You don't have to do it all yourself. Just reply directly to this email or reach out to our support team, and our onboarding specialists will take care of the heavy lifting so you can focus on growing your business.</p>
            <div style='text-align: center; margin: 40px 0;'>
                <a href='https://app.casjoe.com/login' style='display: inline-block; background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;'>Log In to Your Account</a>
            </div>
            <p style='margin-bottom: 5px;'>Best regards,</p>
            <p style='margin-top: 0;'><strong>The Casjoe Team</strong></p>
        </div>
        ";

        // Send the email
        $sent = $mailer->send($email, $subject, $body);

        if ($sent) {
            echo "Sent retention email to: $email\n";
            // Mark as sent
            $update = $db->prepare("UPDATE users SET retention_email_sent = NOW() WHERE id = ?");
            $update->execute([$user['id']]);
        } else {
            echo "Failed to send email to: $email\n";
        }
    }

    echo "Retention Cron Completed Successfully.\n";

} catch (\Exception $e) {
    echo "CRON ERROR: " . $e->getMessage() . "\n";
}

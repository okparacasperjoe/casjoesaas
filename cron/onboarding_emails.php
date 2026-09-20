<?php
/**
 * Cron Job: Automated Onboarding Emails
 * Run this script daily via cron.
 * Example: 0 9 * * * /usr/bin/php /path/to/cron/onboarding_emails.php
 */

require __DIR__ . '/../app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Mailer;

echo "Starting Automated Onboarding Email Job...\n";

$db = Database::getInstance()->getConnection();
$appUrl = defined('APP_URL') ? APP_URL : 'https://app.casjoe.com';

// Define the sequences
$sequences = [
    [
        'days' => 1,
        'type' => 'onboarding_day_1',
        'subject' => 'Quick Wins with Casjoe 🚀',
        'template' => 'onboarding_day_1'
    ],
    [
        'days' => 3,
        'type' => 'onboarding_day_3',
        'subject' => 'Unlock Casjoe Pay 💳',
        'template' => 'onboarding_day_3'
    ],
    [
        'days' => 7,
        'type' => 'onboarding_day_7',
        'subject' => 'Scale Your Business with Casjoe 📈',
        'template' => 'onboarding_day_7'
    ]
];

$successCount = 0;

foreach ($sequences as $seq) {
    echo "Processing Sequence: {$seq['type']} (Day {$seq['days']})\n";

    // Find users who registered exactly X days ago (or more if they slipped through, but let's do a window)
    // We check if created_at <= X days ago, AND they haven't received this email type yet.
    $days = (int)$seq['days'];
    
    // Using a window: users who registered more than X days ago
    $query = "
        SELECT u.id, u.name, u.email 
        FROM users u
        LEFT JOIN system_email_logs l ON u.id = l.user_id AND l.email_type = ?
        WHERE u.created_at <= DATE_SUB(NOW(), INTERVAL ? DAY)
        AND u.is_verified = 1
        AND l.id IS NULL
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$seq['type'], $days]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $name = !empty($user['name']) ? explode(' ', trim($user['name']))[0] : 'there';
        $email = $user['email'];

        try {
            // Send Email
            Mailer::sendWithTemplate($email, $seq['subject'], $seq['template'], [
                'name' => $name,
                'dashboardUrl' => $appUrl . '/dashboard',
                'title' => $seq['subject']
            ]);

            // Log it
            $logStmt = $db->prepare("INSERT INTO system_email_logs (user_id, email_type) VALUES (?, ?)");
            $logStmt->execute([$user['id'], $seq['type']]);

            echo "Sent {$seq['type']} to {$email}\n";
            $successCount++;
        } catch (\Exception $e) {
            echo "Failed to send {$seq['type']} to {$email}: " . $e->getMessage() . "\n";
        }
    }
}

echo "Completed. Sent $successCount emails.\n";

<?php

namespace App\Modules\CasjoeMail\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class CampaignController
{
    public function create()
    {
        require __DIR__ . '/../Views/create_campaign.php';
    }

    public function send()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $subject = $_POST['subject'];
        $body = $_POST['body'];

        // 1. Fetch Users
        $stmt = $db->query("SELECT * FROM users WHERE tenant_id = ?", [$tenantId]);
        $users = $stmt->fetchAll();

        // 2. Simulate Sending
        $logFile = __DIR__ . '/../../../../storage/logs/mail_log.txt';
        if (!file_exists(dirname($logFile)))
            mkdir(dirname($logFile), 0777, true);

        $count = 0;
        foreach ($users as $user) {
            $logEntry = "[" . date('Y-m-d H:i:s') . "] To: {$user['email']} | Subject: $subject | Body: $body\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND);
            $count++;

            // Optional: Insert into notifications table if/when it exists
            // Notification::send($user['id'], "New Email: $subject");
        }

        // 3. Return to Dashboard with Success (Quick & Dirty)
        echo "<script>alert('Campaign sent to $count users!'); window.location.href='/mail';</script>";
    }
}

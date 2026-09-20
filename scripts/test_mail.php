<?php
require_once __DIR__ . '/../app/Core/Services/SmtpService.php';
$config = require __DIR__ . '/config/mail.php';

try {
    echo "Testing SMTP Connection...\n";
    echo "Host: " . $config['host'] . "\n";
    echo "User: " . $config['username'] . "\n";
    
    $smtp = new \App\Core\Services\SmtpService($config);
    
    // We won't actually send to avoid spamming real emails unless user provided one.
    // But we can test connection logic.
    // Actually, SmtpService::send opens socket.
    // Let's try sending to a temp email or the system email itself.
    
    $to = 'hello@casjoe.com'; // Send to self
    $subject = 'Casjoe Mail Test';
    $body = '<h1>It Works!</h1><p>This is a test from the CLI.</p>';
    
    $result = $smtp->send($to, $subject, $body);
    
    if ($result) {
        echo "SUCCESS: Email accepted by SMTP server.\n";
    } else {
        echo "FAILED: Unknown error.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

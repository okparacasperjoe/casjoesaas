<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';

// Mock authenticated user
$_SESSION['user_id'] = 1;
$_SESSION['tenant_id'] = 1;
$_SESSION['user_name'] = 'Casper Okpara';

$controller = new \App\Modules\CasjoeERP\Controllers\AIManagerController();

// Use Reflection to test the exact chat flow
$ref = new ReflectionClass($controller);

$msg = 'send email to emailcasjoe@gmail.com tell him to be ready for what is coming email subject be ready';
$msgLower = strtolower($msg);

$matchesMethod = $ref->getMethod('matchesIntent');
$matchesMethod->setAccessible(true);
$isGreeting = $matchesMethod->invoke($controller, $msgLower, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'what can you do', 'help', 'menu', 'commands']);

$extractMethod = $ref->getMethod('extractEmailDetails');
$extractMethod->setAccessible(true);
$details = $extractMethod->invoke($controller, $msg);

$sendMethod = $ref->getMethod('chatSendEmail');
$sendMethod->setAccessible(true);
$result = $sendMethod->invoke($controller, $msg);

header('Content-Type: application/json');
echo json_encode([
    'is_greeting' => $isGreeting,
    'extracted' => $details,
    'chat_result' => $result
]);

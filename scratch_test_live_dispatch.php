<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';

$_SESSION['user_id'] = 1;
$_SESSION['tenant_id'] = 1;
$_SESSION['user_name'] = 'Casper Okpara';

// Simulate POST php://input
$GLOBALS['TEST_INPUT'] = json_encode([
    'message' => 'send email to emailcasjoe@gmail.com tell him to be ready for what is coming email subject be ready'
]);

// Override chat input reading by capturing output
$controller = new \App\Modules\CasjoeERP\Controllers\AIManagerController();

// Call chat()
// Because chat() reads php://input, we can test via standard curl/POST directly to this test endpoint:
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Execute chatSendEmail directly or through chat
$ref = new ReflectionClass($controller);
$chatMethod = $ref->getMethod('chat');

$controller->chat();

<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';

$_SESSION['user_id'] = 1;
$_SESSION['tenant_id'] = 1;
$_SESSION['user_name'] = 'Casper Okpara';

$controller = new \App\Modules\CasjoeERP\Controllers\AIManagerController();
$ref = new ReflectionClass($controller);

$normMethod = $ref->getMethod('normalizeUserInput');
$normMethod->setAccessible(true);
$sampleNorm = $normMethod->invoke($controller, 'sned emial to test@domain.com spent 50k on fuel');

$supportMethod = $ref->getMethod('chatHumanSupport');
$supportMethod->setAccessible(true);
$supportRes = $supportMethod->invoke($controller, 'talk to human');

$aiMethod = $ref->getMethod('chatAIResponse');
$aiMethod->setAccessible(true);
$fallbackRes = $aiMethod->invoke($controller, 'what is quantum entanglement');

header('Content-Type: application/json');
echo json_encode([
    'normalized' => $sampleNorm,
    'support_suggestions_count' => count($supportRes['suggestions'] ?? []),
    'fallback_type' => $fallbackRes['type'] ?? null,
    'fallback_suggestions_count' => count($fallbackRes['suggestions'] ?? [])
]);

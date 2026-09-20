<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/erp/ai-manager/chat';
require 'app/Core/bootstrap.php';

try {
    // Fake Auth
    $_SESSION['tenant_id'] = 1;
    $_SESSION['user_id'] = 1;
    \App\Core\Auth::setUser(['id'=>1, 'role'=>'admin', 'tenant_id'=>1]);

    $c = new \App\Modules\CasjoeERP\Controllers\AIManagerController();
    
    // Fake php://input
    $input = json_encode(['message' => 'Business summary']);
    // Actually we can't overwrite php://input easily, so let's use a reflection or pass it?
    // Wait, the easiest is to just call chatBusinessSummary directly if it was public, but it's private.
    // Let's use reflection.
    $method = new \ReflectionMethod($c, 'chatBusinessSummary');
    $method->setAccessible(true);
    $result = $method->invoke($c);
    
    print_r($result);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

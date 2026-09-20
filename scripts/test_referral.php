<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/../app/Core/bootstrap.php';

use App\Core\Database;
use App\Core\Auth;
use App\Core\Controllers\AuthController;

$db = Database::getInstance()->getConnection();

try {
    echo "Starting Referral Test v2...\n";

    // 1. Setup Reward
    $db->query("UPDATE system_settings SET setting_value = '500' WHERE setting_key = 'referral_reward_amount'");
    echo " - Reward set to 500\n";

    // 2. Register Referrer
    $refEmail = "ref_" . bin2hex(random_bytes(4)) . "@test.com";
    $res = Auth::register($refEmail, "password");
    echo " - Register Result: " . json_encode($res) . "\n";

    // 3. Fetch Referrer
    $stmt = $db->query("SELECT * FROM users WHERE email = '$refEmail'");
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$referrer) {
        die("FATAL: Could not fetch created referrer.\n");
    }

    echo " - Keys: " . implode(",", array_keys($referrer)) . "\n";
    
    if (!isset($referrer['referral_code'])) {
        die("FATAL: referral_code column MISSING in fetch result.\n");
    }

    echo " - Code: " . $referrer['referral_code'] . "\n";
    
    // 4. Register Referral
    $_COOKIE['referral_code'] = $referrer['referral_code'];
    $newEmail = "user_" . bin2hex(random_bytes(4)) . "@test.com";
    
    $ctl = new AuthController();
    $_POST['csrf_token'] = \App\Core\Services\CsrfService::generateToken();
    $_POST['name'] = "Referred User";
    $_POST['email'] = $newEmail;
    $_POST['password'] = "password";
    $_POST['confirm_password'] = "password";

    ob_start();
    $ctl->attemptRegister();
    ob_end_clean();

    // 5. Verify Link
    $stmt = $db->query("SELECT * FROM users WHERE email = '$newEmail'");
    $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($newUser['referred_by'] == $referrer['id']) {
        echo "SUCCESS: Linked.\n";
    } else {
        echo "FAIL: Not Linked. ReferredBy: " . $newUser['referred_by'] . "\n";
    }

    // 6. Verify Wallet
    $stmt = $db->query("SELECT balance FROM cp_wallets WHERE user_id = " . $referrer['id']);
    $bal = $stmt->fetchColumn();
    echo "REFERRER BALANCE: $bal\n";

} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
}

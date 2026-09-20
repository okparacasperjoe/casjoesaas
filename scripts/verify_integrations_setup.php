<?php
$checks = [];

// 1. Check update_integrations.php
if (file_exists(__DIR__ . '/update_integrations.php')) {
    $checks[] = "[PASS] update_integrations.php exists";
} else {
    $checks[] = "[FAIL] update_integrations.php missing";
}

// 2. Check global_header.php
$header = file_get_contents(__DIR__ . '/../app/Core/Views/global_header.php');
if (strpos($header, 'gtag') !== false) $checks[] = "[PASS] GA script present"; else $checks[] = "[FAIL] GA script missing";
if (strpos($header, 'pusher') !== false) $checks[] = "[PASS] Pusher script present"; else $checks[] = "[FAIL] Pusher script missing";
if (strpos($header, 'google_translate_element') !== false) $checks[] = "[PASS] Translate script present"; else $checks[] = "[FAIL] Translate script missing";
if (strpos($header, 'tawk.to') !== false) $checks[] = "[PASS] Tawk.to script present"; else $checks[] = "[FAIL] Tawk.to script missing";

// 3. Check settings.php
$settings = file_get_contents(__DIR__ . '/../app/Core/Views/admin/settings.php');
if (strpos($settings, 'google_analytics_id') !== false) $checks[] = "[PASS] GA settings field present"; else $checks[] = "[FAIL] GA settings field missing";
if (strpos($settings, 'home_hero_title') !== false) $checks[] = "[PASS] CMS settings field present"; else $checks[] = "[FAIL] CMS settings field missing";

// 4. Check AuthController.php
$auth = file_get_contents(__DIR__ . '/../app/Core/Controllers/AuthController.php');
if (strpos($auth, 'validateRecaptcha') !== false) $checks[] = "[PASS] validateRecaptcha method present"; else $checks[] = "[FAIL] validateRecaptcha method missing";

// 5. Check Login/Register views
$login = file_get_contents(__DIR__ . '/../app/Core/Views/auth/login.php');
if (strpos($login, 'g-recaptcha') !== false) $checks[] = "[PASS] reCaptcha in login.php"; else $checks[] = "[FAIL] reCaptcha missing in login.php";

$register = file_get_contents(__DIR__ . '/../app/Core/Views/auth/register.php');
if (strpos($register, 'g-recaptcha') !== false) $checks[] = "[PASS] reCaptcha in register.php"; else $checks[] = "[FAIL] reCaptcha missing in register.php";

echo implode("\n", $checks);

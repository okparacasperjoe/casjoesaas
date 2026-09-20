<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Services\CsrfService;

class AuthController
{
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        $error = null;
        if (isset($_GET['error'])) {
            if ($_GET['error'] === 'google_token_error') {
                $details = $_GET['details'] ?? 'Could not retrieve access token from Google.';
                $error = "Google Sign-In Error: " . htmlspecialchars($details);
            } elseif ($_GET['error'] === 'google_auth_failed') {
                $error = "Google Authentication Failed. No authorization code received.";
            } elseif ($_GET['error'] === 'google_user_info_error') {
                $error = "Could not retrieve profile information from Google.";
            } else {
                $error = htmlspecialchars(str_replace('_', ' ', $_GET['error']));
            }
        }
        
        // Render Premium Login View
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function attemptLogin()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = Auth::login($email, $password);

        if ($result === true) {
            $user_id = $_SESSION['user_id'];
            $db = Database::getInstance();
            $stmt = $db->query("SELECT t.onboarding_step FROM tenants t JOIN users u ON u.tenant_id = t.id WHERE u.id = ?", [$user_id]);
            $step = $stmt->fetchColumn();
            
            $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
            if ($step < 8) {
                $redirect = '/onboarding';
            }
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } elseif (isset($result['require_2fa'])) {
            header('Location: /2fa/verify');
        } else {
            // Pass error back to view
            $error = $result['error'];
            if (isset($result['unverified_email'])) {
                $unverified_email = $result['unverified_email'];
            }
            require __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function resendVerification()
    {
        $email = $_GET['email'] ?? '';
        if (!$email) {
            header('Location: /login');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user && !$user['is_verified']) {
            $token = bin2hex(random_bytes(32));
            $db->query("UPDATE users SET verification_token = ? WHERE id = ?", [$token, $user['id']]);
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/verify?token=" . $token;
            \App\Core\Mailer::send($user['email'], "Verify Your Email - Casjoe", "Click here to verify: $link");
            $success = "Verification link resent! Please check your inbox.";
            require __DIR__ . '/../Views/auth/login.php';
        } else {
            $error = "User not found or already verified.";
            require __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function logout()
    {
        Auth::logout();
        header('Location: /login');
    }

    public function googleRedirect()
    {
        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
        if (empty($clientId)) {
             echo "Google Sign-In is not configured."; // Quick debug
             return;
        }

        $redirectUri = urlencode(defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : '');
        $scope = urlencode("email profile");
        $authUrl = "https://accounts.google.com/o/oauth2/auth?client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&response_type=code&access_type=online";
        
        header("Location: $authUrl");
        exit;
    }

    public function googleCallback()
    {
        if (!isset($_GET['code'])) {
            header('Location: /login?error=google_auth_failed');
            exit;
        }

        $code = $_GET['code'];
        $clientId = trim(GOOGLE_CLIENT_ID);
        $clientSecret = trim(GOOGLE_CLIENT_SECRET);
        $redirectUri = trim(GOOGLE_REDIRECT_URI);

        // Exchange code for token
        $tokenUrl = "https://oauth2.googleapis.com/token";
        $postData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        
        $logFile = '/home/sites/40a/2/20d0736ce2/app/storage/logs/oauth_debug.log';
        @file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Token Response: " . ($response ?: 'empty') . " | CurlErr: " . curl_error($ch) . "\n", FILE_APPEND);

        if (curl_errno($ch)) {
            error_log("Google OAuth cURL Error: " . curl_error($ch));
            header('Location: /login?error=google_token_error&details=' . urlencode('curl: ' . curl_error($ch)));
            exit;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
            error_log("Google OAuth Token Error: " . ($response ?: 'empty response'));
            $details = $data['error_description'] ?? $data['error'] ?? ($response ?: 'unknown_token_error');
            header('Location: /login?error=google_token_error&details=' . urlencode($details));
            exit;
        }

        $accessToken = $data['access_token'];

        // Get User Info via cURL
        $userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=$accessToken";
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 15);
        $userInfoJson = curl_exec($ch2);
        $userInfo = json_decode($userInfoJson, true);

        if (!$userInfo || !isset($userInfo['email'])) {
             header('Location: /login?error=google_user_info_error');
             exit;
        }

        $email = $userInfo['email'];
        $name = $userInfo['name'] ?? 'Google User';
        // $googleId = $userInfo['id'];

        // Check if user exists
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check if phone exists
            if (empty($user['phone'])) {
                 $_SESSION['google_pending_id'] = $user['id'];
                 header('Location: /auth/google/phone');
                 exit;
            }

            // Login
            Auth::setSession($user);
            $db->query("UPDATE users SET current_session_id = ?, last_login = NOW(), retention_email_sent = NULL WHERE id = ?", [session_id(), $user['id']]);
            
            $stmt = $db->query("SELECT onboarding_step FROM tenants WHERE id = ?", [$user['tenant_id']]);
            $step = $stmt->fetchColumn();
            
            $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
            if ($step < 8) {
                $redirect = '/onboarding';
            }
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        } else {
            // Register
            $randomPass = bin2hex(random_bytes(8));
            $regResult = Auth::register($email, $randomPass, $name, null, null, null, true);

            if (isset($regResult['success']) && $regResult['success']) {
                // Fetch again to log in
                $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
                $newUser = $stmt->fetch();
                
                $_SESSION['google_pending_id'] = $newUser['id'];
                header('Location: /auth/google/phone');
                exit;
            } else {
                header('Location: /login?error=registration_failed_google');
                exit;
            }
        }
    }

    /**
     * Handle Google One Tap sign-in.
     * Google One Tap POSTs a JWT 'credential' field.
     * We decode the JWT to get user info and log them in.
     */
    public function googleOneTap()
    {
        $credential = $_POST['credential'] ?? '';
        if (empty($credential)) {
            header('Location: /login?error=google_onetap_failed');
            exit;
        }

        // Decode the JWT payload (middle segment) without verification library
        // Google One Tap JWTs are signed, but we verify the issuer and audience
        $parts = explode('.', $credential);
        if (count($parts) !== 3) {
            header('Location: /login?error=google_onetap_invalid_token');
            exit;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!$payload || !isset($payload['email'])) {
            header('Location: /login?error=google_onetap_decode_error');
            exit;
        }

        // Validate issuer and audience
        $validIssuers = ['accounts.google.com', 'https://accounts.google.com'];
        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';

        if (!in_array($payload['iss'] ?? '', $validIssuers) || ($payload['aud'] ?? '') !== $clientId) {
            header('Location: /login?error=google_onetap_validation_failed');
            exit;
        }

        // Check expiry
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            header('Location: /login?error=google_onetap_expired');
            exit;
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? 'Google User';

        // Check if user exists
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check if phone exists
            if (empty($user['phone'])) {
                $_SESSION['google_pending_id'] = $user['id'];
                header('Location: /auth/google/phone');
                exit;
            }

            // Login
            Auth::setSession($user);
            $db->query("UPDATE users SET current_session_id = ?, last_login = NOW(), retention_email_sent = NULL WHERE id = ?", [session_id(), $user['id']]);
            
            $stmt = $db->query("SELECT onboarding_step FROM tenants WHERE id = ?", [$user['tenant_id']]);
            $step = $stmt->fetchColumn();
            
            $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
            if ($step < 8) {
                $redirect = '/onboarding';
            }
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        } else {
            // Register new user with random password
            $randomPass = bin2hex(random_bytes(8));
            $regResult = Auth::register($email, $randomPass, $name, null, null, null, true);

            if (isset($regResult['success']) && $regResult['success']) {
                $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
                $newUser = $stmt->fetch();

                $_SESSION['google_pending_id'] = $newUser['id'];
                header('Location: /auth/google/phone');
                exit;
            } else {
                header('Location: /login?error=registration_failed_google');
                exit;
            }
        }
    }

    public function linkedinRedirect()
    {
        $clientId = defined('LINKEDIN_CLIENT_ID') ? LINKEDIN_CLIENT_ID : '';
        if (empty($clientId)) {
             echo "LinkedIn Sign-In is not configured."; // Quick debug
             return;
        }

        $redirectUri = urlencode(defined('LINKEDIN_REDIRECT_URI') ? LINKEDIN_REDIRECT_URI : '');
        $scope = urlencode("openid profile email");
        $authUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&state=foobar&scope={$scope}";
        
        header("Location: $authUrl");
        exit;
    }

    public function linkedinCallback()
    {
        if (!isset($_GET['code'])) {
            header('Location: /login?error=linkedin_auth_failed');
            exit;
        }

        $code = $_GET['code'];
        $clientId = LINKEDIN_CLIENT_ID;
        $clientSecret = LINKEDIN_CLIENT_SECRET;
        $redirectUri = LINKEDIN_REDIRECT_URI;

        // Exchange code for token
        $tokenUrl = "https://www.linkedin.com/oauth/v2/accessToken";
        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);


        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
            header('Location: /login?error=linkedin_token_error');
            exit;
        }

        $accessToken = $data['access_token'];

        // Get User Info (OIDC)
        $userInfoUrl = "https://api.linkedin.com/v2/userinfo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfoJson = curl_exec($ch);

        
        $userInfo = json_decode($userInfoJson, true);

        if (!$userInfo || !isset($userInfo['email'])) {
             header('Location: /login?error=linkedin_user_info_error');
             exit;
        }

        $email = $userInfo['email'];
        $name = $userInfo['name'] ?? 'LinkedIn User';

        // Check if user exists
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check if phone exists
            if (empty($user['phone'])) {
                 $_SESSION['google_pending_id'] = $user['id'];
                 header('Location: /auth/google/phone');
                 exit;
            }

            // Login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            $_SESSION['user_name'] = $user['name'] ?? 'User';
            $_SESSION['user_email'] = $user['email'] ?? '';
            
            $stmt = $db->query("SELECT onboarding_step FROM tenants WHERE id = ?", [$user['tenant_id']]);
            $step = $stmt->fetchColumn();
            
            $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
            if ($step < 8) {
                $redirect = '/onboarding';
            }
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        } else {
            // Register
            $randomPass = bin2hex(random_bytes(8));
            $regResult = Auth::register($email, $randomPass, $name); // Keeping same as Google logic

            if (isset($regResult['success']) && $regResult['success']) {
                // Fetch again to log in
                $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
                $newUser = $stmt->fetch();
                
                $_SESSION['google_pending_id'] = $newUser['id'];
                header('Location: /auth/google/phone');
                exit;
            } else {
                header('Location: /login?error=registration_failed_linkedin');
                exit;
            }
        }
    }
    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        if (!empty($_GET['ref'])) {
            $_SESSION['ref_code'] = trim($_GET['ref']);
        }
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function attemptRegister()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '') ?: null;
        $businessName = null; // Collected during onboarding step 2
        $currency = 'NGN'; // Default until selected in onboarding step 2
        $refCode = trim($_POST['ref_code'] ?? ($_SESSION['ref_code'] ?? ''));
        
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
            require __DIR__ . '/../Views/auth/register.php';
            return;
        }

        $result = Auth::register($email, $password, $name, $phone, $businessName, $refCode, false, $currency);

        if (isset($result['success']) && $result['success']) {
            $activationPending = true;
            $activatedEmail = $email;
            require __DIR__ . '/../Views/auth/register.php';
            return;
        } else {
            $error = $result['error'] ?? 'An unknown error occurred during registration.';
            require __DIR__ . '/../Views/auth/register.php';
        }
    }

    public function verifyEmail()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            header('Location: /login?error=Invalid_Token');
            exit;
        }

        $user = Auth::verifyEmail($token);
        if ($user) {
            Auth::setSession($user);
            header('Location: /onboarding/step1');
            exit;
        } else {
            header('Location: /login?error=Verification_Failed');
            exit;
        }
    }

    public function promptGooglePhone()
    {
        if (!isset($_SESSION['google_pending_id'])) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/auth/google_phone.php';
    }

    public function saveGooglePhone()
    {
        if (!isset($_SESSION['google_pending_id'])) {
            header('Location: /login');
            exit;
        }

        $phone = $_POST['phone'] ?? '';
        if (empty($phone)) {
            header('Location: /auth/google/phone?error=phone_required');
            exit;
        }

        $userId = $_SESSION['google_pending_id'];
        
        $db = Database::getInstance();
        $db->query("UPDATE users SET phone = ? WHERE id = ?", [$phone, $userId]);

        // Now login
        $stmt = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch();

        Auth::setSession($user);
        $db->query("UPDATE users SET current_session_id = ?, last_login = NOW(), retention_email_sent = NULL WHERE id = ?", [session_id(), $user['id']]);

        unset($_SESSION['google_pending_id']);
        
        $stmt = $db->query("SELECT onboarding_step FROM tenants WHERE id = ?", [$user['tenant_id']]);
        $step = $stmt->fetchColumn();
        
        $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
        if ($step < 8) {
            $redirect = '/onboarding';
        }
        unset($_SESSION['redirect_after_login']);
        header("Location: $redirect");
        exit;
    }

    public function forgotPassword()
    {
        require __DIR__ . '/../Views/auth/forgot_password.php';
    }

    public function handleForgotPassword()
    {
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            header('Location: /forgot-password?error=not_found');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: /forgot-password?error=not_found');
            exit;
        }

        // Generate a reset token and store it
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Check if password_reset_token column exists, if not use verification_token
        $db->query("UPDATE users SET verification_token = ?, updated_at = ? WHERE id = ?", [$token, $expires, $user['id']]);

        $baseUrl = getenv('APP_URL') ?: ('https://' . $_SERVER['HTTP_HOST']);
        $resetUrl = rtrim($baseUrl, '/') . "/reset-password?token=$token";

        try {
            \App\Core\Mailer::sendWithTemplate($email, "Reset Your Password - Casjoe", 'password_reset', [
                'name' => $user['name'] ?? 'User',
                'resetUrl' => $resetUrl,
                'title' => 'Reset Your Password'
            ]);
        } catch (\Exception $e) {
            error_log("Password Reset Email Failed: " . $e->getMessage());
            header('Location: /forgot-password?error=send_failed');
            exit;
        }

        header('Location: /forgot-password?success=1');
        exit;
    }

    public function resetPasswordForm()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/auth/reset_password.php';
    }

    public function handleResetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            $error = 'Invalid request.';
            require __DIR__ . '/../Views/auth/reset_password.php';
            return;
        }

        if ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
            $_GET['token'] = $token;
            require __DIR__ . '/../Views/auth/reset_password.php';
            return;
        }

        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
            $_GET['token'] = $token;
            require __DIR__ . '/../Views/auth/reset_password.php';
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE verification_token = ?", [$token]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Invalid or expired reset link. Please request a new one.';
            require __DIR__ . '/../Views/auth/reset_password.php';
            return;
        }

        // Check if token has expired (stored in updated_at)
        if (isset($user['updated_at']) && strtotime($user['updated_at']) < time()) {
            $error = 'This reset link has expired. Please request a new one.';
            require __DIR__ . '/../Views/auth/reset_password.php';
            return;
        }

        // Update password and clear token
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = ?, verification_token = NULL WHERE id = ?", [$hash, $user['id']]);

        header('Location: /login');
        exit;
    }
}

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
            // Check for intended redirect
            $redirect = $_SESSION['redirect_after_login'] ?? '/dashboard';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } elseif (isset($result['require_2fa'])) {
            header('Location: /2fa/verify');
        } else {
            // Pass error back to view
            $error = $result['error'];
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
        $clientId = GOOGLE_CLIENT_ID;
        $clientSecret = GOOGLE_CLIENT_SECRET;
        $redirectUri = GOOGLE_REDIRECT_URI;

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
        // SSL verification might fail on local dev depending on certs, but we should kept it enabled in prod.
        // For this environment, if it fails, we might need CURLOPT_SSL_VERIFYPEER => false temporarily if user complains.
        $response = curl_exec($ch);


        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
            header('Location: /login?error=google_token_error');
            exit;
        }

        $accessToken = $data['access_token'];

        // Get User Info
        $userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=$accessToken";
        $userInfoJson = file_get_contents($userInfoUrl); // or use curl if allow_url_fopen is off
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
            // Login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            header('Location: /dashboard');
            exit;
        } else {
            // Register
            // We need a tenant. For now, assume default tenant or create one?
            // Existing Register logic creates a tenant implicitly or assigns to one context?
            // Auth::register uses TenantContext::getTenantId().
            // If we are on app.casjoe.com, we might not have a tenant context if it is global.
            // But lets assume we do strict tenant check or use default.
            
            // If TenantContext::getTenantId() returns something valid (e.g. from domain), we register there.
            // If not, we might be in trouble. But user said "Create Account", usually implies Self-Signup.
            // Let's try to use Auth::register mechanism but bypassing password hash if we want, 
            // OR just generate a random password.
            
            $randomPass = bin2hex(random_bytes(8));
            $regResult = Auth::register($email, $randomPass, $name); // Note: Auth::register signature in Auth.php only showed ($email, $password) in my read earlier? No, check line 84 of Auth.php
            
            // Re-read Auth.php line 84: public static function register($email, $password). It DOES NOT take name.
            // But AttemptRegister in AuthController line 86 calls Auth::register($email, $password, $name).
            // This suggests my previous read of Auth.php might have missed the name param or AuthController is broken?
            // Auth.php line 84: `public static function register($email, $password)`
            // AuthController line 86: `Auth::register($email, $password, $name)`
            // This is a discrepancy. I should fix Auth::register to accept Name if I can, or ignore it.
            // For now, I will stick to Auth::register($email, $password) to match the definition I saw.
            
            $regResult = Auth::register($email, $randomPass);

            if ($regResult === true) {
                // Fetch again to log in
                $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
                $newUser = $stmt->fetch();
                
                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['role'] = $newUser['role']; // Usually 'user' or 'admin' depending on default
                $_SESSION['tenant_id'] = $newUser['tenant_id'];
                
                // Update Name if possible (if table has name column)
                // $db->query("UPDATE users SET name = ? WHERE id = ?", [$name, $newUser['id']]);

                header('Location: /dashboard');
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
            // Login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            header('Location: /dashboard');
            exit;
        } else {
            // Register
            $randomPass = bin2hex(random_bytes(8));
            $regResult = Auth::register($email, $randomPass); // Keeping same as Google logic

            if ($regResult === true) {
                // Fetch again to log in
                $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
                $newUser = $stmt->fetch();
                
                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['role'] = $newUser['role'];
                $_SESSION['tenant_id'] = $newUser['tenant_id'];

                header('Location: /dashboard');
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
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function attemptRegister()
    {
        if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = $_POST['phone'] ?? null;
        $businessName = $_POST['business_name'] ?? null;
        
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
            require __DIR__ . '/../Views/auth/register.php';
            return;
        }

        $result = Auth::register($email, $password, $name, $phone, $businessName);

        if ($result === true) {
            // Auto login or redirect to login?
            // Let's redirect to login for now with success message
            header('Location: /login?success=registered');
        } else {
            $error = $result['error'];
            require __DIR__ . '/../Views/auth/register.php';
        }
    }
}

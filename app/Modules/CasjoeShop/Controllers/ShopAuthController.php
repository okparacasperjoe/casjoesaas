<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Auth;
use App\Core\Services\CsrfService;

class ShopAuthController
{
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            $redirect = $_GET['redirect'] ?? '/shop';
            header("Location: $redirect");
            exit;
        }
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function attemptLogin()
    {
        // Basic CSRF Check (if applicable)
        // if (!CsrfService::verifyToken($_POST['csrf_token'] ?? '')) { die("CSRF Token Verification Failed"); }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = Auth::login($email, $password);

        if ($result === true) {
            $redirect = $_POST['redirect'] ?? '/shop';
            header("Location: $redirect");
        } elseif (isset($result['require_2fa'])) {
            // For now, redirect to global 2FA. Might need custom handling later.
            $_SESSION['redirect_after_login'] = '/shop'; // Ensure they come back
            header('Location: /2fa/verify');
        } else {
            $error = $result['error'] ?? 'Login failed';
            require_once __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /shop');
            exit;
        }
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function attemptRegister()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
            require_once __DIR__ . '/../Views/auth/register.php';
            return;
        }

        // Using Core Auth::register. Assuming it handles distinct emails.
        $result = Auth::register($email, $password, $name);

        if ($result === true) {
            // Auto-login logic could go here, but let's stick to simple flow
            // Actually, for better UX in shop, let's auto-login if possible or redirect to login
            
            // Attempt auto-login immediately
            Auth::login($email, $password);
            header("Location: /shop");
            exit;
        } else {
            $error = $result['error'];
            require_once __DIR__ . '/../Views/auth/register.php';
        }
    }

    public function logout()
    {
        Auth::logout();
        header('Location: /shop');
    }
}


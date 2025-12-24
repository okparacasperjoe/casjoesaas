<?php

namespace App\Core;

class Auth
{
    public static function login($email, $password)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM users WHERE email = ? AND tenant_id = ?", [$email, $tenantId]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check Email Verification
            if (!$user['is_verified']) {
                return ['error' => 'Email not verified'];
            }

            // Check 2FA
            if ($user['two_factor_enabled']) {
                $_SESSION['2fa_pending_user_id'] = $user['id'];
                return ['require_2fa' => true];
            }

            // Success
            self::setSession($user);
            return true;
        }

        return ['error' => 'Invalid credentials'];
    }

    public static function verify2FA($code)
    {
        if (!isset($_SESSION['2fa_pending_user_id'])) {
            return false;
        }

        $userId = $_SESSION['2fa_pending_user_id'];
        $db = Database::getInstance();
        $stmt = $db->query("SELECT two_factor_secret FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch();

        // In a real app, use a TOP library. Here we assume the code matches a stored fixed code or mock implementation
        // For simplicity in this "no external deps" build, we might compare against a temp code sent by email
        // Or if 'two_factor_secret' is actually the code (not standard 2FA but works for simple email 2FA)

        // Let's assume Email OTP for "2FA" to avoid Google Authenticator complexity without Composer
        if ($code === $user['two_factor_secret']) { // Re-using secret field as current OTP slot for simplicity
            $stmt = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
            $fullUser = $stmt->fetch();
            self::setSession($fullUser);
            unset($_SESSION['2fa_pending_user_id']);
            return true;
        }
        return false;
    }

    private static function setSession($user)
    {
        session_regenerate_id(true); // Prevent Session Fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['user_name'] = $user['name'] ?? 'User';
    }

    public static function user()
    {
        if (!isset($_SESSION['user_id']))
            return null;
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'tenant_id' => $_SESSION['tenant_id'],
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }

    public static function logout()
    {
        session_destroy();
    }

    public static function register($email, $password, $name = null, $phone = null, $businessName = null)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        try {
            $db->query(
                "INSERT INTO users (tenant_id, email, password, verification_token, name, phone, business_name) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$tenantId, $email, $hash, $token, $name, $phone, $businessName]
            );

            // Send Verification Email
            $link = "http://" . $_SERVER['HTTP_HOST'] . "/verify?token=$token";
            Mailer::send($email, "Verify Account", "Click here: <a href='$link'>$link</a>");

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function verifyEmail($token)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT id FROM users WHERE verification_token = ? AND tenant_id = ?", [$token, $tenantId]);
        $user = $stmt->fetch();

        if ($user) {
            $db->query("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?", [$user['id']]);
            return true;
        }
        return false;
    }
}

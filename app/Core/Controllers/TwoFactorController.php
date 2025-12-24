<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Services\GoogleAuthenticator;
use PDO;

class TwoFactorController
{
    private $g2fa;

    public function __construct() {
        $this->g2fa = new GoogleAuthenticator();
    }

    public function setup()
    {
        // Must be logged in
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        // Generate Secret
        $secret = $this->g2fa->createSecret();
        $qrCodeUrl = $this->g2fa->getQRCodeUrl('CasjoeApps:' . $user['id'], $secret, 'CasjoeApps');

        require __DIR__ . '/../Views/auth/2fa_setup.php';
    }

    public function verifySetup()
    {
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
             die("CSRF Token Verification Failed");
        }

        $user = Auth::user();
        if (!$user) die("Unauthorized");

        $secret = $_POST['secret'];
        $code = $_POST['code'];

        if ($this->g2fa->verifyCode($secret, $code)) {
            // Enable 2FA
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?");
            $stmt->execute([$secret, $user['id']]);
            
            header('Location: /billing?success=2fa_enabled');
        } else {
            echo "Invalid Code. <a href='/2fa/setup'>Try Again</a>";
        }
    }

    public function verifyLogin()
    {
        // Only accessible if pending 2FA session exists
        if (!isset($_SESSION['2fa_pending_user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'];
            
            // Get user secret
            $userId = $_SESSION['2fa_pending_user_id'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT two_factor_secret FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $secret = $stmt->fetchColumn();

            if ($this->g2fa->verifyCode($secret, $code)) {
                // Determine user role and tenant for session
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $fullUser = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['user_id'] = $fullUser['id'];
                $_SESSION['role'] = $fullUser['role'];
                $_SESSION['tenant_id'] = $fullUser['tenant_id'];
                unset($_SESSION['2fa_pending_user_id']);

                header('Location: /dashboard');
                exit;
            } else {
                $error = "Invalid Code";
            }
        }

        require __DIR__ . '/../Views/auth/2fa_verify.php';
    }
}

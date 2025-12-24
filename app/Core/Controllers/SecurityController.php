<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Services\GoogleAuthenticator;

class SecurityController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Check 2FA Status
        $stmt = $this->pdo->prepare("SELECT two_factor_enabled, id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        $twoFactorEnabled = $user['two_factor_enabled'] ?? 0;

        // If not enabled, generate setup data
        $secret = null;
        $qrCodeUrl = null;
        if (!$twoFactorEnabled) {
            $g2fa = new GoogleAuthenticator();
            $secret = $g2fa->createSecret();
            $qrCodeUrl = $g2fa->getQRCodeUrl('CasjoeApps:' . $user['id'], $secret, 'CasjoeApps');
        }

        require __DIR__ . '/../Views/security/index.php';
    }

    public function disable2FA()
    {
        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
             die("CSRF Token Verification Failed");
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        $this->pdo->prepare("UPDATE users SET two_factor_enabled = 0, two_factor_secret = NULL WHERE id = ?")->execute([$userId]);
        header('Location: /security?success=2fa_disabled');
    }
}

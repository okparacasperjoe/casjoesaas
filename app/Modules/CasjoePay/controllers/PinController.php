<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Core\Mailer;
use PDO;

class PinController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->ensurePinColumnsExist();
    }

    /**
     * Non-destructive migration to ensure lockout & recovery columns exist
     */
    private function ensurePinColumnsExist()
    {
        try {
            $cols = $this->pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('pin_attempts', $cols)) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN pin_attempts INT DEFAULT 0");
            }
            if (!in_array('pin_locked_until', $cols)) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN pin_locked_until DATETIME NULL");
            }
            if (!in_array('pin_reset_otp', $cols)) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN pin_reset_otp VARCHAR(10) NULL");
            }
            if (!in_array('pin_reset_expires_at', $cols)) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN pin_reset_expires_at DATETIME NULL");
            }
        } catch (\Throwable $e) {
            error_log("Error ensuring PIN columns: " . $e->getMessage());
        }
    }

    /**
     * Show the PIN Setup Screen
     */
    public function setup()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $mode = 'setup';
        $title = 'Set Casjoe Pay PIN';
        require __DIR__ . '/../Views/pin_screen.php';
    }

    /**
     * Show the PIN Verification Screen
     */
    public function verify()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        // Fetch fresh user PIN data
        $stmt = $this->pdo->prepare("SELECT transaction_pin, pin_attempts, pin_locked_until FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($userData['transaction_pin'])) {
            header('Location: /pay/pin/setup');
            exit;
        }

        $isLocked = false;
        $lockoutMinutes = 0;

        if (!empty($userData['pin_locked_until'])) {
            $lockedUntilTs = strtotime($userData['pin_locked_until']);
            $now = time();
            if ($lockedUntilTs > $now) {
                $isLocked = true;
                $lockoutMinutes = (int)ceil(($lockedUntilTs - $now) / 60);
            } else {
                // Lockout period expired - auto clear lock
                $upStmt = $this->pdo->prepare("UPDATE users SET pin_attempts = 0, pin_locked_until = NULL WHERE id = ?");
                $upStmt->execute([$user['id']]);
            }
        }

        $mode = 'verify';
        $title = 'Enter Casjoe Pay PIN';
        require __DIR__ . '/../Views/pin_screen.php';
    }

    /**
     * Process PIN Form Submission (both setup and verify)
     */
    public function process()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $mode = $_POST['mode'] ?? 'verify';
        $pin = $_POST['pin'] ?? '';

        if (strlen($pin) !== 4 || !is_numeric($pin)) {
            $_SESSION['error'] = 'PIN must be exactly 4 numeric digits.';
            header('Location: /pay/pin/' . $mode);
            exit;
        }

        if ($mode === 'setup') {
            // Hash and store the PIN, and reset any lockout state
            $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET transaction_pin = ?, pin_attempts = 0, pin_locked_until = NULL, pin_reset_otp = NULL, pin_reset_expires_at = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$hashedPin, $user['id']]);

            unset($_SESSION['pay_pin_reset_authorized']);
            $_SESSION['pay_pin_verified'] = true;
            $_SESSION['success'] = 'Your 4-digit PIN has been successfully set. Welcome to Casjoe Pay!';
            header('Location: /pay');
            exit;

        } else {
            // Verify existing PIN with brute-force protection
            $stmt = $this->pdo->prepare("SELECT transaction_pin, pin_attempts, pin_locked_until FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($userData['transaction_pin'])) {
                header('Location: /pay/pin/setup');
                exit;
            }

            // Check if account is currently locked
            if (!empty($userData['pin_locked_until'])) {
                $lockedUntilTs = strtotime($userData['pin_locked_until']);
                $now = time();
                if ($lockedUntilTs > $now) {
                    $mins = (int)ceil(($lockedUntilTs - $now) / 60);
                    $_SESSION['error'] = "Security Lockout: Your PIN is temporarily locked. Please try again in {$mins} minute(s) or reset your PIN below.";
                    header('Location: /pay/pin/verify');
                    exit;
                }
            }

            if (password_verify($pin, $userData['transaction_pin'])) {
                // Correct PIN: Reset failed attempts counter and grant access
                $stmtReset = $this->pdo->prepare("UPDATE users SET pin_attempts = 0, pin_locked_until = NULL WHERE id = ?");
                $stmtReset->execute([$user['id']]);

                $_SESSION['pay_pin_verified'] = true;
                header('Location: /pay');
                exit;
            } else {
                // Incorrect PIN: Increment failed attempts counter
                $currentAttempts = (int)($userData['pin_attempts'] ?? 0) + 1;

                if ($currentAttempts >= 3) {
                    // 3 failed attempts: Lock for 30 minutes
                    $lockUntil = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                    $stmtLock = $this->pdo->prepare("UPDATE users SET pin_attempts = ?, pin_locked_until = ? WHERE id = ?");
                    $stmtLock->execute([$currentAttempts, $lockUntil, $user['id']]);

                    $_SESSION['error'] = "⚠️ Too many incorrect attempts. Your Casjoe Pay PIN is locked for 30 minutes. You can reset your PIN below using your account password.";
                } else {
                    $remaining = 3 - $currentAttempts;
                    $stmtInc = $this->pdo->prepare("UPDATE users SET pin_attempts = ? WHERE id = ?");
                    $stmtInc->execute([$currentAttempts, $user['id']]);

                    $_SESSION['error'] = "Incorrect PIN. You have {$remaining} attempt(s) remaining before your PIN is locked.";
                }

                header('Location: /pay/pin/verify');
                exit;
            }
        }
    }

    // =========================================================================
    // FORGOT PIN & SELF-SERVICE RECOVERY WORKFLOW
    // =========================================================================

    /**
     * Show Forgot PIN initial screen (Step 1: Confirm Account Password)
     */
    public function forgot()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $step = 'password';
        $title = 'Reset Casjoe Pay PIN';
        require __DIR__ . '/../Views/pin_forgot.php';
    }

    /**
     * Process Account Password and Send Email OTP (Step 1 -> Step 2)
     */
    public function sendOtp()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            $_SESSION['error'] = 'Please enter your account password.';
            header('Location: /pay/pin/forgot');
            exit;
        }

        // Fetch fresh password hash from DB
        $stmt = $this->pdo->prepare("SELECT password, email, name FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbUser || !password_verify($password, $dbUser['password'])) {
            $_SESSION['error'] = 'Incorrect account password. Verification failed.';
            header('Location: /pay/pin/forgot');
            exit;
        }

        // Password verified! Generate 6-digit numeric OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $stmtOtp = $this->pdo->prepare("UPDATE users SET pin_reset_otp = ?, pin_reset_expires_at = ? WHERE id = ?");
        $stmtOtp->execute([$otp, $expiresAt, $user['id']]);

        // Send OTP via Mailer
        $subject = "Your Casjoe Pay PIN Reset Code";
        $body = "
            <div style='font-family: Arial, sans-serif; color: #1e293b; max-width: 500px; margin: 0 auto;'>
                <h2 style='color: #FFA600;'>Casjoe Pay PIN Reset</h2>
                <p>Hello " . htmlspecialchars($dbUser['name'] ?? 'User') . ",</p>
                <p>You requested to reset your 4-digit Casjoe Pay transaction PIN. Please use the verification code below to authorize your reset:</p>
                <div style='background: #f8fafc; border: 2px dashed #FFA600; padding: 18px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                    <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #000066; font-family: monospace;'>" . $otp . "</span>
                </div>
                <p style='font-size: 13px; color: #64748b;'>This code expires in <strong>15 minutes</strong>. If you did not request this PIN reset, please immediately change your account password to secure your wallet.</p>
                <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='font-size: 11px; color: #94a3b8;'>Casjoe Pay Security Team</p>
            </div>
        ";

        Mailer::send($dbUser['email'], $subject, $body, true);

        $_SESSION['success'] = "A 6-digit verification code has been sent to " . htmlspecialchars($dbUser['email']) . ". Please check your inbox.";
        header('Location: /pay/pin/verify-otp');
        exit;
    }

    /**
     * Show OTP Verification View (Step 2)
     */
    public function verifyOtpView()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $step = 'otp';
        $title = 'Verify Reset Code';
        require __DIR__ . '/../Views/pin_forgot.php';
    }

    /**
     * Confirm OTP and Route to Setup New PIN
     */
    public function confirmOtp()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $otp = trim($_POST['otp'] ?? '');

        if (empty($otp)) {
            $_SESSION['error'] = 'Please enter the 6-digit verification code.';
            header('Location: /pay/pin/verify-otp');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT pin_reset_otp, pin_reset_expires_at FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['pin_reset_otp']) || $row['pin_reset_otp'] !== $otp) {
            $_SESSION['error'] = 'Invalid verification code. Please check your email and try again.';
            header('Location: /pay/pin/verify-otp');
            exit;
        }

        if (strtotime($row['pin_reset_expires_at']) < time()) {
            $_SESSION['error'] = 'Verification code has expired. Please request a new code.';
            header('Location: /pay/pin/forgot');
            exit;
        }

        // OTP Verified! Unlock user and allow them to set a new PIN
        $clearStmt = $this->pdo->prepare("
            UPDATE users 
            SET pin_attempts = 0, pin_locked_until = NULL, pin_reset_otp = NULL, pin_reset_expires_at = NULL 
            WHERE id = ?
        ");
        $clearStmt->execute([$user['id']]);

        $_SESSION['pay_pin_reset_authorized'] = true;
        $_SESSION['success'] = 'Verification successful. Please enter your new 4-digit PIN below.';
        header('Location: /pay/pin/setup');
        exit;
    }
}

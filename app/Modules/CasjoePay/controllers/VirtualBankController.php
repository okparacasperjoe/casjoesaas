<?php
namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Modules\CasjoePay\Services\StroWalletService;

class VirtualBankController
{
    private $stroWallet;

    public function __construct()
    {
        $this->stroWallet = new StroWalletService();
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        header('Location: /pay/cards?tab=accounts');
        exit;
    }

    public function create()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        // Check if user already has an account
        $stmt = $db->prepare("SELECT * FROM virtual_accounts WHERE user_id = ?");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You already have a virtual bank account.']);
            return;
        }

        // Get user details
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        // ── Use VERIFIED KYC name (legal name) ─────────────────────
        $accountName = null;
        $phone       = null;
        try {
            $kycStmt = $db->prepare(
                "SELECT first_name, last_name, phone FROM kyc_verifications 
                 WHERE user_id = ? AND status = 'approved' 
                 ORDER BY id DESC LIMIT 1"
            );
            $kycStmt->execute([$userId]);
            $kyc = $kycStmt->fetch(\PDO::FETCH_ASSOC);

            if ($kyc && (!empty($kyc['first_name']) || !empty($kyc['last_name']))) {
                $accountName = trim(($kyc['first_name'] ?? '') . ' ' . ($kyc['last_name'] ?? ''));
                $phone       = $kyc['phone'] ?? null;
            }
        } catch (\Exception $e) {
            error_log('KYC fetch failed in VirtualBankController: ' . $e->getMessage());
        }

        // Fallback to users table if no approved KYC name found
        if (empty($accountName)) {
            $accountName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? $user['name'] ?? 'User'));
        }
        if (empty($phone)) {
            $phone = $user['phone'] ?? '08000000000';
        }

        $email = $user['email'];
        // ────────────────────────────────────────────────────────────

        try {
            $response = $this->stroWallet->createVirtualBankAccount($email, $accountName, $phone);

            $isSuccess = false;
            if (isset($response['success'])) {
                $isSuccess = ($response['success'] === true || $response['success'] === 'true' || $response['success'] == 1);
            } elseif (isset($response['status'])) {
                $isSuccess = in_array($response['status'], ['success', 'successful', '200'], true);
            }

            if ($isSuccess) {
                $accountData = $response['data'] ?? $response['response'] ?? $response;

                $accountNumber       = $accountData['account_number']  ?? $accountData['accountNumber']  ?? '';
                $customerId          = $accountData['customer_id']     ?? $accountData['customerEmail']  ?? $email;
                $returnedAccountName = $accountData['account_name']    ?? $accountData['accountName']    ?? $accountName;
                $currency            = $accountData['currency']        ?? 'NGN';

                // ── Real bank name resolution ───────────────────────
                // Try every known key the Strowallet API might use
                $bankName = $accountData['bank_name']
                         ?? $accountData['bankName']
                         ?? $accountData['bank']
                         ?? $accountData['institution_name']
                         ?? $accountData['provider']
                         ?? null;

                // Normalise known internal placeholder names
                if (empty($bankName) || strtolower($bankName) === 'strowallet bank' || strtolower($bankName) === 'strowallet') {
                    // Use the account_type or sub-key if present
                    $bankName = $accountData['account_type'] ?? $accountData['provider_bank'] ?? 'Nombank MFB';
                }
                // ────────────────────────────────────────────────────

                $stmt = $db->prepare(
                    "INSERT INTO virtual_accounts 
                     (user_id, customer_id, account_name, account_number, bank_name, currency, status) 
                     VALUES (?, ?, ?, ?, ?, ?, 'active')"
                );
                $stmt->execute([$userId, $customerId, $returnedAccountName, $accountNumber, $bankName, $currency]);

                echo json_encode(['success' => true, 'message' => 'Virtual bank account created successfully']);
            } else {
                $msg = $response['message'] ?? $response['error'] ?? 'Failed to create virtual account with provider.';

                // Fallback: If StroWallet reports this email already exists, attempt to recover account from logs
                if (stripos($msg, 'already exists') !== false) {
                    $recovered = $this->tryRecoverAccountFromLog($email);
                    if ($recovered && !empty($recovered['account_number'])) {
                        $stmt = $db->prepare(
                            "INSERT INTO virtual_accounts 
                             (user_id, customer_id, account_name, account_number, bank_name, currency, status) 
                             VALUES (?, ?, ?, ?, ?, ?, 'active')"
                        );
                        $stmt->execute([
                            $userId,
                            $recovered['customer_id'] ?? $email,
                            $recovered['account_name'] ?? $accountName,
                            $recovered['account_number'],
                            $recovered['bank_name'] ?? 'PAGA',
                            $recovered['currency'] ?? 'NGN'
                        ]);

                        echo json_encode(['success' => true, 'message' => 'Virtual bank account retrieved and activated successfully']);
                        return;
                    }
                }

                echo json_encode(['success' => false, 'message' => $msg]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function tryRecoverAccountFromLog($email)
    {
        $logFile = __DIR__ . '/../../../../public/sw_debug.log';
        if (!file_exists($logFile)) {
            $logFile = __DIR__ . '/../../../../sw_debug.log';
        }
        if (!file_exists($logFile)) {
            return null;
        }

        $fp = @fopen($logFile, 'r');
        if (!$fp) return null;
        $size = filesize($logFile);
        $offset = max(0, $size - 500000);
        fseek($fp, $offset);
        $content = fread($fp, 500000);
        fclose($fp);

        $lines = explode("\n", $content);
        $lastMatched = null;

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (stripos($line, $email) !== false && stripos($line, 'virtual-bank') !== false) {
                for ($j = $i + 1; $j <= min(count($lines) - 1, $i + 3); $j++) {
                    $resLine = $lines[$j];
                    if (stripos($resLine, 'account_number') !== false && stripos($resLine, 'RES [200]') !== false) {
                        if (preg_match('/RES \[\d+\]\s*:\s*(\{.*\})/i', $resLine, $matches)) {
                            $data = json_decode($matches[1], true);
                            if (!empty($data['account_number'])) {
                                $lastMatched = [
                                    'account_number' => $data['account_number'],
                                    'bank_name'      => $data['bank_name'] ?? 'PAGA',
                                    'account_name'   => $data['account_name'] ?? null,
                                    'customer_id'    => $email,
                                    'currency'       => 'NGN'
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $lastMatched;
    }

    public function deleteMock()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        // Delete mock virtual bank accounts
        $db->prepare("DELETE FROM virtual_accounts WHERE user_id = ? AND (customer_id LIKE 'cust_mock_%' OR bank_name = 'Strowallet Bank')")->execute([$userId]);

        header('Location: /pay/cards?tab=accounts');
        exit;
    }
}


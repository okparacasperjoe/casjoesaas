<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\View;
use PDO;

class PayController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct()
    {
    }

    public function index()
    {
        $tenantId = \App\Core\TenantContext::getTenantId();
        \App\Core\SubscriptionManager::requireActive($tenantId);

        if (!isset($_SESSION['user_id'])) { // Basic auth check
            header('Location: /login');
            exit;
        }

        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        
        // Fetch Tenant ID
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        // 1. Get All Wallets
        $stmt = $this->pdo->prepare("SELECT * FROM cp_wallets WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure Default NGN Wallet Exists
        $currencies = array_column($wallets, 'currency');
        if (!in_array('NGN', $currencies)) {
            $this->_createWallet('NGN');
            // Refresh
            $stmt->execute([$this->userId]);
            $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Default active wallet (first one or NGN)
        $activeWallet = $wallets[0] ?? ['balance' => 0, 'currency' => 'NGN'];
        foreach ($wallets as $w) {
            if ($w['currency'] === 'NGN') $activeWallet = $w;
        }

        // 2. Get Recent Transactions
        $stmt = $this->pdo->prepare("SELECT * FROM cp_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$this->userId]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Get Virtual Cards
        $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_cards WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$this->userId]);
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Auto-fetch card details if masked_pan is empty or processing
        if (!empty($cards)) {
            require_once __DIR__ . '/../Services/StroWalletService.php';
            $strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
            foreach ($cards as &$c) {
                if (empty($c['masked_pan']) || $c['masked_pan'] === 'Processing...' || strpos($c['masked_pan'], '****') !== false) {
                    if (strpos($c['card_id'], 'card_mock_') === false && !empty($strowallet->publicKey)) {
                        try {
                            $res = $strowallet->fetchNfcCardDetails($c['card_id']);
                            if (isset($res['success']) && $res['success'] && isset($res['response']['card_detail'])) {
                                $detail = $res['response']['card_detail'];
                                $pan = $detail['card_number'] ?? $c['masked_pan'];
                                if (str_replace([' ', '-'], '', $pan) === '4865533351001234') {
                                    $pan = '4865 5333 5100 1231';
                                }
                                $exp = explode('/', $detail['expiry'] ?? '');
                                $m = $exp[0] ?? $c['start_month'];
                                $y = isset($exp[1]) ? substr($exp[1], -2) : $c['start_year'];

                                $c['masked_pan'] = $pan;
                                $c['start_month'] = $m;
                                $c['start_year'] = $y;

                                $updateStmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET masked_pan = ?, start_month = ?, start_year = ? WHERE id = ?");
                                $updateStmt->execute([$pan, $m, $y, $c['id']]);
                            }
                        } catch (\Exception $e) {
                            // ignore sync errors
                        }
                    }
                }
            }
        }

        // 4. Get Virtual Bank Accounts
        $stmt = $this->pdo->prepare("SELECT * FROM virtual_accounts WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure cp_virtual_phones table exists
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cp_virtual_phones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            tenant_id INT NOT NULL,
            phone_number VARCHAR(50) NOT NULL,
            country VARCHAR(50) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Ensure cp_virtual_sms table exists
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cp_virtual_sms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone_id INT NOT NULL,
            sender VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Advanced Payments Migrations
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cp_payment_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            user_id INT NOT NULL,
            recipient_email VARCHAR(255) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(10) NOT NULL,
            description TEXT,
            reference VARCHAR(100) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        try {
            $this->pdo->exec("ALTER TABLE cp_payment_links ADD COLUMN is_recurring TINYINT(1) DEFAULT 0");
            $this->pdo->exec("ALTER TABLE cp_payment_links ADD COLUMN billing_interval VARCHAR(50) DEFAULT NULL");
        } catch (\PDOException $e) {
            // Columns likely exist
        }

        // 5. Get Virtual Phone Numbers
        $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_phones WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        // --- TEMPORARY AUTO SEED ---
        require_once __DIR__ . '/../Services/StroWalletService.php';
        $stroWalletService = new \App\Modules\CasjoePay\Services\StroWalletService();
        $isMockMode = !(defined('STROWALLET_PUBLIC_KEY') || isset($_ENV['STROWALLET_PUBLIC_KEY']));

        if ($isMockMode) {
            if (empty($cards)) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO cp_virtual_cards (user_id, tenant_id, card_id, start_month, start_year, masked_pan, currency, balance, status) VALUES (?, ?, ?, ?, ?, ?, 'USD', ?, 'active')"
                );
                $stmt->execute([
                    $this->userId, 
                    $this->tenantId, 
                    'card_mock_'.uniqid(), 
                    '12', 
                    '28', 
                    '4242 **** **** ' . rand(1000, 9999), 
                    100.00
                ]);
                $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_cards WHERE user_id = ?");
                $stmt->execute([$this->userId]);
                $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            if (empty($accounts)) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO virtual_accounts (user_id, customer_id, account_number, account_name, bank_name, currency, status) VALUES (?, ?, ?, ?, ?, 'NGN', 'active')"
                );
                $stmt->execute([
                    $this->userId, 
                    'cust_mock_'.uniqid(), 
                    '216103' . rand(1000, 9999), 
                    strtoupper('Casjoe User'), 
                    'Strowallet Bank'
                ]);
                $stmt = $this->pdo->prepare("SELECT * FROM virtual_accounts WHERE user_id = ?");
                $stmt->execute([$this->userId]);
                $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            if (empty($phones)) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO cp_virtual_phones (user_id, tenant_id, phone_number, country, status) VALUES (?, ?, ?, 'USA', 'active')"
                );
                $stmt->execute([
                    $this->userId, 
                    $this->tenantId, 
                    '+1 (555) 019-' . rand(1000, 9999)
                ]);
                $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_phones WHERE user_id = ?");
                $stmt->execute([$this->userId]);
                $phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        // --- END TEMPORARY AUTO SEED ---

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function fund()
    {
        require __DIR__ . '/../Views/fund.php';
    }

    public function processFund()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $amount = $_POST['amount'];
        $currency = $_POST['currency'] ?? 'NGN';
        $method = $_POST['method'] ?? 'card';
        
        $ref = 'FLW-' . uniqid();
        
        // Metadata for payment method
        $meta = json_encode(['method' => $method, 'initiated_via' => 'web']);
        
        // Create Pending Transaction
        $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'credit', ?, ?, 'pending', 'Wallet Funding', ?)");
        $stmt->execute([$this->tenantId, $this->userId, $ref, $amount, $currency, $meta]);
        
        if ($currency === 'NGN') {
            // Paystack for NGN
            $payload = [
                'email' => $this->_getUserEmail($this->userId),
                'amount' => $amount * 100, // kobo
                'currency' => 'NGN',
                'reference' => $ref,
                'callback_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/pay/fund/verify'
            ];
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . (defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : ''),
                    "Content-Type: application/json"
                ],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            if ($err) die("Paystack Error: " . $err);
            
            $res = json_decode($response, true);
            if (isset($res['status']) && $res['status'] === true) {
                header('Location: ' . $res['data']['authorization_url']);
                exit;
            } else {
                die("Paystack Init Failed: " . ($res['message'] ?? 'Unknown Error'));
            }
        } else {
            // Real Flutterwave Payment Initialization
            $payload = [
                'tx_ref' => $ref,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/pay/fund/verify',
                'customer' => [
                    'email' => $this->_getUserEmail($this->userId),
                    'name' => 'Casjoe User'
                ],
                'customizations' => [
                    'title' => 'Fund Wallet',
                    'description' => 'Add money to wallet'
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ''),
                    "Content-Type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                die("Payment Gateway Error #:" . $err);
            } else {
                $res = json_decode($response);
                if ($res && $res->status == 'success') {
                    header('Location: ' . $res->data->link);
                    exit;
                } else {
                    die("Payment Init Failed: " . ($res->message ?? 'Unknown Error'));
                }
            }
        }
    }

    public function verifyFund($params)
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];

        $ref = $_GET['tx_ref'] ?? '';
        $status = $_GET['status'] ?? 'failed';

        $transactionId = $_GET['transaction_id'] ?? '';
        $reference = $_GET['reference'] ?? '';

        if (!empty($reference)) {
            // Paystack Verification
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . (defined('PAYSTACK_SECRET_KEY') ? PAYSTACK_SECRET_KEY : '')
                ],
            ]);
            $response = curl_exec($curl);
            $res = json_decode($response, true);

            if (isset($res['status']) && $res['status'] === true && $res['data']['status'] === 'success') {
                $flwRef = $reference;
                $flwAmount = (float)($res['data']['amount'] / 100);
                $flwCurrency = $res['data']['currency'] ?? 'NGN';

                $stmt = $this->pdo->prepare("SELECT amount, currency FROM cp_transactions WHERE reference = ? AND status = 'pending'");
                $stmt->execute([$flwRef]);
                $txn = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($txn && $txn['amount'] == $flwAmount && $txn['currency'] == $flwCurrency) {
                    $stmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE reference = ?");
                    $stmt->execute([$flwRef]);
                    
                    $this->_createWallet($flwCurrency);

                    $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                    $stmt->execute([$flwAmount, $this->userId, $flwCurrency]);
                }
            }
        } elseif (($status == 'successful' || $status == 'completed') && !empty($transactionId)) {
            // Real Flutterwave Verification
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ''),
                    "Content-Type: application/json"
                ),
            ));
            
            $response = curl_exec($curl);
            $res = json_decode($response);

            if ($res && $res->status === 'success' && $res->data->status === 'successful') {
                $flwRef = $res->data->tx_ref;
                $flwAmount = $res->data->amount;
                $flwCurrency = $res->data->currency;

                // Check against pending transaction
                $stmt = $this->pdo->prepare("SELECT amount, currency FROM cp_transactions WHERE reference = ? AND status = 'pending'");
                $stmt->execute([$flwRef]);
                $txn = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($txn && $txn['amount'] == $flwAmount && $txn['currency'] == $flwCurrency) {
                    $stmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE reference = ?");
                    $stmt->execute([$flwRef]);
                    
                    $this->_createWallet($flwCurrency);

                    $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                    $stmt->execute([$flwAmount, $this->userId, $flwCurrency]);
                }
            }
        }

        header('Location: /pay?success=fund_complete');
    }

    public function transfer()
    {
        require __DIR__ . '/../Views/transfer.php';
    }

    public function processTransfer()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $type = $_POST['type'] ?? 'internal';
        $amount = $_POST['amount'];
        $currency = $_POST['currency'] ?? 'NGN'; // Sender currency

        if ($amount <= 0) {
            header("Location: /pay/transfer?error=Invalid amount");
            exit;
        }

        // Verify PIN
        $pin = $_POST['pin'] ?? '';
        $stmtPin = $this->pdo->prepare("SELECT transaction_pin FROM users WHERE id = ?");
        $stmtPin->execute([$this->userId]);
        $hashedPin = $stmtPin->fetchColumn();

        if (empty($pin) || !password_verify($pin, $hashedPin)) {
            // Usually we'd use session errors, but for simplicity we can redirect with a message
            header("Location: /pay/transfer?error=Invalid Transaction PIN");
            exit;
        }
        // 1. Debit Source Wallet
        $stmt = $this->pdo->prepare("SELECT balance FROM cp_wallets WHERE user_id = ? AND currency = ?");
        $stmt->execute([$this->userId, $currency]);
        $balance = $stmt->fetchColumn();

        if ($balance < $amount) {
            die("Insufficient Funds in $currency Wallet");
        }

        $this->pdo->beginTransaction();

        try {
            // Debit Sender
            $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ? AND currency = ?");
            $stmt->execute([$amount, $this->userId, $currency]);

            if ($type === 'internal') {
                $email = $_POST['email'];
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $recipientId = $stmt->fetchColumn();
    
                if (!$recipientId) throw new \Exception("User not found: $email");

                // Internal Transfer: Credit Recipient
                // Ensure recipient uses same currency or auto-convert? 
                // For simplicity MVP: Same currency transfer only.
                
                // Credit Recipient
                // Helper to create wallet if needed
                // We can't use $this->_createWallet logic easily inside transaction without refactor
                // So manual Insert Ignore
                $stmt = $this->pdo->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)");
                $stmt->execute([$this->tenantId, $recipientId, $currency]);

                $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                $stmt->execute([$amount, $recipientId, $currency]);

                $descSender = "Transfer to $email";
                $descreceiver = "Received from " . $this->_getUserEmail($this->userId);

            } elseif ($type === 'bank' || $type === 'momo') {
                if ($type === 'bank') {
                    $bankCode = $_POST['bank_code'];
                    $accNum = $_POST['account_number'];
                    $descSender = "Bank Transfer to $accNum ($bankCode)";
                } else {
                    $bankCode = $_POST['momo_provider'];
                    $accNum = $_POST['phone_number'];
                    $descSender = "MoMo Transfer to $accNum ($bankCode)";
                }
                
                $ref = 'TRF-' . uniqid();

                // Fetch payout provider setting
                $db = \App\Core\Database::getInstance()->getConnection();
                $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'payout_provider'");
                $providerSetting = $stmt->fetchColumn();
                $payoutProvider = $providerSetting ? strtolower($providerSetting) : 'flutterwave';
                
                if ($payoutProvider === 'strowallet') {
                    // Use StroWallet for Bank Transfer
                    $strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
                    
                    // 1. Resolve Account Name first
                    $nameRes = $strowallet->getAccountName($bankCode, $accNum, 'live');
                    if (!isset($nameRes['success']) || !$nameRes['success']) {
                        throw new \Exception("Could not verify account: " . ($nameRes['message'] ?? 'Unknown error'));
                    }
                    
                    $accountName = $nameRes['data']['account_name'] ?? '';
                    $nameRef = $nameRes['data']['name_enquiry_reference'] ?? ($nameRes['data']['session_id'] ?? ($nameRes['data']['sessionId'] ?? ''));
                    
                    if (empty($nameRef)) {
                        throw new \Exception("Bank account name enquiry reference is missing. API Response: " . json_encode($nameRes));
                    }
                    
                    // 2. Execute Transfer
                    $transferRes = $strowallet->bankTransfer(
                        $amount, 
                        $bankCode, 
                        $accNum, 
                        $descSender, 
                        $nameRef, 
                        $this->_getUserEmail($this->userId), // SenderName
                        'live'
                    );
                    
                    if (!isset($transferRes['success']) || !$transferRes['success']) {
                        throw new \Exception("Transfer Failed: " . ($transferRes['message'] ?? 'Unknown Error'));
                    }
                } else {
                    // Real Flutterwave Transfer API
                    $payload = [
                        "account_bank" => $bankCode,
                        "account_number" => $accNum,
                        "amount" => $amount,
                        "narration" => $descSender,
                        "currency" => $currency,
                        "reference" => $ref,
                        "debit_currency" => $currency
                    ];
    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.flutterwave.com/v3/transfers",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($payload),
                        CURLOPT_HTTPHEADER => array(
                            "Authorization: Bearer " . (defined('FLUTTERWAVE_SECRET_KEY') ? FLUTTERWAVE_SECRET_KEY : ''),
                            "Content-Type: application/json"
                        ),
                    ));
    
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
    
                    if ($err) {
                        throw new \Exception("Gateway Error: " . $err);
                    } else {
                        $res = json_decode($response);
                        if (!$res || $res->status !== 'success') {
                            throw new \Exception("Transfer Failed: " . ($res->message ?? 'Unknown Error'));
                        }
                    }
                }
            }

            // Record Sender Debit
            $ref = 'TRF-' . uniqid();
            $meta = json_encode(['type' => $type, 'details' => $_POST]);
            $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'debit', ?, ?, 'successful', ?, ?)");
            $stmt->execute([$this->tenantId, $this->userId, $ref, $amount, $currency, $descSender, $meta]);

            // If Internal, Record Recipient Credit
            if ($type === 'internal' && isset($recipientId)) {
                $ref2 = 'REC-' . uniqid();
                $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description) VALUES (?, ?, ?, 'credit', ?, ?, 'successful', ?)");
                $stmt->execute([$this->tenantId, $recipientId, $ref2, $amount, $currency, $descreceiver]);
            }

            $this->pdo->commit();
            header('Location: /pay?success=transfer_complete');

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            die("Transfer Failed: " . $e->getMessage());
        }
    }

    private function _getUserEmail($id) {
        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }
    private function _createWallet($currency)
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)");
        $stmt->execute([$this->tenantId, $this->userId, $currency]);
    }

    public function convert()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        $this->pdo = \App\Core\Database::getInstance()->getConnection();
        
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT * FROM cp_wallets WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $activeMenu = 'convert';
        $title = 'Convert Currency | Casjoe Pay';
        $pageTitle = 'Convert';

        require __DIR__ . '/../Views/convert.php';
    }
    public function getRateApi()
    {
        header('Content-Type: application/json');
        $from = $_GET['from'] ?? 'NGN';
        $to = $_GET['to'] ?? 'USD';

        $rates = [
            'USD' => 1.0,
            'NGN' => 1500.0,
            'GBP' => 0.78,
            'EUR' => 0.92,
            'GHS' => 15.0,
            'KES' => 130.0,
            'ZAR' => 18.0,
            'CJC' => 10.0
        ];

        if (isset($rates[$from]) && isset($rates[$to])) {
            $rate = $rates[$to] / $rates[$from];
            echo json_encode(['rate' => $rate]);
        } else {
            echo json_encode(['error' => 'Unsupported currency pair']);
        }
        exit;
    }

    public function processConvert()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        $this->pdo = Database::getInstance()->getConnection();
        
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        $from = $_POST['from_currency'] ?? '';
        $to = $_POST['to_currency'] ?? '';
        $amount = floatval($_POST['amount'] ?? 0);

        if ($amount <= 0) {
            header('Location: /pay/convert?error=Invalid amount');
            exit;
        }

        if ($from === $to) {
            header('Location: /pay/convert?error=Cannot convert to the same currency');
            exit;
        }

        $rates = [
            'USD' => 1.0,
            'NGN' => 1500.0,
            'GBP' => 0.78,
            'EUR' => 0.92,
            'GHS' => 15.0,
            'KES' => 130.0,
            'ZAR' => 18.0,
            'CJC' => 10.0
        ];

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            header('Location: /pay/convert?error=Unsupported currency pair');
            exit;
        }

        // 1. Check Source Balance
        $stmt = $this->pdo->prepare("SELECT balance FROM cp_wallets WHERE user_id = ? AND currency = ?");
        $stmt->execute([$this->userId, $from]);
        $balance = $stmt->fetchColumn();

        if ($balance < $amount) {
            header("Location: /pay/convert?error=Insufficient balance in $from wallet");
            exit;
        }

        $rate = $rates[$to] / $rates[$from];
        $targetAmount = $amount * $rate;

        $this->pdo->beginTransaction();
        try {
            // Debit Source
            $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ? AND currency = ?");
            $stmt->execute([$amount, $this->userId, $from]);

            // Credit Target (ensure wallet exists first)
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)");
            $stmt->execute([$this->tenantId, $this->userId, $to]);

            $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
            $stmt->execute([$targetAmount, $this->userId, $to]);

            // Record Source Debit Transaction
            $ref1 = 'CONV-OUT-' . uniqid();
            $meta1 = json_encode(['action' => 'convert', 'pair' => "$from-$to", 'rate' => $rate, 'target_amount' => $targetAmount]);
            $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'debit', ?, ?, 'successful', ?, ?)");
            $stmt->execute([$this->tenantId, $this->userId, $ref1, $amount, $from, "Convert $from to $to", $meta1]);

            // Record Target Credit Transaction
            $ref2 = 'CONV-IN-' . uniqid();
            $meta2 = json_encode(['action' => 'convert', 'pair' => "$from-$to", 'rate' => $rate, 'source_amount' => $amount]);
            $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'credit', ?, ?, 'successful', ?, ?)");
            $stmt->execute([$this->tenantId, $this->userId, $ref2, $targetAmount, $to, "Received from $from conversion", $meta2]);

            $this->pdo->commit();
            header('Location: /pay?success=conversion_complete');
            exit;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            header('Location: /pay/convert?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    public function createWallet()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = \App\Core\TenantContext::getTenantId();

        $currency = $_POST['currency'] ?? '';
        $allowed = ['USD', 'NGN', 'GBP', 'EUR', 'GHS', 'KES', 'ZAR', 'CJC'];
        
        if (in_array($currency, $allowed)) {
            $this->_createWallet($currency);
        }
        
        header('Location: /pay/convert?success=converted');
    }
    public function resolveAccount()
    {
        header('Content-Type: application/json');
        
        $bankCode = $_POST['bank_code'] ?? '';
        $accountNumber = $_POST['account_number'] ?? '';
        
        if (empty($bankCode) || empty($accountNumber)) {
            echo json_encode(['success' => false, 'message' => 'Missing bank code or account number']);
            exit;
        }

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'payout_provider'");
            $payoutProvider = $stmt->fetchColumn();
            if ($payoutProvider === 'strowallet') {
                $strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
                $res = $strowallet->getAccountName($bankCode, $accountNumber, 'live');
                
                if (isset($res['success']) && $res['success']) {
                    echo json_encode([
                        'success' => true,
                        'account_name' => $res['data']['account_name'] ?? 'Unknown Name',
                        'name_enquiry_reference' => $res['data']['name_enquiry_reference'] ?? ($res['data']['session_id'] ?? ($res['data']['sessionId'] ?? ''))
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $res['message'] ?? 'Unable to resolve account'
                    ]);
                }
            } else {
                // Future Flutterwave logic
                echo json_encode([
                    'success' => true, 
                    'account_name' => 'Flutterwave Account (Name Hidden)'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function getBanks()
    {
        // Suppress errors breaking JSON
        ini_set('display_errors', 0);
        error_reporting(0);
        header('Content-Type: application/json');
        
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'payout_provider'");
            $payoutProvider = $stmt->fetchColumn();
            
            if ($payoutProvider === 'strowallet') {
                $strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
                $res = $strowallet->getBanks();
                if (isset($res['success']) && $res['success']) {
                    echo json_encode(['success' => true, 'data' => $res['data'] ?? []]);
                } else {
                    echo json_encode(['success' => false, 'message' => $res['message'] ?? 'Unable to fetch banks']);
                }
            } else {
                echo json_encode(['success' => true, 'data' => [
                    ['bankCode' => '044', 'bankName' => 'Access Bank'],
                    ['bankCode' => '058', 'bankName' => 'GTBank'],
                    ['bankCode' => '033', 'bankName' => 'UBA'],
                    ['bankCode' => '057', 'bankName' => 'Zenith Bank'],
                    ['bankCode' => '011', 'bankName' => 'First Bank']
                ]]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

}

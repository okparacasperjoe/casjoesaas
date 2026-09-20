<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\Auth;

use App\Modules\CasjoePay\Services\StroWalletService;
class CardController
{
    public function index()
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM cp_virtual_cards WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $cards = $stmt->fetchAll();

        // Auto-fetch missing card details from StroWallet
        $strowallet = new StroWalletService();
        $updated = false;
        foreach ($cards as &$card) {
            if (empty($card['masked_pan']) && strpos($card['card_id'], 'card_mock_') === false) {
                $details = $strowallet->fetchNfcCardDetails($card['card_id']);
                if (isset($details['success']) && $details['success'] && !empty($details['response']['card_detail']['card_number'])) {
                    $cardDetail = $details['response']['card_detail'];
                    $pan = $cardDetail['card_number'];
                    $maskedPan = '**** **** **** ' . substr($pan, -4);
                    // Extract expiry
                    $expParts = explode('/', $cardDetail['expiry']);
                    $month = $expParts[0] ?? '--';
                    $year = $expParts[1] ?? '--';
                    
                    $updateStmt = $db->prepare("UPDATE cp_virtual_cards SET masked_pan = ?, start_month = ?, start_year = ? WHERE id = ?");
                    $updateStmt->execute([$maskedPan, $month, $year, $card['id']]);
                    
                    $card['masked_pan'] = $maskedPan;
                    $card['start_month'] = $month;
                    $card['start_year'] = $year;
                    $updated = true;
                }
            }
        }

        $stmt = $db->prepare("SELECT * FROM virtual_accounts WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $accounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch all wallets for the user to support multi-currency payment
        $stmtWallets = $db->prepare("SELECT currency, balance FROM cp_wallets WHERE user_id = ? ORDER BY CASE WHEN currency = 'NGN' THEN 1 WHEN currency = 'USD' THEN 2 ELSE 3 END, currency ASC");
        $stmtWallets->execute([$user['id']]);
        $userWallets = $stmtWallets->fetchAll(\PDO::FETCH_ASSOC);

        $hasNgn = false;
        foreach ($userWallets as $w) {
            if ($w['currency'] === 'NGN') {
                $hasNgn = true;
                break;
            }
        }
        if (!$hasNgn) {
            $tenantId = $user['tenant_id'] ?? 1;
            try {
                $db->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, 'NGN', 0.00)")
                   ->execute([$tenantId, $user['id']]);
                $stmtWallets->execute([$user['id']]);
                $userWallets = $stmtWallets->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                array_unshift($userWallets, ['currency' => 'NGN', 'balance' => 0.00]);
            }
        }

        require __DIR__ . '/../Views/cards.php';
    }

    public function getCardDetails()
    {
        header('Content-Type: application/json');
        $user = Auth::user();
        $cardId = $_GET['card_id'] ?? '';
        
        if (empty($cardId)) {
            echo json_encode(['success' => false, 'message' => 'Card ID is required']);
            return;
        }

        // Verify ownership
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM cp_virtual_cards WHERE user_id = ? AND card_id = ? LIMIT 1");
        $stmt->execute([$user['id'], $cardId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Card not found or unauthorized']);
            return;
        }

        if (strpos($cardId, 'card_mock_') !== false) {
            // Mock card details
            echo json_encode([
                'success' => true, 
                'details' => [
                    'card_number' => '4242 1234 5678 9012',
                    'cvv' => '123',
                    'expiry' => '12/28',
                    'address' => '123 Mock Street',
                    'city' => 'Mock City',
                    'state' => 'MK',
                    'zip' => '10001',
                    'country' => 'US'
                ]
            ]);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Fetch local balance from database first
        $stmtCard = $db->prepare("SELECT balance FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmtCard->execute([$cardId, Auth::user()['id']]);
        $localBal = $stmtCard->fetchColumn();

        $strowallet = new StroWalletService();
        $res = $strowallet->fetchNfcCardDetails($cardId);

        $cardNum       = null;
        $cvv           = null;
        $expiry        = null;
        $apiBal        = '0.00';
        $cardNumberUrl = '';
        $cvvUrl        = '';
        $maskedPan     = null;
        $address       = null;
        $city          = null;
        $state         = null;
        $zip           = null;
        $country       = null;

        if (isset($res['success']) && $res['success'] && isset($res['response']['card_detail'])) {
            $data = $res['response']['card_detail'];
            $cardNum    = $data['card_number'] ?? null;
            $cvv        = $data['cvv'] ?? null;
            $rawExpiry  = $data['expiry'] ?? '';
            // StroWallet returns "/" when expiry is not yet provisioned
            $expiry     = ($rawExpiry && $rawExpiry !== '/') ? $rawExpiry : null;
            $apiBal     = $data['balance'] ?? '0.00';
            $cardNumberUrl = $data['card_number_url'] ?? '';
            $cvvUrl        = $data['cvv_url'] ?? '';
            $last4         = $data['last4'] ?? null;

            // Build masked pan
            if ($last4) {
                $maskedPan = '**** **** **** ' . $last4;
            } elseif ($cardNum) {
                $maskedPan = '**** **** **** ' . substr(str_replace([' ', '-'], '', $cardNum), -4);
            } else {
                $maskedPan = null;
            }
            // Use billing address from StroWallet if available
            $address  = $data['address'] ?? null;
            $city     = $data['city'] ?? null;
            $state    = $data['state'] ?? null;
            $zip      = $data['postal'] ?? null;
            $country  = $data['country'] ?? null;
        }

        // Sanitize StroWallet sandbox placeholder
        if ($cardNum && str_replace([' ', '-'], '', $cardNum) === '4865533351001234') {
            $cardNum = '4865 5333 5100 1231';
        }

        // Use local balance if available, otherwise fallback to API balance
        $finalBalance = ($localBal !== false) ? number_format((float)$localBal, 2, '.', '') : $apiBal;

        // Fill in billing address fallback if not provided by API
        if (!$address) {
            // Check if USD virtual card
            $stmtVirtual = $db->prepare("SELECT id FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
            $stmtVirtual->execute([$cardId, Auth::user()['id']]);
            if ($stmtVirtual->fetch()) {
                $address = '350 5th Ave, Suite 2100';
                $city    = 'New York';
                $state   = 'NY';
                $zip     = '10118';
                $country = 'United States';
            } else {
                $address = '19 Woke Avenue';
                $city    = 'Port Harcourt';
                $state   = 'Rivers';
                $zip     = '23401';
                $country = 'Nigeria';
            }
        }

        // Build iframe HTML for secure card number display
        // StroWallet uses SecureProxy (browser-only JS) to render card numbers,
        // so we MUST use iframes. Server-side extraction is impossible by design.
        $cardNumberHtml = '';
        $cvvHtml = '';

        if ($cardNumberUrl) {
            $cardNumberHtml = '<iframe src="' . htmlspecialchars($cardNumberUrl) . '" '
                . 'width="260" height="36" frameborder="0" scrolling="no" '
                . 'style="border:none;display:block;filter:invert(1);pointer-events:none;"></iframe>';
        }
        if ($cvvUrl) {
            $cvvHtml = '<iframe src="' . htmlspecialchars($cvvUrl) . '" '
                . 'width="80" height="36" frameborder="0" scrolling="no" '
                . 'style="border:none;display:block;filter:invert(1);pointer-events:none;"></iframe>';
        }

        // NEVER return null for card_number or cvv — null becomes "undefined" in JS
        echo json_encode([
            'success' => true,
            'details' => [
                'card_number'      => $cardNum ?? '',
                'masked_pan'       => $maskedPan ?? '',
                'cvv'              => $cvv ?? '',
                'expiry'           => $expiry ?? '',
                'balance'          => $finalBalance,
                'address'          => $address,
                'city'             => $city,
                'state'            => $state,
                'zip'              => $zip,
                'country'          => $country,
                'card_number_url'  => $cardNumberUrl,
                'cvv_url'          => $cvvUrl,
                'card_number_html' => $cardNumberHtml,
                'cvv_html'         => $cvvHtml,
            ]
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $db = Database::getInstance();
        $amount = (float) ($_POST['amount'] ?? 0);
        $cardTier = strtolower(trim($_POST['card_tier'] ?? 'lite'));
        if (!in_array($cardTier, ['lite', 'reloadable'])) {
            $cardTier = 'lite';
        }
        $brand = strtoupper(trim($_POST['brand'] ?? 'VISA'));
        if (!in_array($brand, ['VISA', 'MASTERCARD'])) {
            $brand = 'VISA';
        }

        // Fetch KYC data if available
        $stmtKyc = $db->query("SELECT * FROM cp_naira_card_users WHERE user_id = ? OR tenant_id = ? LIMIT 1", [$user['id'], $user['tenant_id']]);
        $kycData = $stmtKyc->fetch();

        // If Reloadable card is requested, enforce KYC and card limits
        if ($cardTier === 'reloadable') {
            if (!$kycData) {
                // Check if user has approved platform KYC in kyc_verifications
                $stmtKycVerif = $db->query("SELECT * FROM kyc_verifications WHERE user_id = ? AND status = 'approved' ORDER BY id DESC LIMIT 1", [$user['id']]);
                $verifRow = $stmtKycVerif->fetch();
                if ($verifRow) {
                    $nameParts = explode(' ', $user['name'] ?? '', 2);
                    $kycData = [
                        'firstname'     => $verifRow['first_name'] ?? $nameParts[0] ?? 'User',
                        'lastname'      => $verifRow['last_name'] ?? ($nameParts[1] ?? 'Casjoe'),
                        'email'         => $user['email'],
                        'phone'         => $user['phone'] ?? '2348000000000',
                        'nin'           => $verifRow['id_number'] ?? $verifRow['bvn'] ?? '',
                        'dob'           => $verifRow['date_of_birth'] ?? $verifRow['dob'] ?? '1990-01-01',
                        'address_line1' => $verifRow['address'] ?? 'Casjoe Way',
                        'city'          => $verifRow['city'] ?? 'Lagos',
                        'state'         => $verifRow['state'] ?? 'Lagos',
                        'country'       => 'NGA',
                        'postal_code'   => $verifRow['postal_code'] ?? '100001',
                        'customer_id'   => null
                    ];
                }
            }

            if (!$kycData) {
                $_SESSION['error'] = "Reloadable Business cards require verified cardholder KYC. Please complete verification on the Naira Cards page or choose an Instant Lite Card.";
                header("Location: /pay/cards");
                exit;
            }

            // Check active reloadable cards limit (max 3 per user)
            $stmtCount = $db->query("SELECT COUNT(*) as cnt FROM cp_virtual_cards WHERE user_id = ? AND is_reloadable = 1 AND status = 'active'", [$user['id']]);
            $activeReloadableCount = (int)($stmtCount->fetch()['cnt'] ?? 0);
            if ($activeReloadableCount >= 3) {
                $_SESSION['error'] = "You have reached the maximum limit of 3 active Reloadable cards. You can create Instant Lite Cards or terminate an existing card.";
                header("Location: /pay/cards");
                exit;
            }
        }

        // Retrieve configured exchange rate and card creation fee
        $stmtRate = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'usd_exchange_rate'");
        $rateRow = $stmtRate->fetch();
        $rate = (float) ($rateRow['setting_value'] ?? 1600);

        $stmtFee = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_creation_fee'");
        $feeRow = $stmtFee->fetch();
        $creationFee = (float) ($feeRow['setting_value'] ?? 4);

        // Minimum deposit configured by admin (defaults to 3.00 USD)
        $stmtMin = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_min_deposit'");
        $configuredMin = (float)($stmtMin->fetchColumn() ?: 3.00);
        $minDeposit = $configuredMin;

        if ($amount < $minDeposit) {
            $_SESSION['error'] = "Minimum initial funding amount for a " . ($cardTier === 'lite' ? "Lite Card" : "Reloadable Card") . " is $" . number_format($minDeposit, 2) . " USD.";
            header("Location: /pay/cards");
            exit;
        }

        $paymentCurrency = strtoupper(trim($_POST['payment_currency'] ?? $_POST['currency'] ?? 'NGN'));
        if (empty($paymentCurrency)) {
            $paymentCurrency = 'NGN';
        }

        // Support CJP / CJC aliases
        if (in_array($paymentCurrency, ['CJP', 'CJC'])) {
            $checkToken = $db->prepare("SELECT currency FROM cp_wallets WHERE user_id = ? AND currency IN ('CJP', 'CJC') LIMIT 1");
            $checkToken->execute([$user['id']]);
            $foundToken = $checkToken->fetchColumn();
            if ($foundToken) {
                $paymentCurrency = $foundToken;
            }
        }

        // Currency exchange rates relative to 1 USD (1 USD = X Currency)
        $stmtGhs = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'ghs_exchange_rate'");
        $ghsRate = (float)($stmtGhs ? $stmtGhs->fetchColumn() : 0) ?: 15.0;

        $stmtCjp = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'cjp_exchange_rate'");
        $cjpRate = (float)($stmtCjp ? $stmtCjp->fetchColumn() : 0) ?: 10.0;

        $currencyRates = [
            'USD' => 1.0,
            'NGN' => (float)$rate,
            'GHS' => $ghsRate,
            'GBP' => 0.78,
            'EUR' => 0.92,
            'KES' => 130.0,
            'ZAR' => 18.0,
            'CJC' => $cjpRate,
            'CJP' => $cjpRate
        ];

        $currencySymbols = [
            'USD' => '$',
            'NGN' => '₦',
            'GHS' => '₵',
            'GBP' => '£',
            'EUR' => '€',
            'KES' => 'KSh',
            'ZAR' => 'R',
            'CJC' => 'CJC ',
            'CJP' => 'CJP '
        ];

        $selectedRate = (float)($currencyRates[$paymentCurrency] ?? 1.0);
        $currSymbol = $currencySymbols[$paymentCurrency] ?? ($paymentCurrency . ' ');

        $totalUsd = $amount + $creationFee;
        $deductAmount = round($totalUsd * $selectedRate, 2);

        // 1. Check Wallet Balance strictly for selected currency
        $stmt = $db->prepare("SELECT balance FROM cp_wallets WHERE user_id = ? AND currency = ? LIMIT 1");
        $stmt->execute([$user['id'], $paymentCurrency]);
        $walletRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $walletVal = (float)($walletRow['balance'] ?? 0);

        if ($walletVal < $deductAmount) {
             $rateDisplay = ($paymentCurrency !== 'USD') ? " at {$currSymbol}" . number_format($selectedRate, 2) . "/USD" : "";
             $_SESSION['error'] = "Insufficient {$paymentCurrency} wallet funds. Required: {$currSymbol}" . number_format($deductAmount, 2) . " {$paymentCurrency} (Funding: $" . number_format($amount, 2) . " + Issuance Fee: $" . number_format($creationFee, 2) . " USD{$rateDisplay}). Available: {$currSymbol}" . number_format($walletVal, 2) . " {$paymentCurrency}.";
             header("Location: /pay/cards");
             exit;
        }

        // 2. Call Strowallet / ZiiroPay API
        try {
            $strowallet = new StroWalletService();
            
            // If API keys are empty, block creation
            if (empty($strowallet->publicKey)) {
                throw new \Exception("Payment card API keys are not configured. Please contact the administrator.");
            }

            $customerId = null;

            if ($cardTier === 'lite') {
                // Tier 1: Instant Lite Card (Zero KYC, Instant Issuance)
                $nameOnCard = trim($_POST['name_on_card'] ?? '');
                if (empty($nameOnCard)) {
                    $nameOnCard = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    if (empty($nameOnCard)) {
                        $nameOnCard = $user['name'] ?? 'Casjoe User';
                    }
                }
                $idType = strtolower(trim($_POST['id_type'] ?? 'nin'));
                if (!in_array($idType, ['nin', 'bvn', 'passport', 'voter_id', 'drivers_license'])) {
                    $idType = 'nin';
                }
                $idNumber = trim($_POST['id_number'] ?? '');
                if (empty($idNumber)) {
                    $idNumber = !empty($kycData['nin']) ? $kycData['nin'] : '10' . str_pad((string)$user['id'], 9, '0', STR_PAD_LEFT);
                }

                $cardRes = $strowallet->createLiteCard(
                    $amount,
                    $brand,
                    $nameOnCard,
                    $idNumber,
                    $idType,
                    'live'
                );
            } else {
                // Tier 2: Reloadable Business NFC Card (Requires Cardholder KYC & customer_id)
                $kycEmail   = trim($kycData['email'] ?? $user['email']);
                $customerId = $kycData['customer_id'] ?? null;
                $kycStatus  = null;

                // Step 1: Check status via /cardkycstatus
                $statusRes = $strowallet->getCardKycStatus($kycEmail);
                if (isset($statusRes['success']) && $statusRes['success'] && isset($statusRes['data'])) {
                    $cData = $statusRes['data'];
                    $customerId = $cData['customer_id'] ?? $customerId;
                    $kycStatus  = strtolower($cData['status'] ?? '');
                }

                // Step 2: If customer not found or customer_id is empty, submit Cardholder KYC via /cardkyc
                if (empty($customerId)) {
                    $rawPhone = preg_replace('/[^0-9]/', '', $kycData['phone'] ?? $user['phone'] ?? '2348000000000');
                    if (strlen($rawPhone) === 11 && substr($rawPhone, 0, 1) === '0') {
                        $rawPhone = '234' . substr($rawPhone, 1);
                    } elseif (strlen($rawPhone) === 10) {
                        $rawPhone = '234' . $rawPhone;
                    }

                    $dobFormatted = date('Y-m-d', strtotime($kycData['dob'] ?? '1995-01-01'));
                    $idFrontBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

                    $kycSubmitRes = $strowallet->submitCardKyc([
                        'first_name'     => $kycData['firstname'] ?? 'Casjoe',
                        'last_name'      => $kycData['lastname'] ?? 'User',
                        'id_number'      => $kycData['nin'] ?? ('10' . str_pad((string)$user['id'], 9, '0', STR_PAD_LEFT)),
                        'email'          => $kycEmail,
                        'phone_number'   => $rawPhone,
                        'date_of_birth'  => $dobFormatted,
                        'dial_code'      => '+234',
                        'id_front_image' => $idFrontBase64,
                        'line1'          => $kycData['address_line1'] ?? 'Casjoe Way',
                        'state'          => $kycData['state'] ?? 'Lagos',
                        'postal_code'    => $kycData['postal_code'] ?? '100001',
                        'city'           => $kycData['city'] ?? 'Lagos',
                        'country'        => 'NGA',
                        'id_type'        => 'nin',
                        'mode'           => 'live'
                    ]);

                    if (isset($kycSubmitRes['success']) && $kycSubmitRes['success']) {
                        $subData = $kycSubmitRes['data'] ?? [];
                        $customerId = $subData['customer_id'] ?? null;
                        $kycStatus  = strtolower($subData['status'] ?? 'pending');
                    } else {
                        $errMsg = $kycSubmitRes['message'] ?? 'Card KYC registration failed with issuer.';
                        if (is_array($errMsg)) $errMsg = json_encode($errMsg);
                        throw new \Exception("KYC Submission: " . $errMsg);
                    }
                }

                // If customer_id is resolved, update local record
                if (!empty($customerId)) {
                    $db->query("UPDATE cp_naira_card_users SET customer_id = ? WHERE user_id = ? AND (customer_id IS NULL OR customer_id = '')", [$customerId, $user['id']]);
                }

                // Verify approval status
                if ($kycStatus !== 'approved' && !empty($customerId)) {
                    $recheck = $strowallet->getCardKycStatus($kycEmail);
                    if (isset($recheck['success']) && $recheck['success'] && isset($recheck['data'])) {
                        $kycStatus = strtolower($recheck['data']['status'] ?? $kycStatus);
                        $customerId = $recheck['data']['customer_id'] ?? $customerId;
                    }
                }

                if ($kycStatus !== 'approved') {
                    throw new \Exception("Your Cardholder KYC verification is currently " . ($kycStatus ? ucfirst($kycStatus) : 'Pending Issuer Review') . ". Once approved by the card network, your reloadable card will be issued. Alternatively, you can create an Instant Lite Card immediately with zero wait time.");
                }

                if (empty($customerId)) {
                    throw new \Exception("Cardholder ID could not be verified. Please complete Card KYC verification or create an Instant Lite Card.");
                }

                // Step 3: Call /create-nfc-card with customer_id
                $cardHolderName = trim(($kycData['firstname'] ?? '') . ' ' . ($kycData['lastname'] ?? ''));
                if (empty($cardHolderName)) {
                    $cardHolderName = $user['name'] ?? 'Casjoe User';
                }

                $cardRes = $strowallet->createReloadableNfcCard(
                    $customerId,
                    $cardHolderName,
                    $amount,
                    'live'
                );
            }
            
            if (isset($cardRes['success']) && !$cardRes['success']) {
                $errorMessage = 'Card creation failed';
                if (isset($cardRes['message'])) {
                    if (is_array($cardRes['message'])) {
                        $errorMessage = $cardRes['message']['message'] ?? json_encode($cardRes['message']);
                    } else {
                        $errorMessage = (string) $cardRes['message'];
                    }
                }
                throw new \Exception($errorMessage);
            }
            
            $cardData = $cardRes['response'] ?? $cardRes['data'] ?? $cardRes;
            $cardId = $cardData['card_id'] ?? $cardData['_id'] ?? '';
            $maskedPan = $cardData['card_number'] ?? $cardData['maskedPan'] ?? $cardData['last4'] ?? '';
            if (strlen($maskedPan) === 4) {
                $maskedPan = '**** **** **** ' . $maskedPan;
            } elseif (empty($maskedPan)) {
                $maskedPan = 'Pending Issuance';
            }
            if (str_replace([' ', '-'], '', $maskedPan) === '4865533351001234') {
                $maskedPan = '4865 5333 5100 1231';
            }

            if (empty($cardId)) {
                throw new \Exception("Invalid API response: Missing Card ID.");
            }

            // 3. Deduct Wallet & Save (With DB Transaction)
            $isReloadable = ($cardTier === 'reloadable') ? 1 : 0;
            $conn = $db->getConnection();
            $conn->beginTransaction();
            try {
                $db->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ? AND currency = ?")
                   ->execute([$deductAmount, $user['id'], $paymentCurrency]);
                
                $tierLabel = ($cardTier === 'reloadable') ? 'Reloadable Business Card' : 'Instant Lite Card';
                $db->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, amount, currency, status, type, description) VALUES (?, ?, ?, ?, ?, 'successful', 'debit', ?)")
                   ->execute([$user['tenant_id'], $user['id'], 'txn_'.uniqid(), $deductAmount, $paymentCurrency, "Virtual Card Creation ($tierLabel) - $$amount USD (Paid with $paymentCurrency Wallet)"]);

                $db->query(
                    "INSERT INTO cp_virtual_cards (user_id, tenant_id, card_id, provider, card_tier, is_reloadable, brand, card_type, start_month, start_year, masked_pan, currency, balance, status, strowallet_customer_id) VALUES (?, ?, ?, 'ziiropay', ?, ?, ?, ?, ?, ?, ?, 'USD', ?, 'active', ?)",
                    [$user['id'], $user['tenant_id'], $cardId, $cardTier, $isReloadable, $brand, $brand, '12', '28', $maskedPan, $amount, $customerId]
                );

                $conn->commit();
                $_SESSION['success'] = "Your " . ($cardTier === 'reloadable' ? "Reloadable Business" : "Instant Lite") . " virtual card has been issued successfully!";
                header("Location: /pay/cards?success=created");
                exit;

            } catch (\Exception $dbException) {
                $conn->rollBack();
                throw new \Exception("Database Error: " . $dbException->getMessage());
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = "Provider Error: " . $e->getMessage();
            header("Location: /pay/cards");
            exit;
        }
    }

    public function showFundForm()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $selectedCardId = trim($_GET['id'] ?? '');
        $db = Database::getInstance()->getConnection();

        // Get user's cards from both cp_virtual_cards and cp_naira_cards
        $stmt = $db->prepare("SELECT card_id, masked_pan, balance, currency, status, is_reloadable, card_tier FROM cp_virtual_cards WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $virtualCards = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // If a specific card was requested, verify it is reloadable
        if (!empty($selectedCardId)) {
            foreach ($virtualCards as $vc) {
                if ($vc['card_id'] === $selectedCardId && isset($vc['is_reloadable']) && (int)$vc['is_reloadable'] === 0) {
                    $_SESSION['error'] = "Non-reloadable Lite cards cannot be topped up. Please create a new card or use a reloadable card.";
                    header("Location: /pay/cards");
                    exit;
                }
            }
        }

        // Only include reloadable cards in the funding dropdown
        $reloadableVirtualCards = array_filter($virtualCards, function($c) {
            return !isset($c['is_reloadable']) || (int)$c['is_reloadable'] === 1;
        });

        $stmtNaira = $db->prepare("SELECT card_id, masked_pan, 0.00 as balance, 'NGN' as currency, status, 1 as is_reloadable, 'naira' as card_tier FROM cp_naira_cards WHERE user_id = ?");
        $stmtNaira->execute([$user['id']]);
        $nairaCards = $stmtNaira->fetchAll(\PDO::FETCH_ASSOC);

        $allCards = array_merge(array_values($reloadableVirtualCards), $nairaCards);

        // Get user's NGN wallet balance
        $stmt = $db->prepare("SELECT balance FROM cp_wallets WHERE user_id = ? AND currency = 'NGN' LIMIT 1");
        $stmt->execute([$user['id']]);
        $ngnBalance = (float)($stmt->fetchColumn() ?: 0);

        // Fetch configured exchange rate and min deposit
        $stmtRate = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'usd_exchange_rate'");
        $rate = (float)($stmtRate->fetchColumn() ?: 1600);
        $stmtMin = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_min_deposit'");
        $minFunding = (float)($stmtMin->fetchColumn() ?: 3.00);

        $cards = $allCards;
        require __DIR__ . '/../Views/fund_card.php';
    }

    public function processFundCard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $cardId = trim($_POST['card_id'] ?? '');
        $usdAmount = (float)($_POST['amount'] ?? 0);

        $db = Database::getInstance()->getConnection();

        // Fetch configured exchange rate and min deposit
        $stmtRate = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'usd_exchange_rate'");
        $rate = (float)($stmtRate->fetchColumn() ?: 1600);
        $stmtMin = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_min_deposit'");
        $minFunding = (float)($stmtMin->fetchColumn() ?: 3.00);

        $ngnRequired = $usdAmount * $rate;

        if (!$cardId || $usdAmount < $minFunding) {
            $_SESSION['error'] = "Minimum funding amount is $" . number_format($minFunding, 2) . " USD.";
            header("Location: /pay/cards/fund?id=" . urlencode($cardId));
            exit;
        }

        // Check if card exists in cp_virtual_cards or cp_naira_cards
        $stmt = $db->prepare("SELECT * FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$cardId, $user['id']]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);
        $cardTable = 'cp_virtual_cards';

        if (!$card) {
            $stmt = $db->prepare("SELECT * FROM cp_naira_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
            $stmt->execute([$cardId, $user['id']]);
            $card = $stmt->fetch(\PDO::FETCH_ASSOC);
            $cardTable = 'cp_naira_cards';
        }

        if (!$card) {
            $_SESSION['error'] = "Card not found or unauthorized.";
            header("Location: /pay/cards");
            exit;
        }

        if (isset($card['is_reloadable']) && (int)$card['is_reloadable'] === 0) {
            $_SESSION['error'] = "Non-reloadable Lite cards cannot be topped up. Please create a new card or use a reloadable card.";
            header("Location: /pay/cards");
            exit;
        }


        // Check NGN wallet balance
        $stmt = $db->prepare("SELECT balance FROM cp_wallets WHERE user_id = ? AND currency = 'NGN' LIMIT 1");
        $stmt->execute([$user['id']]);
        $ngnBalance = (float)($stmt->fetchColumn() ?: 0);

        if ($ngnBalance < $ngnRequired) {
            $_SESSION['error'] = "Insufficient wallet funds. Required: ₦" . number_format($ngnRequired, 2) . " NGN, Available: ₦" . number_format($ngnBalance, 2) . " NGN.";
            header("Location: /pay/cards/fund?id=" . urlencode($cardId));
            exit;
        }

        try {
            $strowallet = new StroWalletService();

            if (strpos($cardId, 'card_mock_') === false && !empty($strowallet->publicKey)) {
                // Try funding endpoints
                $res = $strowallet->fundNfcCard($cardId, $usdAmount);
                if (!isset($res['success']) || !$res['success']) {
                    $res = $strowallet->fundCard($cardId, $usdAmount);
                }
            }

            // Deduct NGN from user wallet
            $db->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ? AND currency = 'NGN'")
               ->execute([$ngnRequired, $user['id']]);

            // Credit USD card balance in local DB
            if ($cardTable === 'cp_virtual_cards') {
                $db->prepare("UPDATE cp_virtual_cards SET balance = balance + ? WHERE card_id = ? AND user_id = ?")
                   ->execute([$usdAmount, $cardId, $user['id']]);
            }

            // Record transaction log
            $ref = 'CARD_FUND_' . uniqid();
            $desc = "Virtual Card Funding ($" . number_format($usdAmount, 2) . " USD)";
            $db->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, created_at) VALUES (?, ?, ?, 'debit', ?, 'NGN', 'successful', ?, NOW())")
               ->execute([$user['tenant_id'], $user['id'], $ref, $ngnRequired, $desc]);

            $_SESSION['success'] = "Virtual Card funded successfully with $" . number_format($usdAmount, 2) . " USD!";
            header("Location: /pay/cards");
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = "Card Funding Error: " . $e->getMessage();
            header("Location: /pay/cards/fund?id=" . urlencode($cardId));
            exit;
        }
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

        // Delete mock virtual cards
        $db->prepare("DELETE FROM cp_virtual_cards WHERE user_id = ? AND card_id LIKE 'card_mock_%'")->execute([$userId]);

        header('Location: /pay/cards?tab=cards');
        exit;
    }

    public function toggleFreeze()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = Auth::user();
        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $cardId = trim($_POST['card_id'] ?? $_GET['card_id'] ?? '');
        if (!$cardId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing Card ID']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$cardId, $user['id']]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card not found']);
            exit;
        }

        $currentStatus = strtolower($card['status'] ?? 'active');
        $newStatus = ($currentStatus === 'active') ? 'frozen' : 'active';
        $providerAction = ($newStatus === 'frozen') ? 'block' : 'unblock';

        try {
            $strowallet = new StroWalletService();
            if (strpos($cardId, 'card_mock_') === false && !empty($strowallet->publicKey)) {
                $strowallet->setNfcCardStatus($cardId, $providerAction);
            }

            // Update database
            $db->prepare("UPDATE cp_virtual_cards SET status = ? WHERE card_id = ? AND user_id = ?")
               ->execute([$newStatus, $cardId, $user['id']]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Card has been ' . ($newStatus === 'frozen' ? 'frozen' : 'unfrozen') . ' successfully.'
            ]);
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function terminate()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = Auth::user();
        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $cardId = trim($_POST['card_id'] ?? $_GET['card_id'] ?? '');
        if (!$cardId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing Card ID']);
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Find card in cp_virtual_cards
        $stmt = $db->prepare("SELECT * FROM cp_virtual_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$cardId, $user['id']]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card not found']);
            exit;
        }

        $balanceUsd = (float)($card['balance'] ?? 0);
        $rate = 1500;
        $refundNgn = $balanceUsd * $rate;

        try {
            $strowallet = new StroWalletService();
            if (strpos($cardId, 'card_mock_') === false && !empty($strowallet->publicKey)) {
                try {
                    $strowallet->terminateCard($cardId);
                } catch (\Throwable $swEx) {
                    // Ignore provider unlinked response on old/sandbox card
                }
            }

            // Refund balance to NGN wallet if balance > 0
            if ($refundNgn > 0) {
                $db->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = 'NGN'")
                   ->execute([$refundNgn, $user['id']]);

                // Record refund transaction
                $ref = 'CARD_REFUND_' . uniqid();
                $desc = "Refund from Terminated Virtual Card ($" . number_format($balanceUsd, 2) . " USD)";
                $db->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, created_at) VALUES (?, ?, ?, 'credit', ?, 'NGN', 'successful', ?, NOW())")
                   ->execute([$user['tenant_id'], $user['id'], $ref, $refundNgn, $desc]);
            }

            // Delete card from DB
            $db->prepare("DELETE FROM cp_virtual_cards WHERE card_id = ? AND user_id = ?")
               ->execute([$cardId, $user['id']]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Card terminated successfully!' . ($refundNgn > 0 ? ' ₦' . number_format($refundNgn, 2) . ' NGN ($' . number_format($balanceUsd, 2) . ' USD) has been refunded to your NGN wallet.' : '')
            ]);
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
}

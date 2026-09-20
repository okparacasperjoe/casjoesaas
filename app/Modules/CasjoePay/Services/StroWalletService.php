<?php

namespace App\Modules\CasjoePay\Services;

class StroWalletService
{
    public $publicKey;
    public $strowalletPublicKey;
    public $ziiropayPublicKey;
    private $secretKey;
    private $baseUrl = 'https://ziiropay.com/api/bitvcard'; // Migrated to ZiiroPay by StroWallet

    public function __construct()
    {
        // 1. Resolve StroWallet Nigeria key (starts with pub_... for virtual bank, naira cards, transfers)
        $strowalletKey = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : ($_ENV['STROWALLET_PUBLIC_KEY'] ?? '');
        if (empty($strowalletKey) || strpos($strowalletKey, 'pub_') !== 0) {
            $strowalletKey = $_ENV['STROWALLET_PUBLIC_KEY'] ?? 'pub_IVIyJxJlwAcp4MK27MiHb4MRSKadXBF4ymGBLWJd';
        }
        $this->strowalletPublicKey = $strowalletKey;

        // 2. Resolve ZiiroPay USD Card key (for USD virtual cards, NFC cards, cardkyc)
        $ziiroKey = defined('ZIIROPAY_PUBLIC_KEY') ? ZIIROPAY_PUBLIC_KEY : ($_ENV['ZIIROPAY_PUBLIC_KEY'] ?? '');
        if (empty($ziiroKey)) {
            $rawStro = defined('STROWALLET_PUBLIC_KEY') ? STROWALLET_PUBLIC_KEY : '';
            if (!empty($rawStro) && strpos($rawStro, 'pub_') !== 0) {
                $ziiroKey = $rawStro;
            } else {
                $ziiroKey = '55acb03a4ff77aa26c05a1ab0ab5c7e661871743';
            }
        }
        $this->ziiropayPublicKey = $ziiroKey;

        // Default $this->publicKey is set to ZiiroPay key for USD cards compatibility
        $this->publicKey = $this->ziiropayPublicKey;
        $this->secretKey = $_ENV['STROWALLET_SECRET_KEY'] ?? (defined('STROWALLET_SECRET_KEY') ? STROWALLET_SECRET_KEY : '');
    }

    public function request($endpoint, $method = 'GET', $data = [])
    {
        // Intercept and handle mock card IDs
        $cardId = $data['card_id'] ?? null;
        if (!$cardId) {
            if (preg_match('/card_id=(card_mock_[a-zA-Z0-9_]+)/', $endpoint, $matches)) {
                $cardId = $matches[1];
            }
        }
        if ($cardId && strpos($cardId, 'card_mock_') !== false) {
            return [
                'success' => true,
                'status' => 'success',
                'data' => [
                    'card_id' => $cardId,
                    'card_number' => '4242 1234 5678 9012',
                    'balance' => '100.00',
                    'status' => 'active',
                    'cvv' => '123',
                    'expiry' => '12/28',
                    'card_type' => 'Visa',
                    'card_name' => 'Mock Card Holder'
                ],
                'response' => [
                    'card_id' => $cardId,
                    'card_number' => '4242 1234 5678 9012',
                    'balance' => '100.00',
                    'status' => 'active',
                    'cvv' => '123',
                    'expiry' => '12/28',
                    'card_type' => 'Visa',
                    'card_name' => 'Mock Card Holder'
                ]
            ];
        }

        $curl = curl_init();
        $url = (strpos($endpoint, 'http') === 0) ? $endpoint : $this->baseUrl . $endpoint;
        
        $isStrowalletDotCom = (strpos($url, 'strowallet.com') !== false);
        $activePublicKey = $isStrowalletDotCom ? $this->strowalletPublicKey : $this->ziiropayPublicKey;

        // Ensure payload public_key matches target host
        if (is_array($data) && isset($data['public_key'])) {
            $data['public_key'] = $activePublicKey;
        }

        // Ensure GET query string public_key matches target host
        if (strpos($url, 'public_key=') !== false) {
            $url = preg_replace('/public_key=[^&]*/', 'public_key=' . urlencode($activePublicKey), $url);
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if ($activePublicKey) $headers[] = 'public_key: ' . $activePublicKey;

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false 
        ];

        if ($method === 'GET' && !empty($data)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
            $options[CURLOPT_URL] = $url;
        }

        if ($method === 'POST' || $method === 'PUT') {
            $jsonData = json_encode($data);
            $options[CURLOPT_POSTFIELDS] = $jsonData;
            $this->log("REQ [$method] $url", $jsonData);
        } else {
            $this->log("REQ [$method] $url");
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        // LOG RESPONSE (Truncated if too long)
        $this->log("RES [$httpCode]", substr($response, 0, 1000));

        if ($err) {
            $this->log("CURL ERROR: $err");
            throw new \Exception("StroWallet Connection Error: " . $err);
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // It might be HTML error - log the raw response
            $this->log("JSON ERROR (" . json_last_error_msg() . ")");
            $this->log("RAW RESPONSE", $response); // Log full response to debug
            
            // Include raw response in exception for immediate debugging
            $preview = substr($response, 0, 500);
            throw new \Exception("Invalid API Response from StroWallet (Status $httpCode). Raw response: " . $preview);
        }

        return $result;
    }

    private function log($title, $content = '') {
        // Use public directory for log file (guaranteed to exist and be writable)
        $logFile = __DIR__ . '/../../../../public/sw_debug.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] $title " . ($content ? ": $content" : "") . "\n";
        @file_put_contents($logFile, $entry, FILE_APPEND); // @ to suppress errors if dir not writable
    }

    public function createCustomer($email, $firstName, $lastName, $phone, $dob, $nin, $idImageUrl = null, $userPhotoUrl = null)
    {
        // Endpoint: /create-user (POST form-encoded)
        
        // Normalize phone number (International format without +)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '234' . $phone;
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            $phone = '234' . substr($phone, 1);
        }

        // Use real KYC document URLs if provided, otherwise use placeholder images
        if (empty($idImageUrl)) {
            $idImageUrl = "https://images.freeimages.com/vhq/images/previews/382/id-card-icon-68422.png";
        }
        if (empty($userPhotoUrl)) {
            $userPhotoUrl = "https://images.freeimages.com/vhq/images/previews/f91/user-avatar-icon-68423.png";
        }

        // Build parameters matching StroWallet requirements
        // Note: Support confirmed to use Ghana/Accra for all users
        // Using PASSPORT as idType since Ghana doesn't support NIN (Nigerian ID)
        $params = [
            'public_key'   => $this->publicKey,
            'firstName'    => $firstName,
            'lastName'     => $lastName,
            'customerEmail'=> $email,
            'phoneNumber'  => $phone,
            'dateOfBirth'  => date('m/d/Y', strtotime($dob)),
            'country'      => 'Ghana',
            'state'        => 'Accra',
            'city'         => 'Accra',
            'houseNumber'  => '1',
            'line1'        => 'No 1 Casjoe Way',
            'zipCode'      => '00233',
            'idType'       => 'PASSPORT', // Using PASSPORT instead of NIN for Ghana compatibility
            'idNumber'     => $nin, // Using the provided NIN value as passport number
            'idImage'      => $idImageUrl,
            'userPhoto'    => $userPhotoUrl
        ];
        
        // Send as POST with form-encoded data
        // Removing trailing slash from endpoint as per docs
        return $this->requestFormData('/create-user', $params);
    }
    
    public function requestFormData($endpoint, $params)
    {
        // Special method for form-encoded POST (not JSON)
        $curl = curl_init();
        $url = (strpos($endpoint, 'http') === 0) ? $endpoint : $this->baseUrl . $endpoint;

        $isStrowalletDotCom = (strpos($url, 'strowallet.com') !== false);
        $activePublicKey = $isStrowalletDotCom ? $this->strowalletPublicKey : $this->ziiropayPublicKey;

        if (is_array($params) && isset($params['public_key'])) {
            $params['public_key'] = $activePublicKey;
        }

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ];
        
        $formData = http_build_query($params);
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $this->log("REQ [POST-FORM] $url", $formData);

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        $this->log("RES [$httpCode]", substr($response, 0, 1000));

        if ($err) {
            $this->log("CURL ERROR: $err");
            throw new \Exception("StroWallet Connection Error: " . $err);
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("JSON ERROR (" . json_last_error_msg() . ")");
            $this->log("RAW RESPONSE", $response);
            
            // Include raw response in exception for immediate debugging
            $preview = substr($response, 0, 500);
            throw new \Exception("Invalid API Response from StroWallet (Status $httpCode). Raw response: " . $preview);
        }

        return $result;
    }

    public function getUserDetails($customerEmail)
    {
        // Endpoint: /fetch-user/
        // Note: StroWallet docs often show this as a GET with query parameters
        return $this->request("/fetch-user/?public_key=" . $this->publicKey . "&customerEmail=" . urlencode($customerEmail), 'GET');
    }

    public function updateCustomer($email, $data)
    {
        // Endpoint: /update-user (Back to POST, but using form-encoded)
        $params = array_merge([
            'public_key' => $this->publicKey,
            'customerEmail' => $email
        ], $data);
        
        return $this->requestFormData('/update-user', $params);
    }

    /**
     * Create Non-Reloadable Virtual Dollar Card (Lite Card)
     * No KYC required, instant creation, unlimited cards.
     * Endpoint: /create_litecard
     */
    public function createLiteCard($amount, $brand = 'VISA', $nameOnCard = 'Casjoe User', $idNumber = '10000000000', $idType = 'nin', $mode = 'live')
    {
        return $this->requestFormData('/create_litecard', [
            'public_key'   => $this->publicKey,
            'amount'       => number_format((float)$amount, 2, '.', ''),
            'brand'        => strtoupper($brand),
            'name_on_card' => $nameOnCard,
            'id_number'    => $idNumber,
            'id_type'      => $idType,
            'mode'         => $mode
        ]);
    }

    /**
     * Submit Card KYC for Reloadable NFC Cards
     * Endpoint: /cardkyc
     */
    public function submitCardKyc($data)
    {
        $params = array_merge([
            'public_key' => $this->publicKey,
        ], $data);
        return $this->requestFormData('/cardkyc', $params);
    }

    /**
     * Get Card KYC Status for Reloadable NFC Cards
     * Endpoint: /cardkycstatus
     */
    public function getCardKycStatus($email)
    {
        return $this->request("/cardkycstatus?public_key=" . $this->publicKey . "&email=" . urlencode($email), 'GET');
    }

    /**
     * Create Reloadable NFC Card for approved customer
     * Endpoint: /create-nfc-card
     */
    public function createReloadableNfcCard($customerId, $name, $amount, $mode = 'live')
    {
        return $this->requestFormData('/create-nfc-card', [
            'public_key'  => $this->publicKey,
            'customer_id' => $customerId,
            'name'        => $name,
            'amount'      => number_format((float)$amount, 2, '.', ''),
            'mode'        => $mode
        ]);
    }

    public function createCard($customerId, $currency = 'USD', $amount = 0, $nameOnCard = 'Casjoe User')
    {
        // Endpoint: /create-card/
        return $this->request('/create-card/', 'POST', [
            'public_key' => $this->publicKey,
            'customerEmail' => $customerId, // RESTORED CamelCase
            'card_type' => 'Visa', 
            'amount' => number_format((float)$amount, 2, '.', ''), 
            'name_on_card' => $nameOnCard,
            'currency' => $currency
        ]);
    }

    public function createNfcCard($firstName, $lastName, $dob, $idType, $idNumber, $email, $line1, $city, $state, $postalCode, $country, $amountUsd, $phone, $idImageUrl = 'https://app.casjoe.com/assets/images/default_id.jpg', $brand = 'MasterCard')
    {
        // Endpoint: /create-nfc-card/
        return $this->requestFormData('/create-nfc-card', [
            'public_key' => $this->publicKey,
            'name' => trim("$firstName $lastName"),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => date('m/d/Y', strtotime($dob)),
            'id_type' => strtoupper($idType) === 'NATIONAL ID' ? 'national_id' : (strtoupper($idType) === 'INTERNATIONAL PASSPORT' ? 'passport' : 'drivers_license'),
            'id_number' => $idNumber,
            'email' => $email,
            'line1' => $line1,
            'city' => $city,
            'state' => $state,
            'postal_code' => $postalCode,
            'country' => strtoupper($country),
            'amount_usd' => number_format((float)$amountUsd, 2, '.', ''),
            'phone' => $phone,
            'id_image' => $idImageUrl,
            'brand' => $brand,
            'mode' => 'live'
        ]);
    }

    public function fundCard($cardId, $amount)
    {
        return $this->request("/fund-card/", 'POST', [
            'public_key' => $this->publicKey,
            'card_id' => $cardId,
            'amount' => number_format((float)$amount, 2, '.', ''),
            'mode' => 'live'
        ]);
    }
    public function terminateCard($cardId)
    {
        // Switched to GET due to 405 error on POST
        return $this->request("/terminate-card", 'GET', [
            'public_key' => $this->publicKey,
            'card_id' => $cardId
        ]);
    }

    public function freezeCard($cardId)
    {
        // Switched to GET for consistency with terminate-card
        return $this->request("/freeze-card", 'GET', [
            'public_key' => $this->publicKey,
            'card_id' => $cardId
        ]);
    }

    public function unfreezeCard($cardId)
    {
        // Switched to GET for consistency with terminate-card
        return $this->request("/unfreeze-card", 'GET', [
            'public_key' => $this->publicKey,
            'card_id' => $cardId
        ]);
    }

    // ============================================
    // NFC CARD SPECIFIC ENDPOINTS
    // ============================================

    public function fetchCardDetails($cardId)
    {
        // Typically the standard card endpoint is /card-detail/ or /fetch-card-detail/
        return $this->request("/fetch-card-detail/?public_key=" . $this->publicKey . "&card_id=" . urlencode($cardId), 'GET');
    }

    public function fetchNfcCardDetails($cardId)
    {
        // Endpoint: /fetch-nfccard-detail (GET)
        return $this->request("/fetch-nfccard-detail?public_key=" . $this->publicKey . "&card_id=" . urlencode($cardId) . "&mode=live", 'GET');
    }

    public function nfcCardHistory($cardId)
    {
        // Endpoint: /nfc-card-transactions (GET)
        return $this->request("/nfc-card-transactions?public_key=" . $this->publicKey . "&card_id=" . urlencode($cardId) . "&mode=live", 'GET');
    }

    public function setNfcCardStatus($cardId, $status)
    {
        // Endpoint: /nfc-cards/status (POST)
        $action = ($status === 'block' || $status === 'frozen') ? 'frozen' : 'active';
        return $this->requestFormData("/nfc-cards/status", [
            'public_key' => $this->publicKey,
            'card_id'    => $cardId,
            'status'     => $action
        ]);
    }

    public function fundNfcCard($cardId, $amount)
    {
        // Endpoint: /fund-withdraw-nfccard (POST form-encoded)
        return $this->requestFormData("/fund-withdraw-nfccard", [
            'public_key' => $this->publicKey,
            'card_id'    => $cardId,
            'amount'     => number_format((float)$amount, 2, '.', ''),
            'type'       => 'fund',
            'mode'       => 'live'
        ]);
    }

    public function withdrawNfcCard($cardId, $amount)
    {
        // Endpoint: /fund-withdraw-nfccard (POST form-encoded)
        return $this->requestFormData("/fund-withdraw-nfccard", [
            'public_key' => $this->publicKey,
            'card_id'    => $cardId,
            'amount'     => number_format((float)$amount, 2, '.', ''),
            'type'       => 'withdraw',
            'mode'       => 'live'
        ]);
    }

    // ============================================
    // VIRTUAL BANK ACCOUNT ENDPOINTS
    // ============================================

    public function createVirtualBankAccount($email, $accountName, $phone)
    {
        // Normalize phone number (local 11-digit or international format)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '0' . $cleanPhone;
        }

        $params = [
            'public_key'   => $this->strowalletPublicKey,
            'email'        => $email,
            'account_name' => $accountName,
            'phone'        => $cleanPhone,
            'webhook_url'  => 'https://app.casjoe.com/api/strowallet/webhook',
            'mode'         => 'live'
        ];
        
        // 1. Try default account generator endpoint (PAGA / Nombank)
        $res = $this->request('https://strowallet.com/api/virtual-bank/new-customer/', 'POST', $params);

        // 2. If already exists or error, try Nombank MFB (Amucha) endpoint
        $isSuccess = ($res['success'] ?? false) === true || ($res['status'] ?? '') === 'success';
        if (!$isSuccess && isset($res['message']) && stripos($res['message'], 'already exists') !== false) {
            $resAmucha = $this->request('https://strowallet.com/api/virtual-bank/amucha/', 'POST', $params);
            if (($resAmucha['success'] ?? false) === true || ($resAmucha['status'] ?? '') === 'success') {
                return $resAmucha;
            }
        }

        return $res;
    }

    // ============================================
    // NAIRA CARD (VIRTUAL & PHYSICAL ATM) ENDPOINTS  
    // ============================================

    public function createNairaCardUser($firstname, $lastname, $email, $phone, $nin, $dob, $name, $line1, $city, $state, $provider = 'white', $mode = 'live')
    {
        // Normalize phone
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            $phone = '234' . substr($phone, 1);
        } elseif (strlen($phone) === 10) {
            $phone = '234' . $phone;
        }

        return $this->requestFormData('https://strowallet.com/api/naira_carduser', [
            'public_key' => $this->publicKey,
            'firstname'  => $firstname,
            'lastname'   => $lastname,
            'email'      => $email,
            'phone'      => $phone,
            'nin'        => $nin,
            'dob'        => $dob, // YYYY/MM/DD format
            'name'       => $name, // unique username
            'line1'      => $line1,
            'city'       => $city,
            'state'      => $state,
            'provider'   => $provider,
            'mode'       => $mode,
        ]);
    }

    public function createNairaCard($customerId, $type = 'virtual', $brand = 'AfriGo', $provider = 'white', $pan = null, $mode = 'live')
    {
        $params = [
            'public_key'  => $this->publicKey,
            'customerId'  => $customerId,
            'type'        => $type, // 'virtual' or 'physical'
            'brand'       => $brand, // 'Verve' or 'AfriGo'
            'provider'    => $provider,
            'mode'        => $mode,
        ];
        // Physical cards require the PAN number from the physical card
        if ($type === 'physical' && $pan) {
            $params['number'] = $pan;
        }
        return $this->requestFormData('https://strowallet.com/api/naira_createcard', $params);
    }

    public function viewNairaCard($cardId, $mode = 'live')
    {
        $url = 'https://strowallet.com/api/naira_viewcard?public_key=' . urlencode($this->publicKey) 
             . '&card_id=' . urlencode($cardId)
             . '&mode=' . urlencode($mode);
        return $this->request($url, 'GET');
    }

    public function updateNairaCardStatus($cardId, $status = 'active', $mode = 'live')
    {
        // PUT request with query params
        $url = 'https://strowallet.com/api/naira_ChangeStatus?public_key=' . urlencode($this->publicKey)
             . '&card_id=' . urlencode($cardId)
             . '&status=' . urlencode($status)
             . '&mode=' . urlencode($mode);
        return $this->request($url, 'PUT');
    }

    public function changeNairaCardPin($cardId, $oldPin, $newPin, $mode = 'live')
    {
        return $this->requestFormData('https://strowallet.com/api/naira_changepin', [
            'public_key' => $this->publicKey,
            'card_id'    => $cardId,
            'old_pin'    => $oldPin,
            'new_pin'    => $newPin,
            'mode'       => $mode,
        ]);
    }

    public function nairaCardHistory($cardId, $mode = 'live')
    {
        $url = 'https://strowallet.com/api/naira_cardhistory?public_key=' . urlencode($this->publicKey)
             . '&card_id=' . urlencode($cardId)
             . '&mode=' . urlencode($mode);
        return $this->request($url, 'GET');
    }

    public function getAccountName($bankCode, $accountNumber, $mode = 'live')
    {
        $url = 'https://strowallet.com/api/banks/get-customer-name?public_key=' . urlencode($this->publicKey)
             . '&bank_code=' . urlencode($bankCode)
             . '&account_number=' . urlencode($accountNumber)
             . '&mode=' . urlencode($mode);
        return $this->request($url, 'GET');
    }

    public function bankTransfer($amount, $bankCode, $accountNumber, $narration, $nameEnquiryRef = '', $senderName = '', $mode = 'live')
    {
        $url = 'https://strowallet.com/api/banks/request?public_key=' . urlencode($this->publicKey)
             . '&amount=' . urlencode($amount)
             . '&bank_code=' . urlencode($bankCode)
             . '&account_number=' . urlencode($accountNumber)
             . '&narration=' . urlencode($narration)
             . '&name_enquiry_reference=' . urlencode($nameEnquiryRef)
             . '&SenderName=' . urlencode($senderName)
             . '&mode=' . urlencode($mode);
        
        return $this->request($url, 'POST');
    }

    public function getBanks()
    {
        $url = 'https://strowallet.com/api/banks/lists/?public_key=' . urlencode($this->publicKey);
        return $this->request($url, 'GET');
    }

}

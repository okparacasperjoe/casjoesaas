<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class VisaCardService
{
    private $pdo;
    private $apiUrl;
    private $apiKey;
    private $sandboxMode;

    // Visa DPS Endpoints
    const ENDPOINT_CREATE_CARD_V2 = '/dcas/cardservices/v2/cards';
    const ENDPOINT_ACTIVATE_CARD_V3 = '/dcas/cardservices/v3/cards/{cardId}/cardactivation';
    const ENDPOINT_GET_CARD_V2 = '/dcas/cardservices/v2/cards/{cardId}';
    const ENDPOINT_STATUS_V2 = '/dcas/cardservices/v2/cards/{cardId}/status';
    const ENDPOINT_TRANSACTIONS_V2 = '/dcas/cardservices/v2/cards/{cardId}/transactions';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->loadSettings();
    }

    private function loadSettings()
    {
        // Load from DB or Fallback to hardcoded for now (as requested)
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'visa_%'");
        $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->sandboxMode = isset($settings['visa_sandbox_mode']) ? (bool)$settings['visa_sandbox_mode'] : true;
        
        // Use DB Key if exists, otherwise fallback to the one provided by user
        $dbKey = $settings['visa_api_token'] ?? '';
        $this->apiKey = !empty($dbKey) ? $dbKey : 'UQ5S73I5NGQKT9Q4RB5U21uuF0_TfCZWEIdKTY7vR-hm9dUq8';

        $this->apiUrl = $this->sandboxMode 
            ? 'https://sandbox.api.visa.com' 
            : 'https://api.visa.com';
            
        // Debug Log
        // $this->logError('Init', "Mode: " . ($this->sandboxMode ? 'Sandbox' : 'Prod') . " | Key: " . substr($this->apiKey, 0, 5) . '...');
    }

    /**
     * Create a new Virtual Card
     */
    public function createCard($userId, array $cardData)
    {
        // 1. Validate internal requirements
        // Verify User? (Controller should handle this)
        
        // 2. Prepare Payload
        // Assuming we generic PAN or Program ID management.
        // For generic sandbox, we often need to generate a PAN or use a Test PAN.
        // Payload: primaryAccountNumber, cardProgram, productCode, embossmentName
        
        $payload = [
            'primaryAccountNumber' => $cardData['pan'] ?? $this->generateTestPan(), // In prod, this comes from pool
            'cardProgram' => $cardData['program_code'] ?? 'CASJOE_VIRTUAL',
            'productCode' => $cardData['product_code'] ?? 'VIRTUAL_DEBIT',
            'embossmentName' => strtoupper($cardData['name_on_card']),
        ];

        try {
            $response = $this->call('POST', self::ENDPOINT_CREATE_CARD_V2, $payload);
            
            // 3. Store in DB
            $cardId = $response['cardId'] ?? null;
            
            if (!$cardId) {
                throw new Exception("Visa API did not return a Card ID");
            }

            // Save to database
            $stmt = $this->pdo->prepare("
                INSERT INTO cp_virtual_cards 
                (user_id, card_id, name_on_card, pan, status, provider, created_at) 
                VALUES (?, ?, ?, ?, 'inactive', 'visa', NOW())
            ");
            // Mask PAN for storage
            $maskedPan = substr($payload['primaryAccountNumber'], 0, 6) . '******' . substr($payload['primaryAccountNumber'], -4);
            
            $stmt->execute([
                $userId,
                $cardId,
                $payload['embossmentName'],
                $maskedPan,
            ]);

            return [
                'success' => true,
                'card_id' => $cardId,
                'masked_pan' => $maskedPan,
                'api_response' => $response
            ];

        } catch (Exception $e) {
            $this->logError('createCard', $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Activate the card
     */
    public function activateCard($cardId, $kycData)
    {
        $url = str_replace('{cardId}', $cardId, self::ENDPOINT_ACTIVATE_CARD_V3);
        
        $payload = [
            'birthDateMmDdYyyy' => $kycData['dob'], // 'MM-DD-YYYY'
            'phoneNumber' => $kycData['phone'],
            'cardsInfo' => [
                'cvv2' => $kycData['cvv'], // Often needed for activation verification or setting
                'expirationDate' => $kycData['expiry'], // 'MMYY'
            ]
        ];

        try {
            $response = $this->call('POST', $url, $payload);
            
            // Update DB
            $stmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET status = 'active', activated_at = NOW() WHERE card_id = ?");
            $stmt->execute([$cardId]);

            return ['success' => true, 'response' => $response];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get Card Details
     */
    public function getCardStatus($cardId)
    {
        $url = str_replace('{cardId}', $cardId, self::ENDPOINT_GET_CARD_V2);
        
        // Check Cache first? (Implement Redis/File cache later)
        
        try {
            $response = $this->call('GET', $url);
            
            // Sync status to DB
            $status = $response['cardStatus'] ?? null; // 'ACTIVE', 'LOCKED', etc.
            $balance = $response['availableBalance'] ?? 0.00;
            
            if ($status) {
                 $stmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET status = ?, balance = ? WHERE card_id = ?");
                 $stmt->execute([strtolower($status), $balance, $cardId]);
            }
            
            return ['success' => true, 'data' => $response];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Lock/Unlock Card
     */
    public function updateCardStatus($cardId, $action) 
    {
        // Action: 'lock' or 'unlock'
        $visaStatus = ($action === 'lock') ? 'LOCKED' : 'ACTIVE';
        
        $url = str_replace('{cardId}', $cardId, self::ENDPOINT_STATUS_V2);
        
        $payload = ['cardStatus' => $visaStatus];

        try {
            $response = $this->call('PUT', $url, $payload);
            
            // Update DB
            $stmt = $this->pdo->prepare("UPDATE cp_virtual_cards SET status = ? WHERE card_id = ?");
            $stmt->execute([strtolower($visaStatus), $cardId]);

            return ['success' => true, 'status' => strtolower($visaStatus)];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get Transactions
     */
    public function getTransactions($cardId, $fromDate = null, $toDate = null)
    {
        $url = str_replace('{cardId}', $cardId, self::ENDPOINT_TRANSACTIONS_V2);
        
        $query = [];
        if ($fromDate) $query['fromDate'] = $fromDate; // YYYY-MM-DD
        if ($toDate)   $query['toDate'] = $toDate;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        try {
            $response = $this->call('GET', $url);
            $txns = $response['transactions'] ?? [];
            
            // Sync to DB
            $this->syncTransactions($cardId, $txns);
            
            return ['success' => true, 'data' => $txns];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // --- Private Helpers ---

    private function call($method, $endpoint, $payload = []) 
    {
        // 1. URL: Append API Key (Project ID)
        $separator = (strpos($endpoint, '?') === false) ? '?' : '&';
        $url = $this->apiUrl . $endpoint . $separator . 'apikey=' . $this->apiKey;
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // 2. Mutual SSL Logic
        // Primary: Secure Storage
        $certPath = __DIR__ . '/../../storage/certs/visa/cert.pem';
        $keyPath  = __DIR__ . '/../../storage/certs/visa/key.pem';

        // Fallback: User uploaded to public/certs (Temporary/Insecure)
        if (!file_exists($certPath)) {
            $certPath = __DIR__ . '/../../public/certs/cert.pem';
            $keyPath  = __DIR__ . '/../../public/certs/key.pem';
        }

        if (file_exists($certPath) && file_exists($keyPath)) {
            curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
            curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
            // Optional: If user has a password for the key? Assuming no for now (PEM usually unprotected or we need another setting)
        } else {
            // Fallback to Basic Auth if certs missing (or maybe log error?)
            curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ':'); 
        }

        if ($method !== 'GET' && !empty($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);


        $this->logApiCall($url, $method, $payload, $httpCode, $result);

        if ($error) {
            throw new Exception("cURL Error: $error");
        }

        $decoded = json_decode($result, true);

        if ($httpCode >= 400) {
            $msg = $decoded['errorMessage'] ?? ($decoded['message'] ?? ($decoded['responseStatus']['message'] ?? 'Unknown Error'));
            // Debug: append raw response if unknown
            if ($msg === 'Unknown Error') {
                $msg .= " | Raw: " . substr($result, 0, 200);
            }
            throw new Exception("Visa API Error ($httpCode): $msg");
        }

        return $decoded;
    }

    private function syncTransactions($cardId, $transactions)
    {
        // Get local DB ID
        $stmt = $this->pdo->prepare("SELECT id FROM cp_virtual_cards WHERE card_id = ?");
        $stmt->execute([$cardId]);
        $localId = $stmt->fetchColumn();

        if (!$localId) return;

        $insert = $this->pdo->prepare("
            INSERT IGNORE INTO cp_card_transactions 
            (card_id, visa_transaction_id, amount, currency, merchant_name, transaction_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($transactions as $tx) {
            $insert->execute([
                $localId,
                $tx['transactionId'],
                $tx['amount'],
                $tx['currency'] ?? 'USD',
                $tx['merchantName'],
                $tx['date'] // Ensure format matches DATETIME
            ]);
        }
    }

    private function generateTestPan()
    {
        // Generate a valid Luhn (Mod 10) test card number
        // Starting with 4 (Visa)
        $prefix = "400000"; // Sandbox valid bin?
        $random = mt_rand(100000000, 999999999);
        $pan = $prefix . $random;
        
        // Calculate Checksum
        $sum = 0;
        $numDigits = strlen($pan);
        $parity = $numDigits % 2;
        for ($i = 0; $i < $numDigits; $i++) {
            $digit = $pan[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) $digit -= 9;
            }
            $sum += $digit;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $pan . $checkDigit;
    }

    private function logApiCall($url, $method, $payload, $code, $response)
    {
        $logFile = __DIR__ . '/../../storage/logs/visa_api.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] $method $url ($code)\n";
        $entry .= "Payload: " . json_encode($payload) . "\n";
        $entry .= "Response: " . substr($response, 0, 500) . "...\n\n";
        file_put_contents($logFile, $entry, FILE_APPEND);
    }

    private function logError($context, $message)
    {
        $logFile = __DIR__ . '/../../storage/logs/visa_errors.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] [$context] ERROR: $message\n";
        file_put_contents($logFile, $entry, FILE_APPEND);
    }
}

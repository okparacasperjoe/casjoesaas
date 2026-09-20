<?php

namespace App\Core\Services;

use App\Core\Database;

class LicenseService
{
    private static $version = "1.0.0";
    private static $masterUrl = "https://casjoe.com/api/system"; // Your master management domain
    private static $verifyUrl = "https://casjoe.com/api/license/verify"; 

    public static function verify($purchaseCode)
    {
        $domain = $_SERVER['HTTP_HOST'];
        
        // In a real scenario, this would be a CURL call to your server
        // For now, we implement the logic and a success simulator
        
        /*
        $ch = curl_init(self::$verifyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'purchase_code' => $purchaseCode,
            'domain' => $domain,
            'item_id' => 'CASJOE-SAAS-001'
        ]);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        */

        // SIMULATION: If code starts with 'AV-', it's valid (for testing)
        if (strpos($purchaseCode, 'AV-') === 0) {
            return [
                'success' => true,
                'message' => 'License activated successfully!',
                'token' => bin2hex(random_bytes(16))
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Purchase Code or code already in use on another domain.'
        ];
    }

    public static function isActivated()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'license_status'");
            $status = $stmt->fetchColumn();
            return ($status === 'verified');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function blockUnlicensed()
    {
        $allowedPages = ['/install', '/license-verify'];
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!self::isActivated() && !in_array($currentPath, $allowedPages)) {
            // Check if we are in the installer
            if (strpos($currentPath, '/install') === false) {
                die("<h1>License Required</h1><p>Please activate your Casjoe SaaS license to continue.</p><a href='/admin/settings'>Go to Activation</a>");
            }
        }
    }

    /**
     * Telemetry: Sends usage stats to your master server
     */
    public static function sendHeartbeat($data = [])
    {
        $db = Database::getInstance()->getConnection();
        $userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        $payload = [
            'action' => 'heartbeat',
            'domain' => $_SERVER['HTTP_HOST'],
            'license_code' => self::getLicenseCode(),
            'version' => self::$version,
            'stats' => [
                'users' => $userCount,
                'timestamp' => time()
            ]
        ];

        // Silent background call (simulated)
        // file_get_contents(self::$masterUrl . '?' . http_build_query($payload));
    }

    /**
     * Check for new versions
     */
    public static function checkUpdate()
    {
        // Simulated response from your server
        return [
            'current_version' => self::$version,
            'latest_version' => '1.1.0',
            'has_update' => true,
            'changelog' => 'Added AI Skin Glow analysis and fixed PayPal callback bug.',
            'download_url' => 'https://casjoe.online/updates/v110.zip'
        ];
    }

    private static function getLicenseCode()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'license_code'");
            return $stmt->fetchColumn();
        } catch (\Exception $e) { return 'unknown'; }
    }
}

<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use PDO;

class PhoneController
{
    private $pdo;
    private $userId;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        
        // Auto-create database tables on initialization if they do not exist
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cp_virtual_phones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            tenant_id INT NOT NULL,
            phone_number VARCHAR(50) NOT NULL,
            country VARCHAR(50) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cp_virtual_sms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone_id INT NOT NULL,
            sender VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
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

        $this->userId = $_SESSION['user_id'];
        
        // Resolve tenant ID
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        // Fetch user's virtual phone numbers
        $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_phones WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$this->userId]);
        $phones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch SMS messages for the active phone number if any exists
        $activePhone = !empty($phones) ? $phones[0] : null;
        $smsList = [];

        if ($activePhone) {
            $stmt = $this->pdo->prepare("SELECT * FROM cp_virtual_sms WHERE phone_id = ? ORDER BY created_at DESC");
            $stmt->execute([$activePhone['id']]);
            $smsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../Views/phone.php';
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->userId = $_SESSION['user_id'];

        // Resolve tenant ID
        $stmt = $this->pdo->prepare("SELECT tenant_id FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $this->tenantId = $stmt->fetchColumn();

        $country = $_POST['country'] ?? 'USA';
        
        // Generate mock phone number
        $number = '';
        if ($country === 'USA') {
            $number = '+1 (202) 555-' . rand(1000, 9999);
        } elseif ($country === 'UK') {
            $number = '+44 7700 900' . rand(100, 999);
        } else {
            $number = '+1 (416) 555-' . rand(1000, 9999); // Canada default
        }

        // Insert phone number
        $stmt = $this->pdo->prepare("
            INSERT INTO cp_virtual_phones (user_id, tenant_id, phone_number, country, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$this->userId, $this->tenantId, $number, $country]);
        $phoneId = $this->pdo->lastInsertId();

        // Seed mock SMS verification messages
        $smsData = [
            [
                'sender' => 'Google',
                'message' => 'G-829103 is your Google verification code.',
                'created_at' => date('Y-m-d H:i:s', time() - 30) // 30s ago
            ],
            [
                'sender' => 'WhatsApp',
                'message' => 'Your WhatsApp code is 291-382. Do not share this code.',
                'created_at' => date('Y-m-d H:i:s', time() - 300) // 5 mins ago
            ],
            [
                'sender' => 'Stripe',
                'message' => 'Alert: Successful charge of $10.00 USD on card ending in 8571.',
                'created_at' => date('Y-m-d H:i:s', time() - 3600) // 1 hour ago
            ]
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO cp_virtual_sms (phone_id, sender, message, created_at)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($smsData as $sms) {
            $stmt->execute([$phoneId, $sms['sender'], $sms['message'], $sms['created_at']]);
        }

        header('Location: /pay/phone');
        exit;
    }
}

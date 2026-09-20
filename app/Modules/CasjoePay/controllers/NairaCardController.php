<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Modules\CasjoePay\Services\StroWalletService;

class NairaCardController
{
    /**
     * GET /pay/naira-cards
     * Dashboard showing KYC status, virtual card, physical card
     */
    public function index()
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        // Check if user has completed KYC (naira card user registration)
        $stmt = $db->prepare("SELECT * FROM cp_naira_card_users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user['id']]);
        $cardUser = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Get all naira cards for this user
        $stmt = $db->prepare("SELECT * FROM cp_naira_cards WHERE user_id = ? ORDER BY card_type ASC, created_at DESC");
        $stmt->execute([$user['id']]);
        $cards = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Separate virtual and physical cards
        $virtualCards = array_filter($cards, fn($c) => $c['card_type'] === 'virtual');
        $physicalCards = array_filter($cards, fn($c) => $c['card_type'] === 'physical');
        $hasVirtualCard = !empty($virtualCards);
        $hasPhysicalCard = !empty($physicalCards);

        // Check available physical cards in inventory
        $stmt = $db->prepare("SELECT COUNT(*) FROM cp_naira_card_inventory WHERE status = 'available'");
        $stmt->execute();
        $availablePhysicalCards = (int) $stmt->fetchColumn();

        $activeMenu = 'naira-cards';
        $title = 'Naira Cards | Casjoe Pay';
        $pageTitle = 'Naira Cards';

        require __DIR__ . '/../Views/naira_cards.php';
    }

    /**
     * POST /pay/naira-cards/register
     * KYC registration - creates StroWallet card user
     */
    public function registerUser()
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        // Check if already registered
        $stmt = $db->prepare("SELECT id FROM cp_naira_card_users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user['id']]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'You have already completed card registration.';
            header('Location: /pay/naira-cards');
            exit;
        }

        // Validate inputs
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? $user['email'] ?? '');
        $phone = trim($_POST['phone'] ?? $user['phone'] ?? '');
        $nin = trim($_POST['nin'] ?? '');
        $dob = trim($_POST['dob'] ?? ''); // expects YYYY-MM-DD from HTML date input
        $line1 = trim($_POST['line1'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $country = trim($_POST['country'] ?? 'NGA');
        $postal_code = trim($_POST['postal_code'] ?? '100001');

        if (!$firstname || !$lastname || !$email || !$phone || !$nin || !$dob || !$line1 || !$city || !$state || !$country || !$postal_code) {
            $_SESSION['error'] = 'All fields are required for card registration.';
            header('Location: /pay/naira-cards');
            exit;
        }

        // Format DOB to YYYY/MM/DD for StroWallet
        $dobFormatted = str_replace('-', '/', $dob);

        // Generate unique username from user ID
        $uniqueName = 'casjoe_' . $user['id'] . '_' . substr(md5($email), 0, 6);

        try {
            $strowallet = new StroWalletService();

            if (empty($strowallet->publicKey)) {
                // Mock mode
                $customerId = 'MOCK_' . strtoupper(substr(uniqid(), -12));
            } else {
                $result = $strowallet->createNairaCardUser(
                    $firstname, $lastname, $email, $phone, $nin, $dobFormatted,
                    $uniqueName, $line1, $city, $state, 'white', 'live'
                );

                if (!($result['success'] ?? false)) {
                    throw new \Exception($result['message'] ?? 'Card user registration failed.');
                }

                $customerId = $result['data']['customer_id'] ?? '';
                if (!$customerId) {
                    throw new \Exception('No customer ID returned from provider.');
                }

            }

            // Save to database
            $stmt = $db->prepare(
                "INSERT INTO cp_naira_card_users (tenant_id, user_id, customer_id, firstname, lastname, email, phone, nin, dob, address_line1, city, state, country, postal_code, provider, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'white', 'active')"
            );
            $stmt->execute([
                $user['tenant_id'], $user['id'], $customerId,
                $firstname, $lastname, $email, $phone, $nin, $dob, $line1, $city, $state, $country, $postal_code
            ]);

            $_SESSION['success'] = 'Card registration successful! You can now request your physical Naira card.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Registration Error: ' . $e->getMessage();
        }

        header('Location: /pay/naira-cards');
        exit;
    }

    /**
     * POST /pay/naira-cards/create-physical
     * Creates a physical ATM card
     * Admin must have added PANs to inventory first
     */
    public function createPhysicalCard()
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        // Verify KYC
        $stmt = $db->prepare("SELECT * FROM cp_naira_card_users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user['id']]);
        $cardUser = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$cardUser) {
            $_SESSION['error'] = 'Please complete card registration first.';
            header('Location: /pay/naira-cards');
            exit;
        }


        // Check if already has physical card
        $stmt = $db->prepare("SELECT id FROM cp_naira_cards WHERE user_id = ? AND card_type = 'physical' LIMIT 1");
        $stmt->execute([$user['id']]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'You already have a physical ATM card.';
            header('Location: /pay/naira-cards');
            exit;
        }

        // Get an available PAN from inventory
        $stmt = $db->prepare("SELECT * FROM cp_naira_card_inventory WHERE status = 'available' ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $inventoryCard = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$inventoryCard) {
            $_SESSION['error'] = 'No physical cards are currently available. Please try again later or contact support.';
            header('Location: /pay/naira-cards');
            exit;
        }

        try {
            $strowallet = new StroWalletService();
            $pan = $inventoryCard['pan'];

            if (empty($strowallet->publicKey)) {
                // Mock mode
                $cardId = 'naira_phys_mock_' . uniqid();
                $maskedPan = substr($pan, 0, 6) . '****' . substr($pan, -4);
                $expiryMonth = date('m');
                $expiryYear = date('Y', strtotime('+3 years'));
                $brand = $inventoryCard['brand'] ?? 'AfriGo';
            } else {
                $result = $strowallet->createNairaCard(
                    $cardUser['customer_id'], 'physical', $inventoryCard['brand'] ?? 'AfriGo', 'white', $pan, 'live'
                );

                if (!($result['success'] ?? false)) {
                    throw new \Exception($result['message'] ?? 'Physical card creation failed.');
                }

                $data = $result['data'] ?? [];
                $cardId = $data['card_id'] ?? '';
                $maskedPan = $data['maskedPan'] ?? substr($pan, 0, 6) . '****' . substr($pan, -4);
                $expiryMonth = $data['expiryMonth'] ?? '';
                $expiryYear = $data['expiryYear'] ?? '';
                $brand = $data['brand'] ?? 'AfriGo';
            }

            // Save card
            $stmt = $db->prepare(
                "INSERT INTO cp_naira_cards (tenant_id, user_id, customer_id, card_id, card_type, brand, masked_pan, expiry_month, expiry_year, status, provider, meta_data)
                 VALUES (?, ?, ?, ?, 'physical', ?, ?, ?, ?, 'active', 'white', ?)"
            );
            $stmt->execute([
                $user['tenant_id'], $user['id'], $cardUser['customer_id'],
                $cardId, $brand, $maskedPan, $expiryMonth, $expiryYear,
                json_encode($result ?? ['mock' => true])
            ]);

            // Mark inventory card as assigned
            $stmt = $db->prepare(
                "UPDATE cp_naira_card_inventory SET status = 'assigned', assigned_to_user_id = ?, assigned_card_id = ?, assigned_at = NOW() WHERE id = ?"
            );
            $stmt->execute([$user['id'], $cardId, $inventoryCard['id']]);

            $_SESSION['success'] = 'Physical ATM card created and linked successfully!';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Card Error: ' . $e->getMessage();
        }

        header('Location: /pay/naira-cards');
        exit;
    }

    /**
     * POST /pay/naira-cards/toggle-status
     * Activate or deactivate a card
     */
    public function toggleStatus()
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        $cardId = trim($_POST['card_id'] ?? '');
        $newStatus = trim($_POST['status'] ?? ''); // 'active' or 'inactive'

        if (!$cardId || !in_array($newStatus, ['active', 'inactive'])) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: /pay/naira-cards');
            exit;
        }

        // Verify card belongs to user
        $stmt = $db->prepare("SELECT * FROM cp_naira_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$cardId, $user['id']]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            $_SESSION['error'] = 'Card not found.';
            header('Location: /pay/naira-cards');
            exit;
        }

        try {
            $strowallet = new StroWalletService();

            if (!empty($strowallet->publicKey) && strpos($cardId, 'mock') === false) {
                $result = $strowallet->updateNairaCardStatus($cardId, $newStatus, 'live');
                if (!($result['success'] ?? false)) {
                    throw new \Exception($result['message'] ?? 'Failed to update card status.');
                }
            }

            // Update local DB
            $stmt = $db->prepare("UPDATE cp_naira_cards SET status = ? WHERE card_id = ? AND user_id = ?");
            $stmt->execute([$newStatus, $cardId, $user['id']]);

            $_SESSION['success'] = 'Card ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: /pay/naira-cards');
        exit;
    }

    /**
     * GET /pay/naira-cards/history/{card_id}
     * View card transaction history
     */
    public function history($cardId)
    {
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        // Verify card belongs to user
        $stmt = $db->prepare("SELECT * FROM cp_naira_cards WHERE card_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$cardId, $user['id']]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            $_SESSION['error'] = 'Card not found.';
            header('Location: /pay/naira-cards');
            exit;
        }

        $transactions = [];
        try {
            $strowallet = new StroWalletService();
            if (!empty($strowallet->publicKey) && strpos($cardId, 'mock') === false) {
                $result = $strowallet->nairaCardHistory($cardId, 'live');
                $transactions = $result['data'] ?? [];
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Could not fetch history: ' . $e->getMessage();
        }

        $activeMenu = 'naira-cards';
        $title = 'Card History | Casjoe Pay';
        $pageTitle = 'Card Transaction History';

        require __DIR__ . '/../Views/naira_card_history.php';
    }
}

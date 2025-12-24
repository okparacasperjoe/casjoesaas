<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use PDO;

class LinkController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Require Login for Management
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_links WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/links.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) exit;

        $title = $_POST['title'];
        $amount = !empty($_POST['amount']) ? $_POST['amount'] : null; // Can be null for "Any amount"
        $currency = 'NGN';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . uniqid();
        
        // Ensure slug uniqueness (simple method)
        // Insert
        $stmt = $this->pdo->prepare("INSERT INTO cp_payment_links (tenant_id, user_id, slug, title, amount, currency) VALUES (?, ?, ?, ?, ?, ?)");
        // Fetch Tenant ID (Optimized: Get from session or DB)
        $userId = $_SESSION['user_id'];
        $tenantId = 1; // Default for now
        
        $stmt->execute([$tenantId, $userId, $slug, $title, $amount, $currency]);

        header('Location: /pay/links');
    }

    public function pay($params)
    {
        $slug = $params['slug'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_links WHERE slug = ?");
        $stmt->execute([$slug]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$link) {
            die("Payment Link Not Found");
        }

        // Increment Views
        $this->pdo->prepare("UPDATE cp_payment_links SET views = views + 1 WHERE id = ?")->execute([$link['id']]);

        require __DIR__ . '/../views/public_pay.php';
    }
}

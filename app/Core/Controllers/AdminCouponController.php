<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class AdminCouponController
{
    private $pdo;

    public function __construct()
    {
        // Ensure Admin or Moderator
        $user = Auth::user();
        if (!$user || !in_array($user['role'], ['admin', 'moderator']) || $user['tenant_id'] != 1) {
            die("<h1>Access Denied</h1><p>You do not have permission to access the Coupon Admin Panel.</p><p><a href='/dashboard'>Return to Dashboard</a></p>");
        }
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // View all coupons (for Tenant 1 = Super Admin)
        $stmt = $this->pdo->prepare("SELECT * FROM system_billing_coupons WHERE tenant_id = 1 ORDER BY created_at DESC");
        $stmt->execute();
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin/coupons.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $type = $_POST['type'] ?? 'percent';
            $value = floatval($_POST['value'] ?? 0);
            $duration = intval($_POST['duration_months'] ?? 1);
            $expires = !empty($_POST['expires_at']) ? $_POST['expires_at'] . ' 23:59:59' : null;

            if (empty($code) || $value <= 0) {
                header('Location: /casper-joe/coupons?error=Invalid Input');
                exit;
            }

            try {
                $stmt = $this->pdo->prepare("INSERT INTO system_billing_coupons (tenant_id, code, type, value, duration_months, expires_at) VALUES (1, ?, ?, ?, ?, ?)");
                $stmt->execute([$code, $type, $value, $duration, $expires]);
                header('Location: /casper-joe/coupons?msg=Coupon Created');
            } catch (\Exception $e) {
                header('Location: /casper-joe/coupons?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $stmt = $this->pdo->prepare("DELETE FROM system_billing_coupons WHERE id = ? AND tenant_id = 1");
                $stmt->execute([$id]);
            }
        }
        header('Location: /casper-joe/coupons?msg=Deleted');
        exit;
    }
}

<?php

namespace App\Modules\CasjoeShop\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class AdminController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        // Simple Admin Auth Check (role check or exact email check should be here)
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
        $this->ensureShopSchema();
    }

    private function ensureShopSchema()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN currency VARCHAR(10) DEFAULT 'NGN'");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN slug VARCHAR(255) NULL");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN fee_bearer ENUM('merchant', 'customer') DEFAULT 'merchant'");
        } catch (\Exception $e) {}

        try {
            $this->db->query("ALTER TABLE shop_vendors ADD COLUMN facebook_pixel_id VARCHAR(255) NULL");
        } catch (\Exception $e) {}
    }

    public function dashboard()
    {
        // KPI: Total Vendors
        $stmt = $this->db->query("SELECT COUNT(*) FROM shop_vendors WHERE tenant_id = ?", [$this->tenantId]);
        $totalVendors = $stmt->fetchColumn();

        // KPI: Total Products
        $stmt = $this->db->query("SELECT COUNT(*) FROM shop_products WHERE status = 'active'");
        $totalProducts = $stmt->fetchColumn();

        // KPI: Total Orders
        $stmt = $this->db->query("SELECT COUNT(*) FROM shop_orders WHERE tenant_id = ?", [$this->tenantId]);
        $totalOrders = $stmt->fetchColumn();

        // KPI: Revenue
        $stmt = $this->db->query("SELECT SUM(total_amount) FROM shop_orders WHERE tenant_id = ? AND status='paid'", [$this->tenantId]);
        $revenue = $stmt->fetchColumn() ?: 0;

        // Pending Vendors
        $stmt = $this->db->query("SELECT * FROM shop_vendors WHERE tenant_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 5", [$this->tenantId]);
        $pendingVendors = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function vendors()
    {
        $stmt = $this->db->query("SELECT * FROM shop_vendors WHERE tenant_id = ? ORDER BY created_at DESC", [$this->tenantId]);
        $vendors = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/admin/vendors.php';
    }

    public function approveVendor($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        
        $this->db->query("UPDATE shop_vendors SET status = 'approved' WHERE id = ? AND tenant_id = ?", [$id, $this->tenantId]);
        
        header('Location: /' . ADMIN_PATH . '/shop/vendors');
        exit;
    }

    public function ads()
    {
        $stmt = $this->db->query("SELECT * FROM shop_ads_settings WHERE tenant_id = ?", [$this->tenantId]);
        $ads = $stmt->fetchAll();
        
        // Re-key by location for easier access in view
        $adsByLocation = [];
        foreach ($ads as $ad) {
            $adsByLocation[$ad['banner_location']] = $ad;
        }

        require_once __DIR__ . '/../Views/admin/ads.php';
    }

    public function updateAds()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $locations = ['top', 'middle'];

            foreach ($locations as $loc) {
                if (isset($_POST[$loc])) {
                    $data = $_POST[$loc];
                    $isActive = isset($data['is_active']) ? 1 : 0;
                    
                    // Check if exists
                    $stmt = $this->db->query("SELECT id FROM shop_ads_settings WHERE tenant_id = ? AND banner_location = ?", [$this->tenantId, $loc]);
                    $exists = $stmt->fetchColumn();

                    if ($exists) {
                        $sql = "UPDATE shop_ads_settings SET 
                                title = ?, description = ?, button_text = ?, button_link = ?, image_url = ?, is_active = ?
                                WHERE tenant_id = ? AND banner_location = ?";
                        $this->db->query($sql, [
                            $data['title'], $data['description'], $data['button_text'], $data['button_link'], $data['image_url'], $isActive,
                            $this->tenantId, $loc
                        ]);
                    } else {
                        $sql = "INSERT INTO shop_ads_settings (tenant_id, banner_location, title, description, button_text, button_link, image_url, is_active)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $this->db->query($sql, [
                            $this->tenantId, $loc, $data['title'], $data['description'], $data['button_text'], $data['button_link'], $data['image_url'], $isActive
                        ]);
                    }
                }
            }
            
            header('Location: /' . ADMIN_PATH . '/shop/ads?success=1');
            exit;
        }
    }
    public function sliders()
    {
        $stmt = $this->db->query("SELECT * FROM shop_sliders WHERE tenant_id = ? ORDER BY sort_order ASC", [$this->tenantId]);
        $sliders = $stmt->fetchAll();
        require_once __DIR__ . '/../Views/admin/sliders.php';
    }

    public function storeSlider()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $badge_text = $_POST['badge_text'] ?? null;
            $badge_color = $_POST['badge_color'] ?? 'primary';
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? null;
            $button_text = $_POST['button_text'] ?? null;
            $button_link = $_POST['button_link'] ?? null;
            $button_color = $_POST['button_color'] ?? 'primary';
            $background_overlay = $_POST['background_overlay'] ?? 'rgba(255,255,255,0.2)';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $sort_order = $_POST['sort_order'] ?? 0;
            $image_url = $_POST['image_url'] ?? null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../../public/uploads/shop/sliders/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('slider_') . '.' . $ext;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                    $image_url = '/uploads/shop/sliders/' . $filename;
                }
            }

            $sql = "INSERT INTO shop_sliders (tenant_id, badge_text, badge_color, title, subtitle, button_text, button_link, button_color, image_url, background_overlay, is_active, sort_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($sql, [
                $this->tenantId, $badge_text, $badge_color, $title, $subtitle, $button_text, $button_link, $button_color, $image_url, $background_overlay, $is_active, $sort_order
            ]);

            header('Location: /' . ADMIN_PATH . '/shop/sliders?success=1');
            exit;
        }
    }

    public function deleteSlider($params)
    {
        $id = is_array($params) ? $params['id'] : $params;
        $this->db->query("DELETE FROM shop_sliders WHERE id = ? AND tenant_id = ?", [$id, $this->tenantId]);
        header('Location: /' . ADMIN_PATH . '/shop/sliders?deleted=1');
        exit;
    }
}


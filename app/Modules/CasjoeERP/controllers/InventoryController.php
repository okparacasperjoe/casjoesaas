<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class InventoryController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_inventory_items WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtTenant = $this->pdo->prepare("SELECT currency FROM tenants WHERE id = ?");
        $stmtTenant->execute([$this->tenantId]);
        $tenantData = $stmtTenant->fetch(PDO::FETCH_ASSOC) ?: ['currency' => '$'];

        $cCode = $tenantData['currency'] ?? '$';
        $currencySymbol = $cCode;
        if ($cCode === 'NGN') $currencySymbol = '₦';
        if ($cCode === 'USD') $currencySymbol = '$';
        if ($cCode === 'GBP') $currencySymbol = '£';
        if ($cCode === 'EUR') $currencySymbol = '€';

        require __DIR__ . '/../Views/inventory/index.php';
    }

    public function store()
    {
        $sku = $_POST['sku'];
        $name = $_POST['name'];
        $price = $_POST['unit_price'];
        $stock = $_POST['stock_quantity'];

        $stmt = $this->pdo->prepare("INSERT INTO erp_inventory_items (tenant_id, sku, name, unit_price, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $sku, $name, $price, $stock]);

        header('Location: /erp/inventory');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /erp/inventory');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_inventory_items WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            header('Location: /erp/inventory');
            exit;
        }

        $stmtLoc = $this->pdo->prepare("SELECT * FROM erp_locations WHERE tenant_id = ?");
        $stmtLoc->execute([$this->tenantId]);
        $locations = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/inventory/edit.php';
    }

    public function update()
    {
        $id = $_POST['id'];
        $sku = $_POST['sku'];
        $name = $_POST['name'];
        $price = $_POST['unit_price'];
        $stock = $_POST['stock_quantity'];
        
        $role = \App\Core\Auth::user()['role'] ?? '';
        $isAdmin = ($role === 'admin');

        if ($isAdmin && isset($_POST['location_id'])) {
            $locationId = $_POST['location_id'] ?: null;
            $stmt = $this->pdo->prepare("UPDATE erp_inventory_items SET sku = ?, name = ?, unit_price = ?, stock_quantity = ?, location_id = ? WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$sku, $name, $price, $stock, $locationId, $id, $this->tenantId]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE erp_inventory_items SET sku = ?, name = ?, unit_price = ?, stock_quantity = ? WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$sku, $name, $price, $stock, $id, $this->tenantId]);
        }

        header('Location: /erp/inventory');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_inventory_items WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        header('Location: /erp/inventory');
        exit;
    }
}

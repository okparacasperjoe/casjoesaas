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

        require __DIR__ . '/../views/inventory/index.php';
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
}

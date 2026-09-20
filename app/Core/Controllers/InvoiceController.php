<?php

namespace App\Core\Controllers;

use App\Core\Database;

class InvoiceController
{
    public function view($token)
    {
        $pdo = Database::getInstance()->getConnection();
        
        $stmt = $pdo->prepare("SELECT i.*, t.name as tenant_name, t.currency FROM erp_invoices i JOIN tenants t ON i.tenant_id = t.id WHERE i.token = ?");
        $stmt->execute([$token]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$invoice) {
            die("Invoice not found or link is invalid.");
        }

        require __DIR__ . '/../Views/invoice_view.php';
    }
    
    public function pay($token)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE erp_invoices SET status = 'paid' WHERE token = ?");
        $stmt->execute([$token]);
        
        header("Location: /invoice/view/" . $token . "?success=1");
        exit;
    }
}

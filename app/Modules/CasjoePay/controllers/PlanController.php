<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Auth;
use App\Core\Database;

class PlanController
{
    // Admin: List all Plans
    public function index()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM pay_plans WHERE tenant_id = ?", [Auth::user()['tenant_id']]);
        $plans = $stmt->fetchAll();

        require __DIR__ . '/../Views/admin/plans/index.php';
    }

    // Admin: Create Form
    public function create()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            die('Unauthorized');
        }
        require __DIR__ . '/../Views/admin/plans/create.php';
    }

    // Admin: Store Plan
    public function store()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            die('Unauthorized');
        }

        $name = $_POST['name'];
        $price = $_POST['price'];
        $interval = $_POST['interval']; // monthly/yearly
        
        // Features come as array of strings, we JSON encode them
        $featuresRaw = $_POST['features'] ?? ''; 
        // Simple splitting by newline for MVP input
        $features = array_filter(array_map('trim', explode("\n", $featuresRaw)));
        $featuresJson = json_encode(array_values($features));

        $db = Database::getInstance();
        $db->query("INSERT INTO pay_plans (tenant_id, name, price, billing_interval, features) VALUES (?, ?, ?, ?, ?)", 
            [Auth::user()['tenant_id'], $name, $price, $interval, $featuresJson]
        );

        header('Location: /pay/plans');
    }
    
    // Public: API to get plans (for frontend display if needed)
    public function apiList() {
        $db = Database::getInstance();
        // Just fetch first tenant's plans for demo, or based on query param
        $stmt = $db->query("SELECT * FROM pay_plans ORDER BY price ASC");
        echo json_encode($stmt->fetchAll());
    }
}


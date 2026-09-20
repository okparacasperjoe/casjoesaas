<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();

try {
    // 1. Subscription Plans (Admin defined tiers)
    $db->query("CREATE TABLE IF NOT EXISTS pay_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL, 
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        billing_interval ENUM('monthly', 'yearly') DEFAULT 'monthly',
        features JSON, -- List of features like ['5 Users', '10GB Storage']
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (tenant_id)
    )");

    // 2. Subscriptions (User instances of a plan)
    $db->query("CREATE TABLE IF NOT EXISTS pay_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        plan_id INT NOT NULL,
        status ENUM('active', 'past_due', 'cancelled') DEFAULT 'active',
        current_period_start DATE,
        current_period_end DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (plan_id) REFERENCES pay_plans(id),
        INDEX (user_id),
        INDEX (plan_id)
    )");

    // 3. Invoices (Billing history)
    $db->query("CREATE TABLE IF NOT EXISTS pay_invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subscription_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        status ENUM('paid', 'unpaid', 'void') DEFAULT 'unpaid',
        issued_date DATE DEFAULT CURRENT_DATE,
        pdf_url VARCHAR(255),
        FOREIGN KEY (subscription_id) REFERENCES pay_subscriptions(id),
        INDEX (subscription_id)
    )");

    echo "Schema updated: pay_plans, pay_subscriptions, pay_invoices tables created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?step=3');
    exit;
}

require __DIR__ . '/../../vendor/autoload.php'; // Ensure autoloader exists for password hashing etc.

$name = $_POST['admin_name'] ?? 'Admin';
$email = $_POST['admin_email'] ?? '';
$pass = $_POST['admin_pass'] ?? '';

$dbConfig = require __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Primary Tenant (Workspace)
    $stmt = $pdo->prepare("INSERT INTO tenants (name, plan_type, status) VALUES (?, 'all-access', 'active')");
    $stmt->execute(['Primary Workspace']);
    $tenantId = $pdo->lastInsertId();

    // 2. Create Super Admin User
    // Use password_hash for security
    $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (tenant_id, name, email, password, role, email_verified) VALUES (?, ?, ?, ?, 'admin', 1)");
    $stmt->execute([$tenantId, $name, $email, $hashedPass]);

    // 3. Seed Default System Settings
    $settings = [
        ['site_name', 'Casjoe Business SaaS', 'general'],
        ['site_email', $email, 'general'],
        ['currency', 'USD', 'billing'],
        ['stripe_enabled', '0', 'integrations'],
        ['license_code', $_SESSION['license_code'] ?? '', 'security'],
        ['license_status', 'verified', 'security']
    ];

    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)");
    foreach ($settings as $s) {
        $stmt->execute($s);
    }

    header('Location: index.php?step=4');
} catch (PDOException $e) {
    die("Installation Failed: Admin Creation Error - " . $e->getMessage());
}

<?php
// Admin Settings View
$tab = $_GET['tab'] ?? 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | Casjoe Admin</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .settings-container { display: flex; gap: 30px; margin-top: 20px; }
        .settings-sidebar { width: 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .settings-content { flex: 1; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .settings-nav a { display: block; padding: 10px 15px; text-decoration: none; color: #555; border-radius: 5px; margin-bottom: 5px; transition: background 0.2s; }
        .settings-nav a:hover { background: #f0f0f0; }
        .settings-nav a.active { background: var(--secondary); color: white; font-weight: bold; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #fafafa; }
        .toggle-switch { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .toggle-bg { width: 50px; height: 26px; background: #ccc; border-radius: 13px; position: relative; transition: background 0.3s; }
        .toggle-bg::after { content: ''; position: absolute; left: 3px; top: 3px; width: 20px; height: 20px; background: white; border-radius: 50%; transition: left 0.3s; }
        input[type="checkbox"]:checked + .toggle-bg { background: var(--secondary); }
        input[type="checkbox"]:checked + .toggle-bg::after { left: 27px; }
    </style>
</head>
<body>
<div class="app-container">
    <?php 
    $sidebarPath = __DIR__ . '/../../../Modules/CasjoeERP/views/layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        require $sidebarPath; 
    }
    ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>System Settings</h2>
            <div class="user-profile">
                <span>Super Admin</span>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                Settings saved successfully!
            </div>
        <?php endif; ?>

        <div class="settings-container">
            <aside class="settings-sidebar">
                <nav class="settings-nav">
                    <a href="?tab=general" class="<?= $tab == 'general' ? 'active' : '' ?>">
                        <ion-icon name="options-outline"></ion-icon> General
                    </a>
                    <a href="?tab=payments" class="<?= $tab == 'payments' ? 'active' : '' ?>">
                        <ion-icon name="card-outline"></ion-icon> Payments (Gateways)
                    </a>
                    <a href="?tab=security" class="<?= $tab == 'security' ? 'active' : '' ?>">
                        <ion-icon name="shield-checkmark-outline"></ion-icon> Security
                    </a>
                </nav>
            </aside>

            <section class="settings-content">
                <form method="POST" action="/admin/settings/update">
                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                <input type="hidden" name="tab" value="<?= $tab ?>">

                    <?php if ($tab == 'general'): ?>
                        <h3>General Configuration</h3>
                        
                        <div class="card" style="background: #fff4e5; border: 1px solid #ffcc80; padding: 20px; margin-bottom: 20px;">
                            <h4 style="margin-top: 0; color: #e65100;">Payment Routing Mode</h4>
                            <p style="font-size: 0.9em; margin-bottom: 15px;">Determine whose API keys are used for processing transactions.</p>
                            
                            <label class="toggle-switch">
                                <input type="radio" name="payment_routing_mode" value="global" <?= ($settings['payment_routing_mode'] ?? 'global') == 'global' ? 'checked' : '' ?>> 
                                <strong>Global (Super Admin)</strong> - Use keys defined in Payment Settings (Best for Single SaaS)
                            </label>
                            <br>
                            <label class="toggle-switch">
                                <input type="radio" name="payment_routing_mode" value="tenant" <?= ($settings['payment_routing_mode'] ?? '') == 'tenant' ? 'checked' : '' ?>> 
                                <strong>Tenant Control</strong> - Allow tenants to set their own keys (Best for White Label/Multi-Vendor)
                            </label>
                            
                            <?php if (($settings['payment_routing_mode'] ?? 'global') == 'global'): ?>
                                <p style="margin-top: 10px; color: green; font-weight: bold;"><ion-icon name="checkmark-circle"></ion-icon> Currently using YOUR Global Admin Keys.</p>
                            <?php else: ?>
                                <p style="margin-top: 10px; color: orange; font-weight: bold;"><ion-icon name="warning"></ion-icon> Currently expecting TENANT keys.</p>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($tab == 'payments'): ?>
                        <h3>Payment Gateways</h3>
                        <p style="color: #666; margin-bottom: 20px;">Configure your global API credentials. These act as the master keys for the platform.</p>

                        <!-- Flutterwave -->
                        <div style="border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                <img src="https://asset.brandfetch.io/idAnM3w5m0/id9d4sC-Q0.png" style="height: 24px;"> 
                                <h4 style="margin: 0;">Flutterwave</h4>
                            </div>
                            <div class="form-group">
                                <label>Public Key</label>
                                <input type="text" name="flutterwave_public_key" class="form-control" value="<?= htmlspecialchars($settings['flutterwave_public_key'] ?? '') ?>" placeholder="FLWPUBK_...">
                            </div>
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="password" name="flutterwave_secret_key" class="form-control" value="<?= htmlspecialchars($settings['flutterwave_secret_key'] ?? '') ?>" placeholder="FLWSECK_...">
                            </div>
                        </div>

                        <!-- Paystack -->
                        <div style="border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                             <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                <h4 style="margin: 0;">Paystack</h4>
                            </div>
                            <div class="form-group">
                                <label>Public Key</label>
                                <input type="text" name="paystack_public_key" class="form-control" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>" placeholder="pk_live_...">
                            </div>
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="password" name="paystack_secret_key" class="form-control" value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>" placeholder="sk_live_...">
                            </div>
                        </div>

                        <!-- Sudo (Virtual Cards) -->
                        <div style="border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                <h4 style="margin: 0;">Sudo (Virtual Cards)</h4>
                            </div>
                            <div class="form-group">
                                <label>API Key</label>
                                <input type="text" name="sudo_api_key" class="form-control" value="<?= htmlspecialchars($settings['sudo_api_key'] ?? '') ?>" placeholder="apiKey...">
                            </div>
                            <div class="form-group">
                                <label>API Secret (or Private Key)</label>
                                <textarea name="sudo_api_secret" class="form-control" rows="3" placeholder="Paste private key content..."><?= htmlspecialchars($settings['sudo_api_secret'] ?? '') ?></textarea>
                            </div>
                        </div>

                    <?php elseif ($tab == 'security'): ?>
                        <h3>Security Settings</h3>
                        <p>Configure global security policies.</p>
                        <!-- Placeholders -->
                        <div class="card" style="text-align: center; padding: 40px;">
                            <ion-icon name="shield-half-outline" style="font-size: 64px; color: var(--primary);"></ion-icon>
                            <h3>Two-Factor Authentication</h3>
                            <p>Protect your account with an extra layer of security.</p>
                            <a href="/security" class="btn" style="display: inline-block; margin-top: 15px;">Manage 2FA Settings</a>
                        </div>
                        
                        <div class="form-group" style="margin-top: 30px; opacity: 0.5;">
                            <label>Session Timeout (Minutes) - <i>Coming Soon</i></label>
                            <input type="number" name="session_timeout" class="form-control" value="60" disabled>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn" style="padding: 12px 30px;">Save Changes</button>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>

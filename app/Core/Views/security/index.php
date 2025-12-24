<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/billing" class="nav-link"><ion-icon name="card-outline"></ion-icon> Billing</a></li>
            <li class="nav-item"><a href="/security" class="nav-link active"><ion-icon name="shield-checkmark-outline"></ion-icon> Security</a></li>
            <li class="nav-item"><a href="/support" class="nav-link"><ion-icon name="headset-outline"></ion-icon> Support</a></li>
            <li class="nav-item"><a href="/logout" class="nav-link"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Security Settings</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="padding: 15px; background: #e6fffa; color: #047857; border-radius: 10px; margin-bottom: 20px;">
                <?php if ($_GET['success'] == '2fa_disabled'): ?>
                    Two-Factor Authentication has been disabled.
                <?php else: ?>
                    Success!
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Two-Factor Authentication</h3>
            
            <?php if ($twoFactorEnabled): ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: green; margin-top: 5px;">
                            <ion-icon name="checkmark-circle-outline" style="vertical-align: middle;"></ion-icon> Enabled
                        </div>
                        <p style="color: #666; font-size: 0.9rem;">Your account is secured with 2FA.</p>
                    </div>
                    <form method="POST" action="/security/2fa/disable" onsubmit="return confirm('Are you sure you want to disable 2FA? Account security will be reduced.')">
                        <?= \App\Core\Services\CsrfService::getTokenField() ?>
                        <button class="btn" style="background: white; color: red; border: 1px solid red;">Disable 2FA</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <div style="color: red; margin-top: 5px;">
                            <ion-icon name="warning-outline" style="vertical-align: middle;"></ion-icon> Disabled
                        </div>
                        <p style="color: #666; font-size: 0.9rem;">Add an extra layer of security to your account by enabling 2FA.</p>
                    </div>

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #eee;">
                        <h4 style="margin-top: 0;">Setup 2FA</h4>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">Scan the QR code below with your authenticator app (e.g. Google Authenticator).</p>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
                            <div style="background: white; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                                <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="width: 150px; height: 150px;">
                            </div>
                            
                            <div style="flex: 1; min-width: 250px;">
                                <p style="font-family: monospace; background: #eef; padding: 10px; border-radius: 5px; color: #333; margin-bottom: 20px;">
                                    Secret: <?= $secret ?>
                                </p>

                                <form method="POST" action="/2fa/setup/verify">
                                    <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                    <input type="hidden" name="secret" value="<?= $secret ?>">
                                    <div class="form-group" style="margin-bottom: 15px;">
                                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Enter Verification Code</label>
                                        <input type="text" name="code" class="form-control" style="width: 100%; padding: 10px; font-size: 1.2rem; letter-spacing: 3px; border: 1px solid #ddd; border-radius: 5px;" placeholder="000000" maxlength="6" required>
                                    </div>
                                    <button type="submit" class="btn">Verify & Enable</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>

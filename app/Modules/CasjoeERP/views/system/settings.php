<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Settings | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>System Settings</h2>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 600;">
                &#10003; System Settings and Organization Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['api_generated'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 600;">
                &#10003; New API Key generated successfully! Please copy it below.
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 650px;">
            <form method="POST" action="/erp/settings/update" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700; margin-bottom: 8px; display: block;">Organization Logo</label>
                    <?php if (!empty($tenant['logo'])): ?>
                        <div style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 8px; display: inline-block; border: 1px solid #e9ecef;">
                            <img src="<?= htmlspecialchars($tenant['logo']) ?>" alt="Current Logo" style="max-height: 60px; max-width: 180px; object-fit: contain;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control" accept="image/*" style="padding: 10px;">
                    <small style="color: #6c757d; display: block; margin-top: 4px;">Upload PNG, JPG, or WEBP. Appears on invoices and top command bar.</small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700;">Company / Organization Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($settings['company_name'] ?? ($tenant['name'] ?? '')) ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="font-weight: 700;">Country</label>
                        <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($tenant['country'] ?? 'Nigeria') ?>" placeholder="e.g. Nigeria">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 700;">Default Currency</label>
                        <select name="currency" class="form-control">
                            <?php $curr = $tenant['currency'] ?? 'NGN'; ?>
                            <option value="NGN" <?= $curr === 'NGN' ? 'selected' : '' ?>>NGN (&#8358;)</option>
                            <option value="USD" <?= $curr === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                            <option value="GBP" <?= $curr === 'GBP' ? 'selected' : '' ?>>GBP (&pound;)</option>
                            <option value="EUR" <?= $curr === 'EUR' ? 'selected' : '' ?>>EUR (&euro;)</option>
                            <option value="ZAR" <?= $curr === 'ZAR' ? 'selected' : '' ?>>ZAR (R)</option>
                            <option value="KES" <?= $curr === 'KES' ? 'selected' : '' ?>>KES (KSh)</option>
                            <option value="GHS" <?= $curr === 'GHS' ? 'selected' : '' ?>>GHS (GH&cent;)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700;">Timezone</label>
                    <select name="timezone" class="form-control">
                        <option value="UTC" <?= ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' ?>>UTC (Coordinated Universal Time)</option>
                        <option value="Africa/Lagos" <?= ($settings['timezone'] ?? '') == 'Africa/Lagos' ? 'selected' : '' ?>>Africa/Lagos (WAT)</option>
                        <option value="EST" <?= ($settings['timezone'] ?? '') == 'EST' ? 'selected' : '' ?>>EST (Eastern Standard Time)</option>
                        <option value="PST" <?= ($settings['timezone'] ?? '') == 'PST' ? 'selected' : '' ?>>PST (Pacific Standard Time)</option>
                    </select>
                </div>

                <div style="margin-top: 30px; margin-bottom: 20px; border-top: 1px solid #e9ecef; padding-top: 20px;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; color: #000066;">Social Media Links</h4>
                    <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 20px;">These links will appear in the footer of emails sent by your organization.</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label style="font-size: 0.9rem; font-weight: 600;"><ion-icon name="logo-facebook" style="vertical-align: text-bottom; color: #1877F2;"></ion-icon> Facebook</label>
                            <input type="url" name="social_facebook" class="form-control" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.9rem; font-weight: 600;"><ion-icon name="logo-twitter" style="vertical-align: text-bottom; color: #1DA1F2;"></ion-icon> X (Twitter)</label>
                            <input type="url" name="social_twitter" class="form-control" value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>" placeholder="https://x.com/yourhandle">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.9rem; font-weight: 600;"><ion-icon name="logo-instagram" style="vertical-align: text-bottom; color: #E4405F;"></ion-icon> Instagram</label>
                            <input type="url" name="social_instagram" class="form-control" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/yourprofile">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.9rem; font-weight: 600;"><ion-icon name="logo-linkedin" style="vertical-align: text-bottom; color: #0A66C2;"></ion-icon> LinkedIn</label>
                            <input type="url" name="social_linkedin" class="form-control" value="<?= htmlspecialchars($settings['social_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.9rem; font-weight: 600;"><ion-icon name="logo-youtube" style="vertical-align: text-bottom; color: #FF0000;"></ion-icon> YouTube</label>
                            <input type="url" name="social_youtube" class="form-control" value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/c/yourchannel">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <button type="submit" class="btn" style="width: 100%; padding: 12px; font-weight: 800; font-size: 1rem;">Save Organization Settings</button>
                </div>
            </form>
        </div>

        <div class="card" style="max-width: 650px; margin-top: 30px;">
            <div style="margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 1.25rem;">API Key & Integrations</h3>
                <p style="color: #6c757d; margin-top: 5px; font-size: 0.95rem;">Use this Account API Key to connect your Casjoe BOS/ERP data with external AI agents and third-party apps.</p>
            </div>
            
            <form method="POST" action="/erp/settings/api-key/generate" onsubmit="return confirm('Are you sure you want to generate a new API key? This will invalidate any existing connections using the old key.');">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-weight: 700;">Your Account API Key / Secret Token</label>
                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                        <input type="text" readonly value="<?= htmlspecialchars($settings['api_secret_token'] ?? 'No API Key generated yet') ?>" class="form-control" style="background: #f8f9fa; cursor: copy;" onclick="this.select(); document.execCommand('copy'); alert('Copied to clipboard!');" title="Click to copy">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap; font-weight: 600;">Generate New Key</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card" style="max-width: 650px; margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
                        <ion-icon name="calculator-outline" style="color: #0284c7;"></ion-icon> Moniepoint POS Integration
                    </h3>
                    <p style="color: #6c757d; margin: 5px 0 0 0; font-size: 0.95rem;">Connect your Moniepoint Smart POS terminal to push payment prompts and sync inflows directly.</p>
                </div>
                <a href="/erp/settings/moniepoint" class="btn btn-primary" style="white-space: nowrap; text-decoration: none; padding: 10px 18px; font-size: 0.9rem; font-weight: 700;">Configure POS</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>

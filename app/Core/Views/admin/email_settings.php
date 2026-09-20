<?php
$pageTitle = 'Email & Notifications';
require __DIR__ . '/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .settings-grid {
            display: grid;
            gap: 25px;
        }
        
        .settings-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--glass-shadow);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        
        .settings-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 166, 0, 0.3);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-hover) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            margin: 0;
        }
        
        .card-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-color);
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.1);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: 0.4s;
            border-radius: 30px;
            border: 1px solid var(--glass-border);
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-hover) 100%);
            border-color: var(--secondary);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }
        
        .social-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .social-icon-preview {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-hover) 100%);
            color: var(--primary);
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 166, 0, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            padding: 14px 30px;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .help-text {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 5px;
        }
        
        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 2px solid var(--glass-border);
            cursor: pointer;
        }
    </style>
        <div class="top-bar">
            <h2><ion-icon name="mail-outline" style="vertical-align: middle;"></ion-icon> Email & Notifications</h2>
        </div>

        <div class="settings-container">
            <form action="/<?= ADMIN_PATH ?>/email-settings/update" method="POST" id="emailSettingsForm">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                
                <div class="settings-grid">
                    <!-- SMTP Configuration -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-icon">
                                <ion-icon name="send-outline"></ion-icon>
                            </div>
                            <div>
                                <h3 class="card-title">SMTP Configuration</h3>
                                <p class="card-subtitle">Configure your email sending settings</p>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: center; justify-content: space-between;">
                                <span>Enable SMTP</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="smtp_enabled" value="1" <?= ($settings['smtp_enabled'] ?? '') == '1' ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </label>
                            <p class="help-text">Use SMTP server for sending emails (recommended for production)</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                            </div>
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" name="smtp_username" class="form-control" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" name="smtp_password" class="form-control" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>Encryption</label>
                                <select name="smtp_encryption" class="form-control">
                                    <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="" <?= empty($settings['smtp_encryption']) ? 'selected' : '' ?>>None</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-grid" style="margin-top: 20px;">
                            <div class="form-group">
                                <label>From Email</label>
                                <input type="email" name="smtp_from_address" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_address'] ?? '') ?>" placeholder="noreply@casjoe.com">
                            </div>
                            <div class="form-group">
                                <label>From Name</label>
                                <input type="text" name="smtp_from_name" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Casjoe') ?>" placeholder="Casjoe">
                            </div>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="button" class="btn-secondary" onclick="testEmail()">
                                <ion-icon name="paper-plane-outline" style="vertical-align: middle;"></ion-icon> Send Test Email
                            </button>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-icon">
                                <ion-icon name="share-social-outline"></ion-icon>
                            </div>
                            <div>
                                <h3 class="card-title">Social Media</h3>
                                <p class="card-subtitle">Add your social media links to email footers</p>
                            </div>
                        </div>
                        
                        <div style="display: grid; gap: 15px;">
                            <div class="form-group">
                                <label>Facebook</label>
                                <div class="social-input-group">
                                    <div class="social-icon-preview" style="background: #1877F2;">
                                        <ion-icon name="logo-facebook"></ion-icon>
                                    </div>
                                    <input type="url" name="social_facebook" class="form-control" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/casjoe">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Twitter / X</label>
                                <div class="social-input-group">
                                    <div class="social-icon-preview" style="background: #1DA1F2;">
                                        <ion-icon name="logo-twitter"></ion-icon>
                                    </div>
                                    <input type="url" name="social_twitter" class="form-control" value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>" placeholder="https://twitter.com/casjoe">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Instagram</label>
                                <div class="social-input-group">
                                    <div class="social-icon-preview" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);">
                                        <ion-icon name="logo-instagram"></ion-icon>
                                    </div>
                                    <input type="url" name="social_instagram" class="form-control" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/casjoe">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>LinkedIn</label>
                                <div class="social-input-group">
                                    <div class="social-icon-preview" style="background: #0A66C2;">
                                        <ion-icon name="logo-linkedin"></ion-icon>
                                    </div>
                                    <input type="url" name="social_linkedin" class="form-control" value="<?= htmlspecialchars($settings['social_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/casjoe">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>YouTube</label>
                                <div class="social-input-group">
                                    <div class="social-icon-preview" style="background: #FF0000;">
                                        <ion-icon name="logo-youtube"></ion-icon>
                                    </div>
                                    <input type="url" name="social_youtube" class="form-control" value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/@casjoe">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email Branding -->
                    <div class="settings-card">
                        <div class="card-header">
                            <div class="card-icon">
                                <ion-icon name="color-palette-outline"></ion-icon>
                            </div>
                            <div>
                                <h3 class="card-title">Email Branding</h3>
                                <p class="card-subtitle">Customize the look of your emails</p>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Logo URL</label>
                            <input type="url" name="email_logo_url" class="form-control" value="<?= htmlspecialchars($settings['email_logo_url'] ?? 'https://app.casjoe.com/assets/casjoe_logo.webp') ?>" placeholder="https://app.casjoe.com/assets/casjoe_logo.webp">
                            <p class="help-text">Full URL to your logo image (recommended: 200x60px)</p>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Brand Color</label>
                            <div class="color-picker-wrapper">
                                <input type="color" id="brandColorPicker" value="<?= htmlspecialchars($settings['email_brand_color'] ?? '#000066') ?>" onchange="document.getElementById('brandColorInput').value = this.value">
                                <input type="text" name="email_brand_color" id="brandColorInput" class="form-control" value="<?= htmlspecialchars($settings['email_brand_color'] ?? '#000066') ?>" placeholder="#000066" style="flex: 1;">
                            </div>
                            <p class="help-text">Primary color for email headers and buttons</p>
                        </div>
                        
                        <div class="form-group">
                            <label>Support Email</label>
                            <input type="email" name="email_support_email" class="form-control" value="<?= htmlspecialchars($settings['email_support_email'] ?? 'support@casjoe.com') ?>" placeholder="support@casjoe.com">
                            <p class="help-text">Displayed in email footers for support inquiries</p>
                        </div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        <ion-icon name="checkmark-outline" style="vertical-align: middle;"></ion-icon> Save Settings
                    </button>
                </div>
            </form>
        </div>
<script>
function testEmail() {
    Swal.fire({
        title: 'Send Test Email',
        input: 'email',
        inputLabel: 'Enter your email address',
        inputPlaceholder: 'your@email.com',
        showCancelButton: true,
        confirmButtonText: 'Send',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return fetch('/<?= ADMIN_PATH ?>/email-settings/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to send test email')
                }
                return data
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`)
            })
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Test Email Sent!',
                text: 'Check your inbox to verify email configuration.',
                confirmButtonColor: '#FFA600'
            })
        }
    })
}
</script>
<?php require __DIR__ . '/footer.php'; ?>

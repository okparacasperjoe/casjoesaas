<?php
$pageTitle = 'Moderator Permissions';
require __DIR__ . '/header.php';
?>
<style>
        :root {
            --card-bg: #ffffff;
            --text-main: #333333;
            --text-light: #666666;
            --border-color: #eee;
            --header-color: #000066;
        }
        
        /* Dark Mode Support (if parent has dark-theme class or media query) */
        @media (prefers-color-scheme: dark) {
            :root {
                --card-bg: #1e1e2d;
                --text-main: #e0e0e0;
                --text-light: #a0a0a0;
                --border-color: #2b2b40;
                --header-color: #ffffff;
            }
        }
        
        .permissions-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .permissions-card h3 {
            color: var(--header-color);
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .permission-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 10px;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.2s;
            border-radius: 6px;
        }
        .permission-item:hover {
            background: rgba(0,0,0,0.02);
        }
        .permission-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 3px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #4CAF50;
        }
        .permission-item label {
            cursor: pointer;
            flex: 1;
            margin: 0;
        }
        .permission-title {
            font-weight: 600;
            color: var(--text-main);
            display: block;
            margin-bottom: 4px;
        }
        .permission-description {
            color: var(--text-light);
            font-size: 12px;
            line-height: 1.4;
        }
        
        /* Badges */
        .risk-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .risk-high { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .risk-medium { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .risk-safe { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        
        /* Dark mode overrides for badges */
        @media (prefers-color-scheme: dark) {
            .risk-high { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
            .risk-medium { background: #451a03; color: #fcd34d; border-color: #78350f; }
            .risk-safe { background: #064e3b; color: #6ee7b7; border-color: #065f46; }
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .main-content h2 {
            color: var(--header-color) !important;
        }
        .main-content p {
            color: var(--text-light) !important;
        }
        /* Override if global container is forcing something else */
        .app-container {
            background-color: #f8f9fa;
        }
        @media (prefers-color-scheme: dark) {
            .app-container {
                background-color: #151521;
            }
        }
    </style>
        <div style="padding: 20px;">
            <h2 style="color: #ffffff; margin-top: 0;">⚙️ Moderator Permissions</h2>
            <p style="color: #94a3b8; margin-bottom: 30px;">Configure which features moderators can access in the admin panel. Changes take effect immediately.</p>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            ✓ Moderator permissions updated successfully!
        </div>
        <?php endif; ?>
        
        <form method="POST" action="/<?= ADMIN_PATH ?>/moderator-permissions/update">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            
            <!-- User Management -->
            <div class="permissions-card">
                <h3>👥 User Management <span class="risk-badge risk-safe">SAFE</span></h3>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_users" name="moderator_can_manage_users" value="1" 
                           <?= ($permissions['manage_users'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_users">
                        <span class="permission-title">Can manage users</span>
                        <div class="permission-description">View, edit, and manage user accounts</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_kyc" name="moderator_can_manage_kyc" value="1" 
                           <?= ($permissions['manage_kyc'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_kyc">
                        <span class="permission-title">Can manage KYC</span>
                        <div class="permission-description">Review and approve/reject ID verifications</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="delete_admins" name="moderator_can_delete_admins" value="1" 
                           <?= ($permissions['delete_admins'] ?? false) ? 'checked' : '' ?>>
                    <label for="delete_admins">
                        Can delete admin users <span class="risk-badge risk-medium">MEDIUM RISK</span>
                        <div class="permission-description">Allow deletion of other administrator accounts</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="impersonate" name="moderator_can_impersonate" value="1" 
                           <?= ($permissions['impersonate'] ?? false) ? 'checked' : '' ?>>
                    <label for="impersonate">
                        Can impersonate users <span class="risk-badge risk-medium">MEDIUM RISK</span>
                        <div class="permission-description">Login as any user for support purposes</div>
                    </label>
                </div>
            </div>
            
            <!-- Security & Monitoring -->
            <div class="permissions-card">
                <h3>🛡️ Security & Monitoring <span class="risk-badge risk-safe">SAFE</span></h3>
                
                <div class="permission-item">
                    <input type="checkbox" id="view_soc" name="moderator_can_view_soc" value="1" 
                           <?= ($permissions['view_soc'] ?? false) ? 'checked' : '' ?>>
                    <label for="view_soc">
                        <span class="permission-title">View SOC Dashboard</span>
                        <div class="permission-description">Access Security Operations Center logs</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="train_bot" name="moderator_can_train_bot" value="1" 
                           <?= ($permissions['train_bot'] ?? false) ? 'checked' : '' ?>>
                    <label for="train_bot">
                        <span class="permission-title">Can train chatbot</span>
                        <div class="permission-description">Add and edit chatbot training data</div>
                    </label>
                </div>
            </div>
            
            <!-- Store & Commerce -->
            <div class="permissions-card">
                <h3>🏪 Store & Commerce <span class="risk-badge risk-safe">SAFE</span></h3>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_stores" name="moderator_can_manage_stores" value="1" 
                           <?= ($permissions['manage_stores'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_stores">
                        <span class="permission-title">Can manage store approvals</span>
                        <div class="permission-description">Approve or reject vendor store applications</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_coupons" name="moderator_can_manage_coupons" value="1" 
                           <?= ($permissions['manage_coupons'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_coupons">
                        Can manage coupons <span class="risk-badge risk-medium">MEDIUM RISK</span>
                        <div class="permission-description">Create, edit, and delete discount coupons</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_shop_ads" name="moderator_can_manage_shop_ads" value="1" 
                           <?= ($permissions['manage_shop_ads'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_shop_ads">
                        <span class="permission-title">Can manage shop ads</span>
                        <div class="permission-description">Manage featured advertisements in the shop</div>
                    </label>
                </div>
            </div>
            
            <!-- Communication -->
            <div class="permissions-card">
                <h3>📢 Communication <span class="risk-badge risk-medium">MEDIUM RISK</span></h3>
                
                <div class="permission-item">
                    <input type="checkbox" id="send_broadcast" name="moderator_can_send_broadcast" value="1" 
                           <?= ($permissions['send_broadcast'] ?? false) ? 'checked' : '' ?>>
                    <label for="send_broadcast">
                        <span class="permission-title">Send Broadcasts</span>
                        <div class="permission-description">Send emails/notifications to all users</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_cms" name="moderator_can_manage_cms" value="1" 
                           <?= ($permissions['manage_cms'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_cms">
                        <span class="permission-title">Can manage CMS</span>
                        <div class="permission-description">Edit pages, blog posts, and public content</div>
                    </label>
                </div>
            </div>
            
            <!-- Financial Operations -->
            <div class="permissions-card">
                <h3>💰 Financial Operations <span class="risk-badge risk-high">HIGH RISK</span></h3>
                
                <div class="permission-item">
                    <input type="checkbox" id="view_deposits" name="moderator_can_view_deposits" value="1" 
                           <?= ($permissions['view_deposits'] ?? false) ? 'checked' : '' ?>>
                    <label for="view_deposits">
                        <span class="permission-title">Can view deposits</span>
                        <div class="permission-description">See pending deposit requests (view only)</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_deposits" name="moderator_can_manage_deposits" value="1" 
                           <?= ($permissions['manage_deposits'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_deposits">
                        <span class="permission-title">Can approve/reject deposits</span>
                        <div class="permission-description">Approve or reject user deposit requests</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="view_withdrawals" name="moderator_can_view_withdrawals" value="1" 
                           <?= ($permissions['view_withdrawals'] ?? false) ? 'checked' : '' ?>>
                    <label for="view_withdrawals">
                        <span class="permission-title">View Withdrawals only</span>
                        <div class="permission-description">View history without ability to act</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_withdrawals" name="moderator_can_manage_withdrawals" value="1" 
                           <?= ($permissions['manage_withdrawals'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_deposits">
                        <span class="permission-title">Manage Deposits (High Risk)</span>
                        <div class="permission-description">Approve or reject update_user_deposits</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="view_cards" name="moderator_can_view_cards" value="1" 
                           <?= ($permissions['view_cards'] ?? false) ? 'checked' : '' ?>>
                    <label for="view_cards">
                        <span class="permission-title">View Virtual Cards</span>
                        <div class="permission-description">See issued cards and balances</div>
                    </label>
                </div>
            </div>
            
            <!-- System Configuration -->
            <div class="permissions-card" style="border: 2px solid #dc3545;">
                <h3>⚠️ System Configuration <span class="risk-badge risk-high">CRITICAL</span></h3>
                <p style="color: #dc3545; font-size: 13px; margin-bottom: 15px;">
                    ⚠️ <strong>Warning:</strong> These permissions grant access to sensitive system settings. Only enable for highly trusted moderators.
                </p>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_modules" name="moderator_can_manage_modules" value="1" 
                           <?= ($permissions['manage_modules'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_modules">
                        <span class="permission-title">Manage Modules</span>
                        <div class="permission-description">Enable/Disable system modules (ERP, Shop, etc)</div>
                    </label>
                </div>
                
                <div class="permission-item">
                    <input type="checkbox" id="manage_settings" name="moderator_can_manage_settings" value="1" 
                           <?= ($permissions['manage_settings'] ?? false) ? 'checked' : '' ?>>
                    <label for="manage_settings">
                        <span class="permission-title">Manage System Settings</span>
                        <div class="permission-description">Change payment keys, site config, etc.</div>
                    </label>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn" style="background: #28a745; padding: 12px 30px; font-size: 16px;">
                    💾 Save Permissions
                </button>
                <a href="/<?= ADMIN_PATH ?>" class="btn" style="background: #6c757d; padding: 12px 30px; margin-left: 10px;">
                    Cancel
                </a>
            </div>
        </form>
        </div>
<?php require __DIR__ . '/footer.php'; ?>

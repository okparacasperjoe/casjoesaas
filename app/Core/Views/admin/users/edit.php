<?php
$pageTitle = 'Edit User';
require __DIR__ . '/../header.php';
?>
<div style="max-width: 600px; margin: 20px auto;">
    <div class="card" style="background: #13141f !important; border: 1px solid rgba(255, 166, 0, 0.2) !important; padding: 25px; border-radius: 14px;">
            <h2>Edit User</h2>
            <form method="POST" action="/<?= ADMIN_PATH ?>/users/update/<?= $user['id'] ?>">
                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label>Role</label>
                    <select name="role" class="support-input">
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div style="margin-bottom: 25px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="two_factor_enabled" value="1" <?= $user['two_factor_enabled'] ? 'checked' : '' ?>>
                        <span style="margin-left: 10px;">Two-Factor Authentication (2FA)</span>
                    </label>
                    <p style="font-size: 0.8em; color: #666; margin-top: 5px;">Uncheck to forcibly disable 2FA for this user if they are locked out.</p>
                </div>

                <!-- Future Plan Upgrade/Downgrade -->
                <!-- 
                <div style="margin-bottom: 15px;">
                    <label>Plan</label>
                    <select name="plan" class="support-input">
                        <option value="free">Free</option>
                        <option value="pro">Pro</option>
                    </select>
                </div>
                -->

                <button type="submit" class="btn">Update User</button>
                <a href="/<?= ADMIN_PATH ?>/users" class="btn" style="background: grey; text-align: center;">Cancel</a>
            </form>
    </div>
</div>
<?php require __DIR__ . '/../footer.php'; ?>

<?php
$pageTitle = 'User Management';
require __DIR__ . '/../header.php';
?>
<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.08); color: #e2e8f0; vertical-align: middle; }
    .admin-table th { background: #191b2a !important; color: #FFA600 !important; }
    .btn-action { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; margin-right: 5px; cursor: pointer; border: none; font-weight: 600; display: inline-block; }
    .btn-edit { background: #3498db; color: white; }
    .btn-delete { background: #e74c3c; color: white; }
    .btn-login { background: #f1c40f; color: black; }
    .top-nav { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: rgba(19, 20, 31, 0.8); border-radius: 12px; border: 1px solid rgba(255, 166, 0, 0.2); margin-bottom: 20px; }
</style>
        <div class="top-nav">
            <h2>User Management</h2>
            <span>Welcome, Admin</span>
        </div>

        <?php if (!empty($_SESSION['admin_success'])): ?>
            <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #34d399; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
                <?= htmlspecialchars($_SESSION['admin_success']) ?>
            </div>
            <?php unset($_SESSION['admin_success']); ?>
        <?php endif; ?>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Tenant</th>
                        <th>Status</th>
                        <th>Wallet PIN</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): 
                        $isLocked = !empty($user['pin_locked_until']) && strtotime($user['pin_locked_until']) > time();
                        $hasPin = !empty($user['transaction_pin']);
                    ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                        <td>
                            <span class="status-badge <?= $user['role'] == 'admin' ? 'status-active' : 'status-pending' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($user['tenant_name'] ?? 'N/A') ?></td>
                        <td><?= $user['is_verified'] ? 'Verified' : 'Unverified' ?></td>
                        <td>
                            <?php if ($isLocked): ?>
                                <?php $remainingMins = (int)ceil((strtotime($user['pin_locked_until']) - time()) / 60); ?>
                                <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                    🔒 Locked (<?= $remainingMins ?>m)
                                </span>
                            <?php elseif ($hasPin): ?>
                                <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                    ✓ Active
                                </span>
                            <?php else: ?>
                                <span style="background: rgba(148, 163, 184, 0.2); color: #94a3b8; padding: 3px 8px; border-radius: 4px; font-size: 11px;">
                                    Not Set
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                            <a href="/<?= ADMIN_PATH ?>/users/edit/<?= $user['id'] ?>" class="btn-action btn-edit">Edit</a>
                            
                            <?php if ($isLocked): ?>
                                <form method="POST" action="/<?= ADMIN_PATH ?>/users/unlock-pin/<?= $user['id'] ?>" onsubmit="return confirm('Unlock PIN for this user?');" style="margin: 0;">
                                    <button type="submit" class="btn-action" style="background: #10b981; color: white;">Unlock PIN</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($hasPin): ?>
                                <form method="POST" action="/<?= ADMIN_PATH ?>/users/reset-pin/<?= $user['id'] ?>" onsubmit="return confirm('Are you sure you want to reset this user\'s PIN? The old PIN will be cleared and the user will be prompted to set a new PIN upon their next login.');" style="margin: 0;">
                                    <button type="submit" class="btn-action" style="background: #f59e0b; color: #000;">Reset PIN</button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="/<?= ADMIN_PATH ?>/users/delete/<?= $user['id'] ?>" onsubmit="return confirm('Are you sure you want to delete this user?');" style="margin: 0;">
                                <button type="submit" class="btn-action btn-delete">Delete</button>
                            </form>

                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="/<?= ADMIN_PATH ?>/users/impersonate/<?= $user['id'] ?>" class="btn-action btn-login">Login As</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php require __DIR__ . '/../footer.php'; ?>

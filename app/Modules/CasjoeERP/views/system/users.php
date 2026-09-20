<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>User Management | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 25px; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; font-size: 1.2rem; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        select.form-control { appearance: auto; background-color: white; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Users</h2>
            <button class="btn" onclick="document.getElementById('inviteModal').classList.add('active')">Invite User</button>
        </div>
        
        <div style="background: #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0056b3;">
            <strong>Note:</strong> New users will be assigned the default password <code>Welcome123!</code>. Please have them log in and change it immediately.
        </div>

        <div class="card" style="overflow-x: auto;">
            <table style="width: 100%; min-width: 600px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; text-align: left;">
                        <th style="padding: 12px;">ID</th>
                        <th style="padding: 12px;">Name</th>
                        <th style="padding: 12px;">Email</th>
                        <th style="padding: 12px;">Role (Job Title)</th>
                        <th style="padding: 12px;">System Access</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><?= htmlspecialchars($u['id']) ?></td>
                                <td style="padding: 12px; font-weight: 600;"><?= htmlspecialchars($u['name'] ?? 'N/A') ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($u['email']) ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($u['user_role'] ?? 'Staff') ?></td>
                                <td style="padding: 12px;">
                                    <?php if($u['role'] === 'admin'): ?>
                                        <span style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Admin</span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">User</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if($u['is_verified']): ?>
                                        <span style="color: #16a34a; font-weight: 500;">✓ Verified</span>
                                    <?php else: ?>
                                        <span style="color: #ca8a04; font-weight: 500;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <button class="btn btn-sm" style="background: #f8f9fa; color: #333; border: 1px solid #ddd; margin-right: 5px;" onclick="openEditModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['role'])) ?>', '<?= htmlspecialchars(addslashes($u['user_role'])) ?>')">Edit</button>
                                    <button class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca;" onclick="openDeleteModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'] ?? $u['email'])) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 20px; text-align: center; color: #666;">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Invite Modal -->
<div id="inviteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Invite User</h3>
            <button class="close-btn" onclick="document.getElementById('inviteModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="/erp/users/invite">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label>Job Title (Role Display)</label>
                <input type="text" name="user_role" class="form-control" value="Staff" placeholder="e.g. HR Manager">
            </div>
            <div class="form-group">
                <label>System Access Level</label>
                <select name="role" class="form-control">
                    <option value="user">Standard User (Limited Access)</option>
                    <option value="admin">Administrator (Full Access)</option>
                </select>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn" style="background: #f1f5f9; color: #333;" onclick="document.getElementById('inviteModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn">Send Invite</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User Role</h3>
            <button class="close-btn" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="/erp/users/update">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label>Job Title (Role Display)</label>
                <input type="text" name="user_role" id="edit_user_role" class="form-control" required>
            </div>
            <div class="form-group">
                <label>System Access Level</label>
                <select name="role" id="edit_role" class="form-control">
                    <option value="user">Standard User (Limited Access)</option>
                    <option value="admin">Administrator (Full Access)</option>
                </select>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn" style="background: #f1f5f9; color: #333;" onclick="document.getElementById('editModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="color: #ef4444;">Delete User</h3>
            <button class="close-btn" onclick="document.getElementById('deleteModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="/erp/users/delete">
            <input type="hidden" name="user_id" id="delete_user_id">
            <p style="margin-bottom: 20px; line-height: 1.5;">Are you sure you want to remove system access for <strong id="delete_user_name"></strong>? This action cannot be undone.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn" style="background: #f1f5f9; color: #333;" onclick="document.getElementById('deleteModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn" style="background: #ef4444;">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, role, userRole) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_user_role').value = userRole;
    document.getElementById('editModal').classList.add('active');
}

function openDeleteModal(id, name) {
    document.getElementById('delete_user_id').value = id;
    document.getElementById('delete_user_name').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
}
</script>
</body>
</html>

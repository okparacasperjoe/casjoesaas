<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-table th { background: rgba(0,0,0,0.2); }
        .btn-action { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; margin-right: 5px; cursor: pointer; border: none; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-login { background: #f1c40f; color: black; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: rgba(0,0,0,0.2); }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <?php 
        $sidebarPath = __DIR__ . '/../../../Modules/CasjoeERP/views/layout/sidebar.php';
        if (file_exists($sidebarPath)) {
            require $sidebarPath; 
        } else {
            // Fallback Sidebar if module missing
            echo '<div class="brand"><img src="/assets/casjoe_logo.png" style="height: 40px;"></div>
                  <ul class="nav-menu">
                    <li class="nav-item"><a href="/admin" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="/admin/users" class="nav-link">Users</a></li>
                  </ul>';
        }
        ?>
    </aside>

    <main class="main-content">
        <div class="top-nav">
            <h2>User Management</h2>
            <span>Welcome, Admin</span>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tenant</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="status-badge <?= $user['role'] == 'admin' ? 'status-active' : 'status-pending' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($user['tenant_name'] ?? 'N/A') ?></td>
                        <td><?= $user['is_verified'] ? 'Verified' : 'Unverified' ?></td>
                        <td style="display: flex;">
                            <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn-action btn-edit">Edit</a>
                            
                            <form method="POST" action="/admin/users/delete/<?= $user['id'] ?>" onsubmit="return confirm('Are you sure?');">
                                <button class="btn-action btn-delete">Delete</button>
                            </form>

                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="/admin/users/impersonate/<?= $user['id'] ?>" class="btn-action btn-login">Login As</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <h2>User Roles</h2>
        <div class="card">
            <table>
                <thead><tr><th>Role Name</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?= htmlspecialchars($role['name']) ?></td>
                        <td><?= htmlspecialchars($role['description']) ?></td>
                        <td><button class="btn btn-sm">Edit</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

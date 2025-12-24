<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Users</h2>
            <button class="btn">Invite User</button>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Static Data until DB fetch is implemented -->
                    <tr>
                        <td>1</td>
                        <td>Admin User</td>
                        <td>admin@example.com</td>
                        <td>Administrator</td>
                        <td><span style="color: green;">Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Customers</h2>
            <button onclick="document.getElementById('newCustomerModal').showModal()" class="btn">Add Customer</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['company']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['phone']) ?></td>
                        <td><?= ucfirst($c['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newCustomerModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/crm/customers/store" method="POST">
            <h3>New Customer</h3>
            <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label>Company</label><input type="text" name="company" class="form-control"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
            
            <div style="margin-top: 20px; padding: 15px; background: #f7fafc; border-radius: 5px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="create_login" value="1" id="create_login_chk">
                    <span style="margin-left: 10px; font-weight: bold; font-size: 0.9rem;">Enable Client Portal Access</span>
                </label>
                
                <div id="login_fields" style="display: none; margin-top: 10px;">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                    <small style="color: #718096;">Client will use their email to login.</small>
                </div>
            </div>

            <script>
                document.getElementById('create_login_chk').addEventListener('change', function() {
                    document.getElementById('login_fields').style.display = this.checked ? 'block' : 'none';
                });
            </script>

            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newCustomerModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>

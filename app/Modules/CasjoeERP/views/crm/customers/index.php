<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
                        <th>Phone</th><th>Actions</th>
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
                    <td>
                        <button onclick='editCustomer(<?= json_encode($c) ?>)' class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">Edit</button>
                        <form action="/erp/crm/customers/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete customer?');">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-danger-outline" style="padding: 4px 8px; font-size: 0.8rem;">Del</button>
                        </form>
                    </td>
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
    <dialog id="editCustomerModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/crm/customers/update" method="POST">
            <input type="hidden" name="id" id="edit_cust_id">
            <h3 style="color:#000066; margin-top:0;">Edit Customer</h3>
            <div class="form-group"><label>Name *</label><input type="text" name="name" id="edit_cust_name" class="form-control" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_cust_email" class="form-control" required></div>
            <div class="form-group"><label>Company</label><input type="text" name="company" id="edit_cust_company" class="form-control"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" id="edit_cust_phone" class="form-control"></div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editCustomerModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update Customer</button>
            </div>
        </form>
    </dialog>
    <script>
    function editCustomer(c) {
        document.getElementById('edit_cust_id').value = c.id;
        document.getElementById('edit_cust_name').value = c.name;
        document.getElementById('edit_cust_email').value = c.email;
        document.getElementById('edit_cust_company').value = c.company;
        document.getElementById('edit_cust_phone').value = c.phone;
        document.getElementById('editCustomerModal').showModal();
    }
    </script>
</body>
</html>

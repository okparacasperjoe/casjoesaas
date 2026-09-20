<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 260px !important;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            
            /* Table responsiveness directly in here */
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Customer</h2>
            <a href="/erp/crm/customers" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="padding: 30px;">
                    <form action="/erp/crm/customers/update" method="POST">
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="<?= htmlspecialchars($customer['company'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select form-control">
                                <option value="lead" <?= ($customer['status'] ?? '') == 'lead' ? 'selected' : '' ?>>Lead</option>
                                <option value="customer" <?= ($customer['status'] ?? '') == 'customer' ? 'selected' : '' ?>>Customer</option>
                                <option value="inactive" <?= ($customer['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                            <a href="/erp/crm/customers" class="btn" style="background: #ccc; color: #333;">Cancel</a>
                            <button type="submit" class="btn">Update Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

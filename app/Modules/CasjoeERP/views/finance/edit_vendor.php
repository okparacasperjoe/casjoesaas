<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Edit Vendor | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Edit Vendor</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Update vendor profile, contact details, and address</p>
            </div>
            <a href="/erp/finance/vendors" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Vendors
            </a>
        </div>
        <div class="card" style="max-width: 650px; margin: 20px auto;">
            <form action="/erp/finance/vendors/update" method="POST">
                <input type="hidden" name="id" value="<?= $vendor['id'] ?>">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Company / Vendor Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($vendor['name']) ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Contact Person</label>
                    <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($vendor['contact_person'] ?? '') ?>">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($vendor['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($vendor['phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Office / Physical Address</label>
                    <textarea name="address" class="form-control" style="min-height: 80px;"><?= htmlspecialchars($vendor['address'] ?? '') ?></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="/erp/finance/vendors" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="checkmark-outline"></ion-icon> Update Vendor
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

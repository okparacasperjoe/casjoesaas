<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2><ion-icon name="add-circle-outline" style="vertical-align:middle; margin-right:6px;"></ion-icon> Add Business Location</h2>
        </div>

        <div class="card" style="max-width: 600px;">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'name_required'): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;margin-bottom:16px;">
                Location name is required.
            </div>
            <?php endif; ?>

            <form action="/erp/locations/store" method="POST">
                <div class="form-group">
                    <label>Location Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Main Branch, Ikeja Outlet" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full address of this location"></textarea>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. +234 800 000 0000">
                </div>

                <div class="form-actions">
                    <a href="/erp/locations" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="checkmark-outline"></ion-icon> Create Location
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

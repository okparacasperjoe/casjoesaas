<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Location | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2><ion-icon name="create-outline" style="vertical-align:middle; margin-right:6px;"></ion-icon> Edit Location</h2>
        </div>

        <div class="card" style="max-width: 600px;">
            <form action="/erp/locations/update" method="POST">
                <input type="hidden" name="id" value="<?= $location['id'] ?>">

                <div class="form-group">
                    <label>Location Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($location['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($location['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($location['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= ($location['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($location['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="/erp/locations" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="checkmark-outline"></ion-icon> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

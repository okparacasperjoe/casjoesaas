<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed; top: 0; left: -100%; height: 100%;
                z-index: 1000; transition: left 0.3s ease;
                width: 260px !important; background-color: #000066;
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
        }
        .info-banner {
            display: flex; align-items: center; gap: 10px;
            background: #eff6ff; border: 1px solid #bfdbfe;
            color: #1e40af; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <h2>Edit Inventory Item</h2>

        <?php
        $role    = \App\Core\Auth::user()['role'] ?? '';
        $isAdmin = ($role === 'admin');
        ?>

        <div class="card" style="max-width: 640px;">
            <form action="/erp/inventory/update" method="POST">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                <?php if ($isAdmin && !empty($locations)): ?>
                <div class="form-group">
                    <label>Branch / Location</label>
                    <select name="location_id" class="form-control">
                        <option value="">— Unassigned —</option>
                        <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>" <?= ($item['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif (!$isAdmin): ?>
                <div class="info-banner">
                    <ion-icon name="location-outline"></ion-icon>
                    This item belongs to your assigned branch.
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Product Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>SKU (Stock Keeping Unit) <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($item['sku']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Unit Price (₦) <span style="color:#ef4444;">*</span></label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" value="<?= htmlspecialchars($item['unit_price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= htmlspecialchars($item['stock_quantity']) ?>" min="0" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/erp/inventory" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="save-outline"></ion-icon> Update Item
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

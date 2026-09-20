<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Edit Asset</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Update asset details, valuation, status, or employee assignment</p>
            </div>
            <a href="/erp/assets" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Assets
            </a>
        </div>

        <div class="card" style="max-width: 700px; margin: 20px auto;">
            <form action="/erp/assets/update" method="POST">
                <input type="hidden" name="id" value="<?= $asset['id'] ?>">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Asset Name / Description *</label>
                    <input type="text" name="asset_name" class="form-control" required value="<?= htmlspecialchars($asset['asset_name']) ?>" placeholder="e.g. MacBook Pro M3, Dell 27-inch Monitor">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Serial / Tag Number</label>
                        <input type="text" name="serial_number" class="form-control" value="<?= htmlspecialchars($asset['serial_number'] ?? '') ?>" placeholder="S/N: XXXXXX">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Asset Value (<?= htmlspecialchars($currencySymbol) ?>)</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="<?= htmlspecialchars($asset['value'] ?? '0.00') ?>" placeholder="0.00">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="<?= htmlspecialchars($asset['purchase_date'] ?? date('Y-m-d')) ?>">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= ($asset['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active / In Use</option>
                            <option value="repair" <?= ($asset['status'] ?? '') === 'repair' ? 'selected' : '' ?>>In Repair / Service</option>
                            <option value="retired" <?= ($asset['status'] ?? '') === 'retired' ? 'selected' : '' ?>>Retired / Decommissioned</option>
                            <option value="lost" <?= ($asset['status'] ?? '') === 'lost' ? 'selected' : '' ?>>Lost / Stolen</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Assign to Employee</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($asset['assigned_to'] == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="/erp/assets" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="checkmark-outline"></ion-icon> Update Asset
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Office & Fixed Assets | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="top-bar">
    <div>
        <h2>Office & Fixed Assets</h2>
        <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Track company laptops, hardware, office furniture, tools, and equipment</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/erp/assets/create" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
            <ion-icon name="add-circle-outline"></ion-icon> Register New Asset
        </a>
    </div>
</div>

<?php if (!empty($errorMsg)): ?>
    <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
        <div><?= htmlspecialchars($errorMsg) ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($successMsg)): ?>
    <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
        <div><?= htmlspecialchars($successMsg) ?></div>
    </div>
<?php endif; ?>

<!-- Quick Metrics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
    <div class="card" style="margin: 0; padding: 18px;">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Assets</div>
        <div style="font-size: 24px; font-weight: bold; margin-top: 4px;"><?= number_format($totalAssets) ?></div>
    </div>
    <div class="card" style="margin: 0; padding: 18px;">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Asset Value</div>
        <div style="font-size: 24px; font-weight: bold; margin-top: 4px; color: #0284c7;"><?= $currencySymbol ?><?= number_format($totalValuation, 2) ?></div>
    </div>
    <div class="card" style="margin: 0; padding: 18px;">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Active / In Use</div>
        <div style="font-size: 24px; font-weight: bold; margin-top: 4px; color: #16a34a;"><?= number_format($activeCount) ?></div>
    </div>
    <div class="card" style="margin: 0; padding: 18px;">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">In Repair / Service</div>
        <div style="font-size: 24px; font-weight: bold; margin-top: 4px; color: #d97706;"><?= number_format($repairCount) ?></div>
    </div>
</div>

<div class="card">
    <table class="data-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #eee;">
                <th style="padding: 10px;">Asset Name / Model</th>
                <th style="padding: 10px;">Serial / Tag Number</th>
                <th style="padding: 10px;">Purchase Date</th>
                <th style="padding: 10px;">Valuation</th>
                <th style="padding: 10px;">Assigned Employee</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assets as $a): 
                $st = strtolower($a['status'] ?? 'active');
                $badgeBg = '#dcfce7'; $badgeColor = '#15803d';
                if ($st === 'repair') { $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; }
                elseif ($st === 'retired') { $badgeBg = '#f1f5f9'; $badgeColor = '#475569'; }
                elseif ($st === 'lost') { $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; }
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($a['asset_name']) ?></td>
                    <td style="padding: 10px; font-family: monospace; color: #64748b; font-size: 13px;"><?= htmlspecialchars($a['serial_number'] ?: 'N/A') ?></td>
                    <td style="padding: 10px; color: #666; white-space: nowrap;"><?= htmlspecialchars($a['purchase_date'] ?: '-') ?></td>
                    <td style="padding: 10px; font-family: monospace; font-weight: 600;">
                        <?= $currencySymbol ?><?= number_format($a['value'] ?? 0, 2) ?>
                    </td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;">
                        <?= htmlspecialchars($a['assigned_employee'] ?: 'Unassigned') ?>
                    </td>
                    <td style="padding: 10px;">
                        <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                            <?= ucfirst($st === 'repair' ? 'In Repair' : ($st === 'lost' ? 'Lost / Stolen' : $st)) ?>
                        </span>
                    </td>
                    <td style="padding: 10px; text-align: right; white-space: nowrap;">
                        <a href="/erp/assets/edit?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                            <ion-icon name="create-outline"></ion-icon> Edit
                        </a>
                        <form method="POST" action="/erp/assets/delete" onsubmit="return confirm('Are you sure you want to delete this asset record?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 8px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                <ion-icon name="trash-outline"></ion-icon> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($assets)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">
                        No office or fixed assets registered yet.<br>
                        <a href="/erp/assets/create" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Register your first asset</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>

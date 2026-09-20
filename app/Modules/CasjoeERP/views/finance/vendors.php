<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Vendors | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 400px; }
        html.dark-theme .modal-content { background-color: #1f2937; color: #f3f4f6; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="top-bar">
    <div>
        <h2>Vendors & Suppliers</h2>
        <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Manage external vendors, contractors, and service providers</p>
    </div>
    <button class="btn btn-primary" onclick="openModal()" style="display: inline-flex; align-items: center; gap: 6px;">
        <ion-icon name="add-outline"></ion-icon> Add Vendor
    </button>
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

<div class="card">
    <table class="data-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #eee;">
                <th style="padding: 10px;">Company / Vendor Name</th>
                <th style="padding: 10px;">Email</th>
                <th style="padding: 10px;">Phone</th>
                <th style="padding: 10px;">Address</th>
                <th style="padding: 10px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendors as $v): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($v['name']) ?></td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($v['email'] ?? '-') ?></td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($v['phone'] ?? '-') ?></td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($v['address'] ?? '-') ?></td>
                    <td style="padding: 10px; text-align: right; white-space: nowrap;">
                        <a href="/erp/finance/vendors/edit?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                            <ion-icon name="create-outline"></ion-icon> Edit
                        </a>
                        <form method="POST" action="/erp/finance/vendors/delete" onsubmit="return confirm('Are you sure you want to delete this vendor?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 8px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                <ion-icon name="trash-outline"></ion-icon> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($vendors)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color: #94a3b8;">
                        No vendors registered yet.<br>
                        <a href="javascript:void(0)" onclick="openModal()" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Add your first vendor</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Vendor Modal -->
<div id="vendorModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 style="margin-top: 0;">Add Vendor</h2>
        <form action="/erp/finance/vendors/store" method="POST">
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;">Vendor Name</label>
                <input type="text" name="name" class="form-control" required style="width: 100%; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;">Email</label>
                <input type="email" name="email" class="form-control" style="width: 100%; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;">Phone</label>
                <input type="text" name="phone" class="form-control" style="width: 100%; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 4px; font-weight: 500;">Address</label>
                <textarea name="address" class="form-control" style="width: 100%; box-sizing: border-box;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Vendor</button>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('vendorModal').style.display = 'block'; }
function closeModal() { document.getElementById('vendorModal').style.display = 'none'; }
window.onclick = function(event) {
    if (event.target == document.getElementById('vendorModal')) {
        closeModal();
    }
}
</script>

    </main>
</div>
</body>
</html>

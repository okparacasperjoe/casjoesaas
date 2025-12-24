<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendors | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 400px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Vendors</h1>
    <button class="btn btn-primary" onclick="openModal()">
        <ion-icon name="add-outline"></ion-icon> Add Vendor
    </button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendors as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['name']) ?></td>
                    <td><?= htmlspecialchars($v['email']) ?></td>
                    <td><?= htmlspecialchars($v['phone']) ?></td>
                    <td><?= htmlspecialchars($v['address']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($vendors)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 20px;">No vendors found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Vendor Modal -->
<div id="vendorModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add Vendor</h2>
        <form action="/erp/finance/vendors/store" method="POST">
            <div class="form-group">
                <label>Vendor Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Save Vendor</button>
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

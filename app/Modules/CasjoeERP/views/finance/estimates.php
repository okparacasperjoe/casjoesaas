<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Estimates | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Estimates</h1>
    <a href="/erp/finance/estimates/create" class="btn btn-primary">
        <ion-icon name="add-outline"></ion-icon> Create Estimate
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Estimate #</th>
                <th>Client</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estimates as $est): ?>
                <tr>
                    <td>#<?= str_pad($est['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($est['client_name']) ?></td>
                    <td><?= $est['issue_date'] ?></td>
                    <td><?= $est['expiry_date'] ?></td>
                    <td>NGN <?= number_format($est['total_amount'], 2) ?></td>
                    <td>
                        <span class="badge badge-<?= $est['status'] == 'accepted' ? 'success' : ($est['status'] == 'rejected' ? 'danger' : ($est['status'] == 'converted' ? 'info' : 'warning')) ?>">
                            <?= ucfirst($est['status']) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Public Link -->
                         <a href="/estimate/<?= $est['uuid'] ?>" target="_blank" class="btn btn-sm btn-outline">
                            <ion-icon name="link-outline"></ion-icon> Client View
                         </a>
                         
                         <!-- Convert -->
                         <?php if ($est['status'] == 'accepted'): ?>
                         <form action="/estimate/<?= $est['uuid'] ?>/convert" method="POST" style="display:inline;">
                             <button type="submit" class="btn btn-sm btn-success">Convert to Invoice</button>
                         </form>
                         <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($estimates)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">No estimates found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>

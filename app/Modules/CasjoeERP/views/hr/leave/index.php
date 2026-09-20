<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Leave Management | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Leave Management</h2>
            <a href="/erp/leave/create" class="btn"><ion-icon name="add-outline"></ion-icon> Request Leave</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></td>
                        <td><?= ucfirst($req['leave_type']) ?></td>
                        <td><?= $req['start_date'] ?></td>
                        <td><?= $req['end_date'] ?></td>
                        <td>
                            <?php
                            $color = 'orange';
                            if ($req['status'] === 'approved') $color = 'green';
                            if ($req['status'] === 'rejected') $color = 'red';
                            ?>
                            <span style="color: <?= $color ?>; font-weight: bold;"><?= ucfirst($req['status']) ?></span>
                        </td>
                        <td>
                            <?php if ($req['status'] === 'pending'): ?>
                                <div style="display: flex; gap: 5px;">
                                    <form method="POST" action="/erp/leave/approve">
                                        <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn btn-sm" style="background: green;">Approve</button>
                                    </form>
                                    <form method="POST" action="/erp/leave/reject">
                                        <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn btn-sm" style="background: red;">Reject</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Lifecycle | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Lifecycle Events</h2>
            <a href="/erp/lifecycle/create" class="btn">Record Event</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>New Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= $event['date'] ?></td>
                        <td><?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?></td>
                        <td><?= ucfirst($event['type']) ?></td>
                        <td><?= htmlspecialchars($event['reason']) ?></td>
                        <td><?= htmlspecialchars($event['new_position'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

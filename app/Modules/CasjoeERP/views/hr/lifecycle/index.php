<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title><?= !empty($type) ? ucfirst($type) . ' Events' : 'Employee Lifecycle' ?> | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 24px; }
        .filter-tab { padding: 8px 18px; border-radius: 8px; background: #ffffff; color: #475569; text-decoration: none; font-weight: 600; border: 1px solid #cbd5e1; transition: all 0.2s; font-size: 0.9rem; }
        .filter-tab:hover, .filter-tab.active { background: #000066; color: #ffffff; border-color: #000066; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar" style="justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2><?= !empty($type) ? ucfirst($type) . ' Events' : 'Lifecycle Events' ?></h2>
            <a href="/erp/lifecycle/create<?= !empty($type) ? '?type=' . $type : '' ?>" class="btn">Record <?= !empty($type) ? ucfirst($type) : 'Event' ?></a>
        </div>

        <div class="filter-tabs">
            <a href="/erp/lifecycle" class="filter-tab <?= empty($type) ? 'active' : '' ?>">All Events</a>
            <a href="/erp/lifecycle/promotion" class="filter-tab <?= ($type ?? '') === 'promotion' ? 'active' : '' ?>">Promotions</a>
            <a href="/erp/lifecycle/resignation" class="filter-tab <?= ($type ?? '') === 'resignation' ? 'active' : '' ?>">Resignations</a>
            <a href="/erp/lifecycle/termination" class="filter-tab <?= ($type ?? '') === 'termination' ? 'active' : '' ?>">Terminations</a>
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
                        <th>Action</th>
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
                        <td>
                            <form action="/erp/lifecycle/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete lifecycle event?');">
                                <input type="hidden" name="id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-danger-outline" style="padding: 2px 6px; font-size: 0.75rem; color: #ef4444; border: 1px solid #ef4444; border-radius: 4px; background: transparent;">Delete</button>
                            </form>
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

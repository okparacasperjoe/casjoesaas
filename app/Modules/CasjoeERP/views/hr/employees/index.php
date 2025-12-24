<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employees | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; text-transform: uppercase; font-weight: bold; }
        .status-active { background: #dcfce7; color: #15803d; }
        .status-resigned { background: #fef9c3; color: #854d0e; }
        .status-terminated { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Employees</h2>
            <a href="/erp/employees/create" class="btn"><ion-icon name="person-add-outline"></ion-icon> New Employee</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Employee</th>
                        <th style="padding: 10px;">Role</th>
                        <th style="padding: 10px;">Department</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;">
                                <div style="font-weight: bold;"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                <div style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($emp['email']) ?></div>
                            </td>
                            <td style="padding: 10px;"><?= htmlspecialchars($emp['job_title'] ?? '-') ?></td>
                            <td style="padding: 10px;">
                                <?php if ($emp['department_name']): ?>
                                    <span style="background: #f3f4f6; color: #374151; padding: 2px 8px; border-radius: 5px; font-size: 0.8rem;">
                                        <?= htmlspecialchars($emp['department_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px;">
                                <span class="status-badge status-<?= $emp['status'] ?>">
                                    <?= ucfirst($emp['status']) ?>
                                </span>
                            </td>
                            <td style="padding: 10px;">
                                <div style="display: flex; gap: 5px;">
                                    <button class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #eee; color: #333;">View</button>
                                    <form method="POST" action="/erp/employees/delete" onsubmit="return confirm('Are you sure you want to delete this employee? This will also remove their login access.');">
                                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">Delete</button>
                                    </form>
                                </div>
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

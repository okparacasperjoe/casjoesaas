<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Departments</h2>
            <a href="/erp/departments/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Department</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Name</th>
                        <th style="padding: 10px;">Description</th>
                        <th style="padding: 10px;">Employees</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($dept['name']) ?></td>
                            <td style="padding: 10px; color: #666;"><?= htmlspecialchars($dept['description']) ?></td>
                            <td style="padding: 10px;">
                                <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                                    <?= $dept['employee_count'] ?> Employees
                                </span>
                            </td>
                            <td style="padding: 10px;">
                                <button class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #eee; color: #333;">Edit</button>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/erp" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> ERP Dashboard</a></li>
            <li class="nav-item"><a href="/erp/hr" class="nav-link active"><ion-icon name="people-outline"></ion-icon> Employees</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Human Resources</h2>
            <button class="btn" onclick="alert('Demo: Add Employee Modal')">+ Add Employee</button>
        </div>

        <div class="card">
            <h3>Employee Directory</h3>
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Name</th>
                        <th style="padding: 10px;">Position</th>
                        <th style="padding: 10px;">Department</th>
                        <th style="padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="4" style="padding: 20px; text-align: center;">No employees found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($emp['position']) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($emp['department']) ?></td>
                                <td style="padding: 10px;"><span style="background: #e6fffa; color: #047857; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;"><?= ucfirst($emp['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

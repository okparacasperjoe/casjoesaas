<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Finance Dashboard | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Financial Overview</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
            <div class="card" style="text-align: center;">
                <h3 style="color: #666; margin-bottom: 5px;">Total Income</h3>
                <p style="font-size: 2rem; color: #15803d; font-weight: bold;">$<?= number_format($income, 2) ?></p>
            </div>
            <div class="card" style="text-align: center;">
                <h3 style="color: #666; margin-bottom: 5px;">Total Expenses</h3>
                <p style="font-size: 2rem; color: #991b1b; font-weight: bold;">$<?= number_format($expense, 2) ?></p>
            </div>
            <div class="card" style="text-align: center; border-top: 4px solid var(--primary);">
                <h3 style="color: #666; margin-bottom: 5px;">Net Profit</h3>
                <p style="font-size: 2rem; color: #fff; font-weight: bold;">$<?= number_format($profit, 2) ?></p>
            </div>
        </div>
        
        <div class="card">
            <p style="color: #888; text-align: center; padding: 20px;">[Placeholder for Financial Charts]</p>
        </div>
    </main>
</div>
</body>
</html>

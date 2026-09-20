<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Reports | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <h2>System Reports</h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <div class="card">
                <h3>Employee Demographics</h3>
                <p>Breakdown by department, age, and tenure.</p>
                <button class="btn btn-sm">Generate</button>
            </div>
            <div class="card">
                <h3>Financial Summary</h3>
                <p>Revenue, expenses, and net income.</p>
                <button class="btn btn-sm">Generate</button>
            </div>
            <div class="card">
                <h3>Sales Performance</h3>
                <p>Top products and customer acquisition.</p>
                <button class="btn btn-sm">Generate</button>
            </div>
        </div>
    </main>
</div>
</body>
</html>

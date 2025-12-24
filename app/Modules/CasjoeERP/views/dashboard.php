<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ERP Dashboard | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .module-card {
            background: #ffffff;
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            display: block;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .module-card:hover {
            transform: translateY(-5px);
            background: #ffffff;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .module-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        .module-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .module-stat {
            font-size: 0.9rem;
            color: var(--secondary);
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Enterprise Resource Planning</h2>
        </div>

        <div class="module-grid">
            <a href="/erp/hr" class="module-card">
                <ion-icon name="people-outline" class="module-icon"></ion-icon>
                <div class="module-title">Human Resources</div>
                <div class="module-stat"><?= $stats['employees'] ?> Active Employees</div>
            </a>
            <a href="/erp/finance" class="module-card">
                <ion-icon name="cash-outline" class="module-icon"></ion-icon>
                <div class="module-title">Finance & Accounting</div>
                <div class="module-stat">GL & Journal Entries</div>
            </a>
            <a href="/erp/crm" class="module-card">
                <ion-icon name="people-circle-outline" class="module-icon"></ion-icon>
                <div class="module-title">CRM</div>
                <div class="module-stat"><?= $stats['customers'] ?> Customers</div>
            </a>
            <a href="/erp/inventory" class="module-card">
                <ion-icon name="cube-outline" class="module-icon"></ion-icon>
                <div class="module-title">Inventory</div>
                <div class="module-stat">Stock Management</div>
            </a>
        </div>

    </main>
</div>
</body>
</html>
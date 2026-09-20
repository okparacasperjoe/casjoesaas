<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>CRM Dashboard | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-content h3 { margin: 0; font-size: 1.8rem; color: #333; }
        .stat-content p { margin: 0; color: #666; font-size: 0.9rem; }
        
        .recent-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .recent-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .recent-item:last-child { border-bottom: none; }
        .avatar-initial {
            width: 40px;
            height: 40px;
            background: #eee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #555;
            margin-right: 15px;
        }
    </style>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 260px !important;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            
            /* Table responsiveness directly in here */
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../layout/sidebar_erp_css.php'; ?>

        <div class="erp-brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe Apps" style="height: 40px;"></a></div>
        <ul class="erp-menu">
            <li class="erp-item"><a href="/erp" class="erp-link"><ion-icon name="arrow-back-outline"></ion-icon> ERP Home</a></li>
            <li class="erp-item"><a href="/erp/crm" class="erp-link active"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="erp-item"><a href="/erp/crm/customers" class="erp-link"><ion-icon name="people-outline"></ion-icon> Customers</a></li>
            <li class="erp-item"><a href="/erp/crm/leads" class="erp-link"><ion-icon name="magnet-outline"></ion-icon> Leads</a></li>
            <li class="erp-item"><a href="/erp/crm/pipeline" class="erp-link"><ion-icon name="funnel-outline"></ion-icon> Pipeline</a></li>
             <li class="erp-item"><a href="/erp/crm/sales" class="erp-link"><ion-icon name="cash-outline"></ion-icon> Sales</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>CRM Overview</h2>
            <div>
                <a href="/erp/crm/leads" class="btn btn-outline">+ Add Lead</a>
             </div>
        </div>

        <!-- KPI Stats -->
        <?php
        use App\Core\Services\CurrencyService;
        $currencyService = new CurrencyService();
        $userCurrency = $_SESSION['currency'] ?? 'USD';
        $currencySymbol = $currencyService->getSymbol($userCurrency);
        ?>
        <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd; color: #1565c0;">
                    <ion-icon name="people"></ion-icon>
                </div>
                <div class="stat-content">
                    <h3><?= $totalCustomers ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9; color: #2e7d32;">
                    <ion-icon name="magnet"></ion-icon>
                </div>
                <div class="stat-content">
                    <h3><?= $activeLeads ?></h3>
                    <p>Active Leads</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0; color: #ef6c00;">
                    <ion-icon name="cash"></ion-icon>
                </div>
                <div class="stat-content">
                    <h3><?= $currencySymbol ?><?= number_format($pipelineValue) ?></h3>
                    <p>Pipeline Value</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f3e5f5; color: #7b1fa2;">
                    <ion-icon name="trophy"></ion-icon>
                </div>
                <div class="stat-content">
                    <h3><?= $wonDeals ?></h3>
                    <p>Deals Won</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <!-- Main Area -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Recent Leads</h3>
                    <a href="/erp/crm/leads" style="text-decoration: none; color: var(--primary);">View All</a>
                </div>
                
                <?php if(empty($recentLeads)): ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        No leads yet. <a href="/erp/crm/leads">Add one?</a>
                    </div>
                <?php else: ?>
                    <ul class="recent-list">
                        <?php foreach($recentLeads as $lead): ?>
                            <li class="recent-item">
                                <div class="avatar-initial"><?= strtoupper(substr($lead['name'], 0, 1)) ?></div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 1rem;"><?= htmlspecialchars($lead['name']) ?></h4>
                                    <span style="font-size: 0.85rem; color: #666;"><?= htmlspecialchars($lead['source']) ?></span>
                                </div>
                                <span class="badge" style="background: #edf2f7; color: #4a5568; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    <?= htmlspecialchars($lead['status']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Side Area -->
            <div class="card" style="background: linear-gradient(135deg, #000066 0%, #000044 100%); color: white;">
                <h3>Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="/erp/crm/customers" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; text-align: left;">
                        <ion-icon name="people-outline" style="margin-right: 8px;"></ion-icon> View Customers
                    </a>
                    <a href="/erp/crm/pipeline" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; text-align: left;">
                        <ion-icon name="funnel-outline" style="margin-right: 8px;"></ion-icon> Manage Pipeline
                    </a>
                    <a href="/erp/crm/opportunities" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; text-align: left;">
                        <ion-icon name="add-circle-outline" style="margin-right: 8px;"></ion-icon> New Opportunity
                    </a>
                </div>
            </div>
        </div>

    </main>
</div>
</body>
</html>


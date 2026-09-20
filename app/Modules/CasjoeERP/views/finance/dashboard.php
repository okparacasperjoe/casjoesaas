<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Financial Overview | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .fin-stat-card {
            background: var(--surface, #ffffff);
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        html.dark-theme .fin-stat-card {
            background: #111827;
            border-color: rgba(255,255,255,0.08);
        }
        .fin-stat-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .fin-stat-value {
            font-size: 1.85rem;
            font-weight: 700;
            font-family: monospace;
            margin: 0;
        }
        .action-chip {
            background: var(--surface, #ffffff);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: inherit;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        html.dark-theme .action-chip {
            background: #1e293b;
            border-color: rgba(255,255,255,0.1);
        }
        .action-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
            border-color: #3b82f6;
            color: #2563eb;
        }
        .action-chip ion-icon {
            font-size: 22px;
            color: #3b82f6;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Financial Overview</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">
                    Currency: <strong style="color: #0284c7;"><?= htmlspecialchars($cCode ?? 'NGN') ?> (<?= htmlspecialchars($currencySymbol ?? '₦') ?>)</strong> &bull; Inherited from your account settings
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/erp/transactions/import" class="btn" style="background: #0284c7; color: #fff; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <ion-icon name="cloud-upload-outline"></ion-icon> Import Excel / CSV
                </a>
                <a href="/erp/transactions/create" class="btn" style="display: inline-flex; align-items: center; gap: 6px;">
                    <ion-icon name="swap-horizontal-outline"></ion-icon> New Transaction
                </a>
            </div>
        </div>

        <!-- 4 Main P&L & Balance Stat Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 24px;">
            <!-- Total Income -->
            <div class="fin-stat-card" style="border-top: 4px solid #16a34a;">
                <div class="fin-stat-title">
                    <span>Total Income</span>
                    <ion-icon name="trending-up-outline" style="font-size: 20px; color: #16a34a;"></ion-icon>
                </div>
                <div class="fin-stat-value" style="color: #16a34a;">
                    <?= htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format($income ?? 0, 2) ?>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 8px;">All time recorded inflow & revenue</div>
            </div>

            <!-- Total Expenses -->
            <div class="fin-stat-card" style="border-top: 4px solid #dc2626;">
                <div class="fin-stat-title">
                    <span>Total Expenses</span>
                    <ion-icon name="trending-down-outline" style="font-size: 20px; color: #dc2626;"></ion-icon>
                </div>
                <div class="fin-stat-value" style="color: #dc2626;">
                    <?= htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format($expense ?? 0, 2) ?>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 8px;">All recorded costs & vendor expenses</div>
            </div>

            <!-- Net Profit -->
            <div class="fin-stat-card" style="border-top: 4px solid <?= ($profit ?? 0) >= 0 ? '#2563eb' : '#dc2626' ?>;">
                <div class="fin-stat-title">
                    <span>Net Profit</span>
                    <ion-icon name="calculator-outline" style="font-size: 20px; color: <?= ($profit ?? 0) >= 0 ? '#2563eb' : '#dc2626' ?>;"></ion-icon>
                </div>
                <div class="fin-stat-value" style="color: <?= ($profit ?? 0) >= 0 ? '#2563eb' : '#dc2626' ?>;">
                    <?= (($profit ?? 0) < 0 ? '-' : '') . htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format(abs($profit ?? 0), 2) ?>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Total Inflow minus Total Outflow</div>
            </div>

            <!-- Cash Balance -->
            <div class="fin-stat-card" style="border-top: 4px solid #0891b2;">
                <div class="fin-stat-title">
                    <span>Cash Balance</span>
                    <ion-icon name="wallet-outline" style="font-size: 20px; color: #0891b2;"></ion-icon>
                </div>
                <div class="fin-stat-value" style="color: #0891b2;">
                    <?= (($cashBalance ?? 0) < 0 ? '-' : '') . htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format(abs($cashBalance ?? 0), 2) ?>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Liquid cash & ledger accounts</div>
            </div>
        </div>

        <!-- Quick Financial Actions -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;">
            <a href="/erp/transactions" class="action-chip">
                <ion-icon name="list-outline"></ion-icon>
                <div>
                    <div>All Transactions</div>
                    <div style="font-size: 11px; font-weight: normal; color: #64748b;">View Cashflow Ledger</div>
                </div>
            </a>
            <a href="/erp/finance/expenses" class="action-chip">
                <ion-icon name="receipt-outline"></ion-icon>
                <div>
                    <div>Expenses</div>
                    <div style="font-size: 11px; font-weight: normal; color: #64748b;">Manage Vendor Costs</div>
                </div>
            </a>
            <a href="/erp/finance/invoices" class="action-chip">
                <ion-icon name="document-text-outline"></ion-icon>
                <div>
                    <div>Invoices</div>
                    <div style="font-size: 11px; font-weight: normal; color: #64748b;">Issue & Track Invoices</div>
                </div>
            </a>
            <a href="/erp/finance" class="action-chip">
                <ion-icon name="git-network-outline"></ion-icon>
                <div>
                    <div>Chart of Accounts</div>
                    <div style="font-size: 11px; font-weight: normal; color: #64748b;">Double-Entry Ledger</div>
                </div>
            </a>
        </div>

        <!-- Recent Financial Activity Table -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                <h3 style="margin: 0; font-size: 16px;">Recent Financial Transactions</h3>
                <a href="/erp/transactions" style="color: #0284c7; font-size: 13px; text-decoration: none; font-weight: 600;">View All &rarr;</a>
            </div>

            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Description</th>
                        <th style="padding: 10px;">Category</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentTransactions)): ?>
                        <?php foreach ($recentTransactions as $txn): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; color: #666; white-space: nowrap;"><?= htmlspecialchars($txn['date']) ?></td>
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($txn['description']) ?></td>
                                <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($txn['category'] ?? 'General') ?></td>
                                <td style="padding: 10px;">
                                    <span style="background: <?= $txn['type'] == 'income' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $txn['type'] == 'income' ? '#15803d' : '#991b1b' ?>; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        <?= ucfirst($txn['type']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; font-family: monospace; font-weight: 600; text-align: right; color: <?= $txn['type'] == 'income' ? '#15803d' : '#991b1b' ?>;">
                                    <?= ($txn['type'] == 'income' ? '+' : '-') ?><?= htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format($txn['amount'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                                No transactions recorded yet. <br>
                                <a href="/erp/transactions/import" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Import an Excel/CSV spreadsheet</a> or <a href="/erp/transactions/create" style="color: #0284c7; text-decoration: underline;">create one manually</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

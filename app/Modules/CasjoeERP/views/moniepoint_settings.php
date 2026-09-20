<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Moniepoint POS Integration | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--text-color); }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-color);
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .settings-card {
            background: var(--card-bg);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .btn-primary { 
            background: #0284c7; 
            color: #fff; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 1rem; 
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #0369a1; }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .hint-text {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }
        .webhook-box {
            background: rgba(2, 132, 199, 0.05);
            border: 1px dashed #0284c7;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 25px;
        }
        .status-badge.income {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .status-badge.expense {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="/erp/settings" class="btn" style="padding: 8px 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                        <ion-icon name="arrow-back-outline"></ion-icon> Settings
                    </a>
                    <h2 style="margin: 0;">Moniepoint POS Integration</h2>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="/erp/finance/dashboard" class="btn" style="text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="stats-chart-outline"></ion-icon> View ERP Stats
                    </a>
                    <a href="/erp/transactions" class="btn" style="text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="list-outline"></ion-icon> Transactions Ledger
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['flash_message']) || isset($_GET['saved'])): ?>
                <div style="padding: 14px 18px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 25px; border: 1px solid #c3e6cb; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 1.3rem;"></ion-icon>
                    <?= htmlspecialchars($_SESSION['flash_message'] ?? 'Moniepoint configuration saved successfully!') ?>
                    <?php unset($_SESSION['flash_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Real-Time Webhook Listener Box -->
            <div class="webhook-box">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 280px;">
                        <span style="background: #0284c7; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Auto-Sync Webhook</span>
                        <h3 style="margin: 8px 0 6px 0; font-size: 1.15rem; color: var(--text-color);">Your Moniepoint Webhook Notification URL</h3>
                        <p style="margin: 0; color: #64748b; font-size: 0.88rem; line-height: 1.5;">
                            Copy this URL and paste it into your <strong>Moniepoint Business Dashboard</strong> under <strong>Developer &gt; Webhook Settings</strong>. Whenever any <strong>deposit (inflow)</strong> or <strong>debit (outflow)</strong> occurs on your POS terminal, Moniepoint pushes it directly here to notify you and update your ERP stats in real time.
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 15px; max-width: 750px;">
                    <input type="text" id="webhookUrlInput" readonly value="https://app.casjoe.com/api/erp/moniepoint/webhook/<?= htmlspecialchars($tenantId) ?>" class="form-control" style="background: #fff; font-family: monospace; font-weight: 600; cursor: copy;" onclick="copyWebhookUrl()">
                    <button type="button" class="btn btn-primary" onclick="copyWebhookUrl()" style="white-space: nowrap; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="copy-outline"></ion-icon> Copy URL
                    </button>
                </div>
                <small id="copyAlert" style="display: none; color: #10b981; font-weight: 700; margin-top: 6px;">✓ Webhook URL copied to clipboard!</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                <!-- Moniepoint Credentials Form -->
                <div class="settings-card">
                    <h3 style="margin: 0 0 15px 0; font-size: 1.15rem;">Terminal Credentials</h3>
                    <form action="/erp/settings/moniepoint" method="POST">
                        <div class="form-group">
                            <label>Moniepoint Client ID</label>
                            <input type="text" name="client_id" class="form-control" value="<?= htmlspecialchars($integration['client_id'] ?? '') ?>" placeholder="e.g. CLI_1234567890" required>
                            <small class="hint-text">Found in Moniepoint POS Terminal Configuration &gt; Developer / API Settings.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Moniepoint Client Secret</label>
                            <input type="password" name="client_secret" class="form-control" value="<?= htmlspecialchars($integration['client_secret'] ?? '') ?>" placeholder="Enter client secret" required>
                            <small class="hint-text">Your secret OAuth key from Moniepoint.</small>
                        </div>

                        <div class="form-group">
                            <label>POS Terminal Serial Number</label>
                            <input type="text" name="terminal_serial" class="form-control" value="<?= htmlspecialchars($integration['terminal_serial'] ?? '') ?>" placeholder="e.g. MP12345678" required>
                            <small class="hint-text">Serial number printed on your physical Moniepoint terminal.</small>
                        </div>

                        <div class="form-group">
                            <label>Webhook Secret Key (Optional)</label>
                            <input type="text" name="webhook_secret" class="form-control" value="<?= htmlspecialchars($integration['webhook_secret'] ?? '') ?>" placeholder="Optional webhook signature key">
                            <small class="hint-text">If configured in Moniepoint, used to verify webhook integrity.</small>
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap: 10px; padding: 14px; background: rgba(2, 132, 199, 0.05); border-radius: 8px; border: 1px solid rgba(2, 132, 199, 0.15);">
                            <input type="checkbox" name="is_active" id="is_active" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= (!empty($integration['is_active'])) ? 'checked' : '' ?>>
                            <label for="is_active" style="margin:0; cursor: pointer; font-size: 0.95rem;">Enable Moniepoint POS Integration</label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Save Settings</button>
                    </form>
                </div>

                <!-- Simulation & How It Works -->
                <div>
                    <!-- Simulation Card -->
                    <div class="settings-card" style="border-left: 4px solid #10b981;">
                        <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                            <ion-icon name="flash-outline" style="color: #10b981;"></ion-icon> Real-Time Notification & Stat Test
                        </h3>
                        <p style="color: #64748b; font-size: 0.88rem; line-height: 1.5; margin-bottom: 20px;">
                            Click below to simulate an incoming Moniepoint POS event. You will instantly receive an in-app notification on your bell icon, and the transaction will immediately reflect in your <strong>ERP Stats</strong>!
                        </p>

                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <form action="/erp/moniepoint/simulate-test" method="POST" style="flex: 1;">
                                <input type="hidden" name="test_type" value="deposit">
                                <input type="hidden" name="test_amount" value="5000">
                                <button type="submit" class="btn" style="width: 100%; background: #10b981; color: #fff; font-weight: 700; padding: 12px;">
                                    <ion-icon name="arrow-down-circle-outline"></ion-icon> Test Deposit (+₦5,000)
                                </button>
                            </form>

                            <form action="/erp/moniepoint/simulate-test" method="POST" style="flex: 1;">
                                <input type="hidden" name="test_type" value="debit">
                                <input type="hidden" name="test_amount" value="2000">
                                <button type="submit" class="btn" style="width: 100%; background: #ef4444; color: #fff; font-weight: 700; padding: 12px;">
                                    <ion-icon name="arrow-up-circle-outline"></ion-icon> Test Debit (-₦2,000)
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Integration Info -->
                    <div class="settings-card">
                        <h4 style="margin: 0 0 10px 0; font-size: 1rem;">How Inflows & Outflows Update Your ERP:</h4>
                        <ul style="padding-left: 18px; margin: 0; color: #64748b; font-size: 0.88rem; line-height: 1.6;">
                            <li><strong>Deposits (Inflows)</strong>: Card purchases and bank transfers received on the POS are logged as <code>income</code>. They increase your Total Revenue, Gross Profit, and Cash Inflow in the Financial Overview stats.</li>
                            <li><strong>Debits (Outflows)</strong>: Withdrawals, refunds, or disbursements are recorded as <code>expense</code>, updating your Total Expenses and Net Profit in real time.</li>
                            <li><strong>Instant Alerts</strong>: All tenant administrators receive an immediate in-app notification bell update on every transaction.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Moniepoint POS Transactions -->
            <div class="table-container" style="margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; font-size: 1.2rem;">Recent Moniepoint POS Activity</h3>
                    <a href="/erp/transactions" class="btn btn-outline" style="text-decoration: none; font-size: 0.85rem;">View All in Ledger &rarr;</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentMoniepointTx)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 25px; color: #64748b;">
                                    No Moniepoint transactions recorded yet. Use the simulation buttons above or connect your live webhook to see activity here!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentMoniepointTx as $tx): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tx['date']) ?></td>
                                    <td><?= htmlspecialchars($tx['description']) ?></td>
                                    <td><code><?= htmlspecialchars($tx['reference'] ?? 'N/A') ?></code></td>
                                    <td>
                                        <span class="status-badge <?= $tx['type'] === 'income' ? 'income' : 'expense' ?>">
                                            <?= $tx['type'] === 'income' ? '↓ Deposit (Inflow)' : '↑ Debit (Outflow)' ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: <?= $tx['type'] === 'income' ? '#10b981' : '#ef4444' ?>;">
                                        <?= $tx['type'] === 'income' ? '+' : '-' ?>₦<?= number_format((float)$tx['amount'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script>
    function copyWebhookUrl() {
        const input = document.getElementById('webhookUrlInput');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        const alertEl = document.getElementById('copyAlert');
        alertEl.style.display = 'block';
        setTimeout(() => { alertEl.style.display = 'none'; }, 3500);
    }
    </script>
</body>
</html>

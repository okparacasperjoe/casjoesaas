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
            margin-bottom: 25px;
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
            padding: 9px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-danger {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
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
        .terminal-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(2, 132, 199, 0.1);
            color: #0284c7;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 0.82rem;
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
                            Paste this single webhook URL into your <strong>Moniepoint Dashboard</strong> under <strong>Developer &gt; Webhook Settings</strong>. Whether your business has <strong>1, 2, or 10 POS terminals</strong>, Moniepoint will automatically route every deposit (inflow) and debit (outflow) from all your terminals to your ERP in real time!
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

            <!-- Multi-Terminal Management Section -->
            <div class="settings-card" style="border-top: 4px solid #0284c7;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; display: flex; align-items: center; gap: 8px;">
                            <ion-icon name="hardware-chip-outline" style="color: #0284c7; font-size: 1.3rem;"></ion-icon> Registered POS Terminals (Multi-Terminal)
                        </h3>
                        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.88rem;">
                            You can link multiple POS devices (e.g. Counter 1, Counter 2, Bar POS, Branch 2). Each terminal's transactions will be labeled individually in your notifications and financial stats!
                        </p>
                    </div>
                    <button type="button" onclick="document.getElementById('addTerminalModal').style.display='block'" class="btn btn-primary" style="padding: 9px 16px; font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="add-circle-outline"></ion-icon> Link Another POS Terminal
                    </button>
                </div>

                <div class="table-container" style="box-shadow: none; border: 1px solid var(--border-color); border-radius: 8px; margin-top: 15px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Terminal Name / Label</th>
                                <th>Serial Number</th>
                                <th>Assigned Location</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($terminals)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">
                                        No terminals registered yet. Click "Link Another POS Terminal" above to add your first device!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($terminals as $term): ?>
                                    <tr>
                                        <td>
                                            <strong style="color: var(--text-color);"><?= htmlspecialchars($term['terminal_name']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="terminal-pill">
                                                <ion-icon name="calculator-outline"></ion-icon> <?= htmlspecialchars($term['terminal_serial']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($term['location_name'] ?? 'Main Store') ?>
                                        </td>
                                        <td>
                                            <span style="color: #10b981; font-weight: 700; font-size: 0.85rem;">● Active</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <form action="/erp/settings/moniepoint/terminals/delete" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this terminal?');">
                                                <input type="hidden" name="terminal_id" value="<?= $term['id'] ?>">
                                                <button type="submit" class="btn-danger" title="Remove Terminal">
                                                    <ion-icon name="trash-outline" style="vertical-align: text-bottom;"></ion-icon> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Terminal Modal -->
            <div id="addTerminalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
                <div style="background: var(--card-bg); max-width: 480px; width: 90%; margin: 80px auto; padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0; font-size: 1.15rem;">Link New POS Terminal</h3>
                        <button type="button" onclick="document.getElementById('addTerminalModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-color);">&times;</button>
                    </div>
                    <form action="/erp/settings/moniepoint/terminals/add" method="POST">
                        <div class="form-group">
                            <label>Terminal Label / Name</label>
                            <input type="text" name="terminal_name" class="form-control" placeholder="e.g. Counter 2 / Bar / Mobile POS" required>
                            <small class="hint-text">A friendly name so you know which cashier or station took the payment.</small>
                        </div>
                        <div class="form-group">
                            <label>Terminal Serial Number</label>
                            <input type="text" name="terminal_serial" class="form-control" placeholder="e.g. MP12345678" required>
                            <small class="hint-text">Found on the back of the physical Moniepoint POS device.</small>
                        </div>
                        <?php if (!empty($locations)): ?>
                            <div class="form-group">
                                <label>Assign to Branch / Location</label>
                                <select name="location_id" class="form-control">
                                    <option value="">-- Main Store / Unassigned --</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                            <button type="button" onclick="document.getElementById('addTerminalModal').style.display='none'" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Terminal</button>
                        </div>
                    </form>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                <!-- Moniepoint API Account Credentials Form -->
                <div class="settings-card">
                    <h3 style="margin: 0 0 15px 0; font-size: 1.15rem;">Moniepoint Developer API Credentials</h3>
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
                            <label>Primary Terminal Serial Number</label>
                            <input type="text" name="terminal_serial" class="form-control" value="<?= htmlspecialchars($integration['terminal_serial'] ?? '') ?>" placeholder="e.g. MP12345678">
                            <small class="hint-text">Default terminal for single-terminal push payments.</small>
                        </div>

                        <div class="form-group">
                            <label>Webhook Secret Key (Optional)</label>
                            <input type="text" name="webhook_secret" class="form-control" value="<?= htmlspecialchars($integration['webhook_secret'] ?? '') ?>" placeholder="Optional webhook signature key">
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap: 10px; padding: 14px; background: rgba(2, 132, 199, 0.05); border-radius: 8px; border: 1px solid rgba(2, 132, 199, 0.15);">
                            <input type="checkbox" name="is_active" id="is_active" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= (!empty($integration['is_active'])) ? 'checked' : '' ?>>
                            <label for="is_active" style="margin:0; cursor: pointer; font-size: 0.95rem;">Enable Moniepoint POS Integration</label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Save API Settings</button>
                    </form>
                </div>

                <!-- Simulation & Multi-POS Testing -->
                <div>
                    <div class="settings-card" style="border-left: 4px solid #10b981;">
                        <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                            <ion-icon name="flash-outline" style="color: #10b981;"></ion-icon> Real-Time Notification & Stat Test
                        </h3>
                        <p style="color: #64748b; font-size: 0.88rem; line-height: 1.5; margin-bottom: 20px;">
                            Simulate an incoming Moniepoint POS deposit or debit event from any of your registered terminals to verify that alerts and ERP stats update instantly!
                        </p>

                        <form action="/erp/moniepoint/simulate-test" method="POST">
                            <?php if (!empty($terminals)): ?>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label style="font-size: 0.88rem;">Select Which POS Terminal to Test:</label>
                                    <select name="terminal_serial" class="form-control">
                                        <?php foreach ($terminals as $term): ?>
                                            <option value="<?= htmlspecialchars($term['terminal_serial']) ?>">
                                                <?= htmlspecialchars($term['terminal_name']) ?> (<?= htmlspecialchars($term['terminal_serial']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                <button type="submit" name="test_type" value="deposit" class="btn" style="flex: 1; background: #10b981; color: #fff; font-weight: 700; padding: 12px;">
                                    <ion-icon name="arrow-down-circle-outline"></ion-icon> Test Deposit (+₦5,000)
                                </button>
                                <button type="submit" name="test_type" value="debit" class="btn" style="flex: 1; background: #ef4444; color: #fff; font-weight: 700; padding: 12px;">
                                    <ion-icon name="arrow-up-circle-outline"></ion-icon> Test Debit (-₦2,000)
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Multi-POS Summary -->
                    <div class="settings-card">
                        <h4 style="margin: 0 0 10px 0; font-size: 1rem;">How Multiple POS Devices Work:</h4>
                        <ul style="padding-left: 18px; margin: 0; color: #64748b; font-size: 0.88rem; line-height: 1.6;">
                            <li><strong>Unified Webhook</strong>: You only need to register the webhook URL once in Moniepoint. Moniepoint will automatically send transactions from ALL your POS terminals.</li>
                            <li><strong>Terminal Labeling</strong>: Transactions will specifically state which terminal made them (e.g., <em>"via Counter 1"</em> or <em>"via Bar POS"</em>).</li>
                            <li><strong>Selectable POS on Push Payment</strong>: Cashiers can choose which terminal prompts the customer when collecting payments.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="table-container" style="margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; font-size: 1.2rem;">Recent Moniepoint Activity Across All Terminals</h3>
                    <a href="/erp/transactions" class="btn btn-outline" style="text-decoration: none; font-size: 0.85rem;">View Full Ledger &rarr;</a>
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
                                    No Moniepoint transactions recorded yet.
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

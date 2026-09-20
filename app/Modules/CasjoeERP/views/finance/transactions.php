<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Transactions | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .bulk-bar {
            display: none;
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            margin-bottom: 16px;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.2s ease-in-out;
        }
        html.dark-theme .bulk-bar {
            background: #0f172a;
            border: 1px solid rgba(255,255,255,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Financial Transactions</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Track all cashflow, revenue inflows, and expense outflows</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <?php if (!empty($transactions)): ?>
                    <form method="POST" action="/erp/transactions/delete-all" onsubmit="return confirm('⚠️ DANGER: Are you sure you want to permanently delete ALL recorded transactions and expenses? This will completely reset your ledger and cannot be undone.');" style="margin: 0;">
                        <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 9px 14px; cursor: pointer; font-weight: 600;">
                            <ion-icon name="trash-bin-outline"></ion-icon> Delete All
                        </button>
                    </form>
                    <form method="POST" action="/erp/transactions/clean-duplicates" onsubmit="return confirm('Are you sure you want to scan and remove any duplicate transactions? The earliest record of each transaction will be safely kept.');" style="margin: 0;">
                        <button type="submit" class="btn" style="background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 9px 14px; cursor: pointer;">
                            <ion-icon name="duplicate-outline"></ion-icon> Clean Duplicates
                        </button>
                    </form>
                <?php endif; ?>
                <a href="/erp/transactions/import" class="btn" style="background: #0284c7; color: #fff; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <ion-icon name="cloud-upload-outline"></ion-icon> Import Excel / CSV
                </a>
                <a href="/erp/transactions/create" class="btn" style="display: inline-flex; align-items: center; gap: 6px;">
                    <ion-icon name="swap-horizontal-outline"></ion-icon> New Transaction
                </a>
            </div>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($errorMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($successMsg) ?></div>
            </div>
        <?php endif; ?>

        <!-- Bulk Selection Action Bar -->
        <form id="bulkForm" method="POST" action="/erp/transactions/bulk-delete" onsubmit="return confirm('Are you sure you want to delete the selected transactions?');">
            <div id="bulkBar" class="bulk-bar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkbox-outline" style="font-size: 20px; color: #38bdf8;"></ion-icon>
                    <span><strong id="selectedCount">0</strong> transaction(s) selected</span>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button type="button" onclick="deselectAll()" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color: #fff; border: none; padding: 6px 12px; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #dc2626; color: #fff; border: none; padding: 6px 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="trash-outline"></ion-icon> Delete Selected
                    </button>
                </div>
            </div>

            <div class="card">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #eee;">
                            <th style="padding: 10px; width: 36px; text-align: center;">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th style="padding: 10px;">Date</th>
                            <th style="padding: 10px;">Description</th>
                            <th style="padding: 10px;">Category</th>
                            <th style="padding: 10px;">Type</th>
                            <th style="padding: 10px;">Amount</th>
                            <th style="padding: 10px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; text-align: center;">
                                    <input type="checkbox" name="ids[]" value="<?= $txn['id'] ?>" class="txn-chk" onchange="updateSelectedCount()" style="cursor: pointer; width: 16px; height: 16px;">
                                </td>
                                <td style="padding: 10px; color: #666; white-space: nowrap;"><?= htmlspecialchars($txn['date']) ?></td>
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($txn['description']) ?></td>
                                <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($txn['category'] ?? 'General') ?></td>
                                <td style="padding: 10px;">
                                    <span style="background: <?= strtolower($txn['type']) == 'income' ? '#dcfce7' : '#fee2e2' ?>; color: <?= strtolower($txn['type']) == 'income' ? '#15803d' : '#991b1b' ?>; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        <?= ucfirst($txn['type']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; font-family: monospace; font-weight: 600; color: <?= strtolower($txn['type']) == 'income' ? '#15803d' : '#991b1b' ?>;">
                                    <?= (strtolower($txn['type']) == 'income' ? '+' : '-') ?><?= $currencySymbol ?><?= number_format($txn['amount'], 2) ?>
                                </td>
                                <td style="padding: 10px; text-align: right; white-space: nowrap;">
                                    <a href="/erp/transactions/edit?id=<?= $txn['id'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                                        <ion-icon name="create-outline"></ion-icon> Edit
                                    </a>
                                    <button type="button" onclick="deleteSingleTransaction(<?= $txn['id'] ?>)" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 8px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                        <ion-icon name="trash-outline"></ion-icon> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    No transactions recorded yet. <br>
                                    <a href="/erp/transactions/import" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Import a spreadsheet</a> or <a href="/erp/transactions/create" style="color: #0284c7; text-decoration: underline;">create one manually</a>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Hidden form for single row delete -->
        <form id="singleDeleteForm" method="POST" action="/erp/transactions/delete" style="display: none;">
            <input type="hidden" name="id" id="singleDeleteId">
        </form>

        <script>
        function toggleSelectAll(master) {
            const chks = document.querySelectorAll('.txn-chk');
            chks.forEach(c => c.checked = master.checked);
            updateSelectedCount();
        }

        function deselectAll() {
            const master = document.getElementById('selectAll');
            if (master) master.checked = false;
            const chks = document.querySelectorAll('.txn-chk');
            chks.forEach(c => c.checked = false);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const chks = document.querySelectorAll('.txn-chk:checked');
            const count = chks.length;
            const countSpan = document.getElementById('selectedCount');
            const bulkBar = document.getElementById('bulkBar');
            const master = document.getElementById('selectAll');

            if (countSpan) countSpan.textContent = count;
            if (bulkBar) {
                bulkBar.style.display = count > 0 ? 'flex' : 'none';
            }

            const allChks = document.querySelectorAll('.txn-chk');
            if (master && allChks.length > 0) {
                master.checked = (count === allChks.length);
            }
        }

        function deleteSingleTransaction(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                document.getElementById('singleDeleteId').value = id;
                document.getElementById('singleDeleteForm').submit();
            }
        }
        </script>
    </main>
</div>
</body>
</html>

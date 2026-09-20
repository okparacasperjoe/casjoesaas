<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Finance | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Finance & Accounting</h2>
            <div style="display: flex; gap: 10px;">
                <button class="btn" style="background: transparent; border: 1px solid var(--secondary); color: var(--secondary);" onclick="openJournalModal()"><ion-icon name="swap-horizontal-outline" style="vertical-align:middle;"></ion-icon> Journal Entry</button>
                <button class="btn" onclick="openAddAccountModal()"><ion-icon name="add-circle-outline" style="vertical-align:middle;"></ion-icon> Add Account</button>
            </div>
        </div>

        <div class="card">
            <h3>Chart of Accounts</h3>
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Code</th>
                        <th style="padding: 10px;">Name</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px; text-align: right;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accounts)): ?>
                        <tr><td colspan="4" style="padding: 20px; text-align: center;">No accounts found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($accounts as $acc): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-family: monospace;"><?= $acc['code'] ?></td>
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($acc['name']) ?></td>
                                <td style="padding: 10px;"><?= strtoupper($acc['type']) ?></td>
                                <td style="padding: 10px; text-align: right;"><?= htmlspecialchars($currencySymbol ?? '₦') ?><?= number_format($acc['balance'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- CSS overrides for Modals -->
<style>
    .cancel-modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center; }
    .cancel-modal.active { display: flex; }
    .modal-content { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px; width: 90%; max-width: 520px; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
</style>

<!-- Add Account Modal -->
<div class="cancel-modal" id="addAccountModal">
    <div class="modal-content">
        <h3 style="color:#000066; margin-top:0; font-size:1.3rem; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
            <ion-icon name="add-circle-outline" style="font-size:1.5rem; color:#FFA600; vertical-align:middle;"></ion-icon> Add Chart of Account
        </h3>
        <form method="POST" action="/erp/finance/account/store">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Account Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. 1010" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Account Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Cash at Hand" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Account Type</label>
                    <select name="type" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;">
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Initial Balance (<?= htmlspecialchars($cCode ?? 'NGN') ?>)</label>
                    <input type="number" name="balance" class="form-control" placeholder="0.00" step="0.01" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#334155 !important; border:1px solid #cbd5e1; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeAddAccountModal()">Dismiss</button>
                <button type="submit" class="btn" style="background:#000066; color:#ffffff !important; font-weight:600; border:none; padding:8px 20px; border-radius:6px; cursor:pointer;">Save Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Journal Entry Modal -->
<div class="cancel-modal" id="journalModal">
    <div class="modal-content">
        <h3 style="color:#000066; margin-top:0; font-size:1.3rem; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
            <ion-icon name="swap-horizontal-outline" style="font-size:1.5rem; color:#FFA600; vertical-align:middle;"></ion-icon> New Journal Entry
        </h3>
        <form method="POST" action="/erp/finance/journal/store">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Debit Account *</label>
                    <select name="debit_account_id" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;" required>
                        <option value="">-- Select Debit Account --</option>
                        <?php foreach ($accounts as $acc): ?>
                            <option value="<?= $acc['id'] ?>"><?= $acc['code'] ?> - <?= htmlspecialchars($acc['name']) ?> (<?= strtoupper($acc['type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Credit Account *</label>
                    <select name="credit_account_id" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;" required>
                        <option value="">-- Select Credit Account --</option>
                        <?php foreach ($accounts as $acc): ?>
                            <option value="<?= $acc['id'] ?>"><?= $acc['code'] ?> - <?= htmlspecialchars($acc['name']) ?> (<?= strtoupper($acc['type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Amount (<?= htmlspecialchars($cCode ?? 'NGN') ?>) *</label>
                    <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Description *</label>
                <input type="text" name="description" class="form-control" placeholder="e.g. Purchase office furniture" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#334155 !important; border:1px solid #cbd5e1; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeJournalModal()">Dismiss</button>
                <button type="submit" class="btn" style="background:#000066; color:#ffffff !important; font-weight:600; border:none; padding:8px 20px; border-radius:6px; cursor:pointer;">Post Entry</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddAccountModal() {
        document.getElementById('addAccountModal').classList.add('active');
    }
    function closeAddAccountModal() {
        document.getElementById('addAccountModal').classList.remove('active');
    }
    function openJournalModal() {
        document.getElementById('journalModal').classList.add('active');
    }
    function closeJournalModal() {
        document.getElementById('journalModal').classList.remove('active');
    }
</script>
</body>
</html>

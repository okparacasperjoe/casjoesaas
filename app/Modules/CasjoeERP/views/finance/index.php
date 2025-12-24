<!DOCTYPE html>
<html lang="en">
<head>
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
                <button class="btn" style="background: transparent; border: 1px solid var(--secondary); color: var(--secondary);">Journal Entry</button>
                <button class="btn">+ Add Account</button>
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
                                <td style="padding: 10px; text-align: right;">₦<?= number_format($acc['balance'], 2) ?></td>
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

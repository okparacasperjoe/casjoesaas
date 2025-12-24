<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Casjoe Pay | Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .wallet-card {
            background: linear-gradient(135deg, #000044 0%, #000066 100%);
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,102,0.3);
        }
        .balance-label { font-size: 0.9rem; opacity: 0.8; margin-bottom: 5px; }
        .balance-amount { font-size: 2.5rem; font-weight: 700; color: #FFA600; }
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .action-card {
            background: rgba(255,255,255,0.05); /* Glass */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--text-color);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .action-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.1); }
        .action-icon { font-size: 2rem; margin-bottom: 10px; color: #FFA600; }
        .txn-table { width: 100%; border-collapse: collapse; }
        .txn-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .txn-amount.credit { color: #2ed573; }
        .txn-amount.debit { color: #ff4757; }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="speedometer-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/pay" class="nav-link active"><ion-icon name="wallet-outline"></ion-icon> Casjoe Pay</a></li>
            <li class="nav-item"><a href="/logout" class="nav-link"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Casjoe Pay</h2>
            <div class="btn" style="background: transparent; border: 1px solid #FFA600;">Wallet ID: <?= $_SESSION['user_id'] ?></div>
        </div>

        <div class="wallet-card">
            <div>
                <div class="balance-label">Available Balance</div>
                <div class="balance-amount">₦ <?= number_format($wallet['balance'] ?? 0, 2) ?></div>
            </div>
            <div>
                <a href="/pay/fund" class="btn" style="padding: 10px 20px;">+ Fund Wallet</a>
            </div>
        </div>

        <div class="action-grid">
            <a href="/pay/fund" class="action-card">
                <ion-icon name="add-circle-outline" class="action-icon"></ion-icon>
                <span>Fund Wallet</span>
            </a>
            <a href="/pay/transfer" class="action-card">
                <ion-icon name="paper-plane-outline" class="action-icon"></ion-icon>
                <span>Transfer</span>
            </a>
            <a href="#" class="action-card">
                <ion-icon name="card-outline" class="action-icon"></ion-icon>
                <span>Virtual Cards (Soon)</span>
            </a>
        </div>

        <div class="card">
            <h3>Recent Transactions</h3>
            <?php if (empty($transactions)): ?>
                <p style="color: var(--text-muted); text-align: center; padding: 20px;">No transactions yet.</p>
            <?php else: ?>
                <table class="txn-table">
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td>
                                <div style="font-weight: bold;"><?= htmlspecialchars($txn['description'] ?? 'Transaction') ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?= $txn['created_at'] ?></div>
                            </td>
                            <td style="text-align: right;">
                                <div class="txn-amount <?= $txn['type'] ?>">
                                    <?= $txn['type'] == 'credit' ? '+' : '-' ?> ₦<?= number_format($txn['amount'], 2) ?>
                                </div>
                                <div style="font-size: 0.8rem; text-transform: uppercase;">
                                    <?= $txn['status'] ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
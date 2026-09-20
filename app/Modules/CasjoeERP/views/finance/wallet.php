<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet & Withdrawals | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .wallet-card {
            background: linear-gradient(135deg, #000066 0%, #000033 100%);
            color: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 102, 0.2);
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wallet-card::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 166, 0, 0.1);
            border-radius: 50%;
        }
        .balance-label { font-size: 1rem; opacity: 0.8; margin-bottom: 5px; }
        .balance-amount { font-size: 3rem; font-weight: 700; margin: 0; }
        
        .btn-withdraw {
            background: #ffa600;
            color: #000066;
            font-weight: 600;
            padding: 15px 30px;
            border-radius: 12px;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-withdraw:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 166, 0, 0.3); }

        .tx-credit { color: #10b981; font-weight: 600; }
        .tx-debit { color: #ef4444; font-weight: 600; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 40px; border-radius: 16px; width: 100%; max-width: 500px; position: relative; color: #333; }
        .modal-content label { color: #1e293b; font-weight: 600; display: block; margin-bottom: 8px; }
        .modal-content input, .modal-content select { background-color: #fff; color: #333; border: 1px solid #ccc; }
        .modal-close { position: absolute; top: 20px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #888; }
        .form-group { margin-bottom: 20px; }
        
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed; top: 0; left: -260px; height: 100%; z-index: 1000; transition: 0.3s; width: 260px; background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .wallet-card { flex-direction: column; align-items: flex-start; gap: 20px; padding: 25px; }
            .balance-amount { font-size: 2.5rem; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

        <div class="top-bar">
            <h2>Wallet & Withdrawals</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ion-icon name="checkmark-circle-outline"></ion-icon> <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ion-icon name="warning-outline"></ion-icon> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="wallet-card">
            <div>
                <div class="balance-label">Available Balance</div>
                <div class="balance-amount">₦<?= number_format($wallet['balance'] ?? 0, 2) ?></div>
            </div>
            <button class="btn-withdraw" onclick="document.getElementById('withdrawModal').style.display='flex'">
                <ion-icon name="cash-outline"></ion-icon> Withdraw Funds
            </button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 40px;">
            <!-- Transactions -->
            <div class="card" style="margin: 0;">
                <h3 style="margin-top: 0;">Recent Transactions</h3>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="3" style="text-align: center;">No transactions yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($tx['description']) ?></td>
                                        <td class="<?= $tx['type'] == 'credit' ? 'tx-credit' : 'tx-debit' ?>">
                                            <?= $tx['type'] == 'credit' ? '+' : '-' ?>₦<?= number_format($tx['amount'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Withdrawals -->
            <div class="card" style="margin: 0;">
                <h3 style="margin-top: 0;">Withdrawal History</h3>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($withdrawals)): ?>
                                <tr><td colspan="4" style="text-align: center;">No withdrawals requested.</td></tr>
                            <?php else: ?>
                                <?php foreach ($withdrawals as $w): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($w['created_at'])) ?></td>
                                        <td style="text-transform: capitalize;"><?= htmlspecialchars($w['withdrawal_method']) ?></td>
                                        <td>₦<?= number_format($w['amount'], 2) ?></td>
                                        <td>
                                            <span class="status-badge" style="background: <?= $w['status'] == 'completed' ? '#d1fae5; color: #065f46;' : ($w['status'] == 'pending' ? '#fef3c7; color: #b45309;' : '#fee2e2; color: #991b1b;') ?>">
                                                <?= ucfirst($w['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="modal">
    <div class="modal-content">
        <ion-icon name="close-outline" class="modal-close" onclick="document.getElementById('withdrawModal').style.display='none'"></ion-icon>
        <h2 style="color: #000066; margin-top: 0;">Request Withdrawal</h2>
        <p style="color: #666; font-size: 0.9rem;">Withdraw your available balance. A 1.5% withdrawal fee applies.</p>
        
        <form action="/erp/wallet/withdraw" method="POST">
            <div class="form-group">
                <label>Amount to Withdraw (NGN)</label>
                <input type="number" name="amount" id="withdrawAmount" class="form-control" max="<?= $wallet['balance'] ?? 0 ?>" step="0.01" required oninput="calculateFee()">
                <div style="font-size: 0.85rem; color: #888; margin-top: 5px;">
                    Fee (1.5%): ₦<span id="feeDisplay">0.00</span> | You Receive: <strong style="color: #10b981;">₦<span id="netDisplay">0.00</span></strong>
                </div>
            </div>

            <div class="form-group">
                <label>Withdrawal Method</label>
                <select name="withdrawal_method" id="withdrawalMethod" class="form-control" onchange="toggleMethod()">
                    <option value="bank">Bank Transfer (NGN)</option>
                    <option value="crypto">USDT (Cryptocurrency)</option>
                </select>
            </div>

            <!-- Bank Fields -->
            <div id="bankFields">
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. Zenith Bank">
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control" placeholder="10 digits">
                </div>
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" class="form-control" placeholder="John Doe">
                </div>
            </div>

            <!-- Crypto Fields -->
            <div id="cryptoFields" style="display: none;">
                <div class="form-group">
                    <label>Crypto Network</label>
                    <select name="crypto_network" class="form-control">
                        <option value="USDT_TRC20">USDT (TRC20)</option>
                        <option value="USDT_ERC20">USDT (ERC20)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Wallet Address</label>
                    <input type="text" name="crypto_address" class="form-control" placeholder="Paste your USDT wallet address here">
                </div>
                <div style="background: #fffbeb; padding: 10px; border-left: 4px solid #f59e0b; margin-bottom: 20px; font-size: 0.85rem;">
                    Please ensure the network matches the address. Funds sent to the wrong network cannot be recovered.
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; background: #000066;">Submit Request</button>
        </form>
    </div>
</div>

<script>
    function calculateFee() {
        const amount = parseFloat(document.getElementById('withdrawAmount').value) || 0;
        const fee = amount * 0.015;
        const net = amount - fee;
        document.getElementById('feeDisplay').textContent = fee.toFixed(2);
        document.getElementById('netDisplay').textContent = net.toFixed(2);
    }

    function toggleMethod() {
        const method = document.getElementById('withdrawalMethod').value;
        if (method === 'crypto') {
            document.getElementById('bankFields').style.display = 'none';
            document.getElementById('cryptoFields').style.display = 'block';
        } else {
            document.getElementById('bankFields').style.display = 'block';
            document.getElementById('cryptoFields').style.display = 'none';
        }
    }
</script>

</body>
</html>

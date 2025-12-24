<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cards | Casjoe Pay</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .virtual-card {
            background: linear-gradient(135deg, #000066, #0000aa);
            border-radius: 15px;
            padding: 20px;
            color: white;
            width: 300px;
            height: 180px;
            position: relative;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 20px;
            vertical-align: top;
        }

        .card-chip {
            width: 40px;
            height: 30px;
            background: #ffd700;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .card-number {
            font-size: 1.2rem;
            letter-spacing: 2px;
            margin-bottom: 20px;
            font-family: monospace;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="card"></ion-icon>
                Casjoe<span>Pay</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="nav-item"><a href="/pay" class="nav-link"><ion-icon
                            name="grid-outline"></ion-icon>Dashboard</a></li>
                <li class="nav-item"><a href="/pay/wallet/fund" class="nav-link"><ion-icon
                            name="add-circle-outline"></ion-icon>Fund Wallet</a></li>
                <li class="nav-item"><a href="/pay/transfer" class="nav-link"><ion-icon
                            name="paper-plane-outline"></ion-icon>Transfer</a></li>
                <li class="nav-item"><a href="/pay/cards" class="nav-link active"><ion-icon
                            name="card-outline"></ion-icon>Virtual Cards</a></li>
                <li class="nav-item"><a href="/pay/links" class="nav-link"><ion-icon
                            name="link-outline"></ion-icon>Payment Links</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>Virtual Cards</h1>
            </div>

            <div style="margin-bottom: 30px;">
                <?php if (empty($cards)): ?>
                    <div
                        style="background: rgba(255,255,255,0.05); padding: 30px; border-radius: 10px; text-align: center;">
                        <ion-icon name="card-outline" style="font-size: 48px; opacity: 0.5;"></ion-icon>
                        <p>No virtual cards found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cards as $card): ?>
                        <div class="virtual-card">
                            <div class="card-chip"></div>
                            <div class="card-number"><?= htmlspecialchars($card['masked_pan']) ?></div>
                            <div class="card-meta">
                                <span>EXP: <?= $card['start_month'] ?>/<?= $card['start_year'] ?></span>
                                <span><?= htmlspecialchars($card['card_type']) ?></span>
                            </div>
                            <div style="position: absolute; bottom: 20px; right: 20px; font-weight: bold;">
                                <?= htmlspecialchars($card['status']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Create Card Form -->
            <div class="card" style="max-width: 500px;">
                <h3>Create New Card</h3>
                <form method="POST" action="/pay/cards/create">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Initial Funding (NGN)</label>
                        <input type="number" name="amount" min="1000" required class="form-control"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;">
                    </div>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Creation is free. Amount will be
                        deducted from your wallet to fund the card.</p>
                    <button type="submit" class="btn" style="width: 100%;">Create Card</button>
                </form>
            </div>

        </main>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Wallet | Casjoe Pay</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <?php include __DIR__ . '/partials/sidebar_pay_css.php'; ?>

            <div class="pay-brand">
                <ion-icon name="card"></ion-icon>
                Casjoe<span>Pay</span>
            </div>
            <ul class="pay-menu">
                <li class="pay-item"><a href="/" class="pay-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="pay-item"><a href="/pay" class="pay-link"><ion-icon
                            name="grid-outline"></ion-icon>Dashboard</a></li>
                <li class="pay-item"><a href="/pay/wallet/fund" class="pay-link active"><ion-icon
                            name="add-circle-outline"></ion-icon>Fund Wallet</a></li>
                <li class="pay-item"><a href="/pay/transfer" class="pay-link"><ion-icon
                            name="paper-plane-outline"></ion-icon>Transfer</a></li>
                <li class="pay-item"><a href="/pay/cards" class="pay-link"><ion-icon
                            name="card-outline"></ion-icon>Virtual Cards</a></li>
                <li class="pay-item"><a href="/pay/links" class="pay-link"><ion-icon
                            name="link-outline"></ion-icon>Payment Links</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Fund Your Wallet</h1>
            </div>

            <div class="card" style="max-width: 500px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <ion-icon name="wallet" style="font-size: 48px; color: #FFA600;"></ion-icon>
                    <p>Add money to your workspace wallet securely.</p>
                </div>

                <form method="POST" action="/pay/wallet/fund/init">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Amount to Add (NGN)</label>
                        <?php
                        $db = \App\Core\Database::getInstance()->getConnection();
                        $minFunding = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'min_funding_amount'")->fetchColumn();
                        $minFunding = $minFunding ?: 100;
                        ?>
                        <input type="number" name="amount" min="<?= htmlspecialchars($minFunding) ?>" required placeholder="e.g. <?= htmlspecialchars($minFunding) ?>"
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    </div>
                    <button type="submit" class="btn" style="width: 100%; padding: 12px;">Proceed to Payment</button>
                    <p style="text-align: center; font-size: 0.8rem; margin-top: 15px; color: #666;">Secured by
                        Flutterwave</p>
                </form>
            </div>
        </main>
    </div>
</body>

</html>

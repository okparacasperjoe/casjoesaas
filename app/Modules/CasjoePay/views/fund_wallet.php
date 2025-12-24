<!DOCTYPE html>
<html lang="en">

<head>
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
            <div class="brand">
                <ion-icon name="card"></ion-icon>
                Casjoe<span>Pay</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="nav-item"><a href="/pay" class="nav-link"><ion-icon
                            name="grid-outline"></ion-icon>Dashboard</a></li>
                <li class="nav-item"><a href="/pay/wallet/fund" class="nav-link active"><ion-icon
                            name="add-circle-outline"></ion-icon>Fund Wallet</a></li>
                <li class="nav-item"><a href="/pay/transfer" class="nav-link"><ion-icon
                            name="paper-plane-outline"></ion-icon>Transfer</a></li>
                <li class="nav-item"><a href="/pay/cards" class="nav-link"><ion-icon
                            name="card-outline"></ion-icon>Virtual Cards</a></li>
                <li class="nav-item"><a href="/pay/links" class="nav-link"><ion-icon
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
                        <input type="number" name="amount" min="100" required placeholder="e.g. 5000"
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
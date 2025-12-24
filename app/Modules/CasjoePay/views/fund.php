<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fund Wallet | Casjoe Pay</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .form-container { max-width: 500px; margin: 50px auto; }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/pay" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Wallet</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="form-container">
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 30px;">Fund Wallet</h2>
                <form method="POST" action="/pay/fund">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px;">Amount (NGN)</label>
                        <input type="number" name="amount" placeholder="0.00" class="support-input" min="100" required step="0.01">
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">Pay with Flutterwave</button>
                    <p style="text-align: center; font-size: 0.8rem; color: #aaa; margin-top: 10px;">Secure payment via Flutterwave</p>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer | Casjoe Pay</title>
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
                <h2 style="text-align: center; margin-bottom: 30px;">Transfer Money</h2>
                <form method="POST" action="/pay/transfer">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px;">Recipient Email</label>
                        <input type="email" name="email" placeholder="Recipient Email" class="support-input" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px;">Amount (NGN)</label>
                        <input type="number" name="amount" placeholder="0.00" class="support-input" min="100" required step="0.01">
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">Send Money</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
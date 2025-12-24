<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade Plan | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .upgrade-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            display: flex;
            gap: 40px;
        }
        .plan-card {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            border: 2px solid transparent;
        }
        .plan-card.highlight {
            border-color: var(--secondary);
            transform: scale(1.05);
        }
        .price {
            font-size: 3rem;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
        }
        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .features ion-icon {
            color: var(--secondary);
            font-size: 1.2rem;
        }
    </style>
</head>
<body style="background: #f0f4f8;">

<div style="text-align: center; margin-top: 50px;">
    <h1>Unlock the Full Power of Casjoe</h1>
    <p style="color: #666;">One subscription. Unlimited possibilities.</p>
</div>

<div class="upgrade-container">
    <div class="plan-card highlight">
        <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: var(--secondary); color: white; padding: 5px 20px; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">MOST POPULAR</div>
        <h2 style="margin-top: 0;">Casjoe Premium</h2>
        <div class="price">₦50,000<span style="font-size: 1rem; color: #999;">/mo</span></div>
        <p style="color: #666;">Everything you need to run your business.</p>
        
        <ul class="features" style="list-style: none; padding: 0; margin: 30px 0;">
            <li><ion-icon name="checkmark-circle"></ion-icon> <strong>Casjoe ERP</strong> (Finance, HR, CRM)</li>
            <li><ion-icon name="checkmark-circle"></ion-icon> <strong>Casjoe Pay</strong> (Transfers & Links)</li>
            <li><ion-icon name="checkmark-circle"></ion-icon> <strong>Casjoe Academy</strong> (LMS)</li>
            <li><ion-icon name="checkmark-circle"></ion-icon> Priority Support</li>
        </ul>

        <form method="POST" action="/billing/pay">
            <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1.1rem; justify-content: center;">Pay with Flutterwave</button>
            <p style="text-align: center; font-size: 0.8rem; color: #999; margin-top: 10px;">Secure 128-bit SSL Encrypted Payment</p>
        </form>
    </div>
</div>

<div style="text-align: center; margin-bottom: 50px;">
    <a href="/billing" style="color: #666; text-decoration: none;">Cancel and return to dashboard</a>
</div>

</body>
</html>

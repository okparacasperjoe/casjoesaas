<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Resend Verification - Casjoe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        h2 { color: #000066; margin-bottom: 20px; }
        p { color: #666; font-size: 0.95rem; margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #FFA600; color: #000066; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: background 0.3s; }
        button:hover { background-color: #ffb700; }
        .back-link { display: block; margin-top: 20px; color: #000066; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Verify Your Email</h2>
        <p>Enter your email address and we'll send you a new verification link.</p>
        
        <?php if (isset($_GET['success'])): ?>
            <div style="color: green; margin-bottom: 20px; font-weight: 500;">Check your email for the new link!</div>
        <?php endif; ?>

        <form action="/resend-verification" method="POST">
            <?= \App\Core\Services\CsrfService::generateInput() ?>
            <input type="email" name="email" placeholder="name@company.com" required>
            <button type="submit">Resend Verification Email</button>
        </form>
        
        <a href="/login" class="back-link">&larr; Back to Login</a>
    </div>
</body>
</html>

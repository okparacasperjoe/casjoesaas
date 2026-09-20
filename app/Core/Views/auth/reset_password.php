<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Reset Password - Casjoe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', sans-serif;
            background: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 102, 0.10);
            border: 1px solid rgba(0, 0, 102, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .logo-img {
            max-height: 70px;
            width: auto;
            object-fit: contain;
            margin: 0 auto 20px auto;
            display: block;
        }

        h2 {
            color: #000066;
            margin: 0 0 8px 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-control {
            width: 100%;
            padding: 13px 16px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #f8f9fc;
            color: #333;
            font-size: 1rem;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            background: #fff;
            border-color: #FFA600;
            box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.1);
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            border-radius: 10px;
            background: #FFA600;
            border: none;
            color: #000066;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            background: #ffb700;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
        }

        .lock-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #000066, #000044);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }

        .lock-icon ion-icon {
            font-size: 28px;
            color: #FFA600;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="/casjoe_logo.png" alt="Casjoe Logo" class="logo-img">

        <div class="lock-icon">
            <ion-icon name="key-outline"></ion-icon>
        </div>

        <h2>Set New Password</h2>
        <p class="subtitle">Create a strong password for your account.</p>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <ion-icon name="alert-circle-outline" style="font-size: 1.2rem; flex-shrink: 0;"></ion-icon>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/reset-password" method="POST">
            <?= \App\Core\Services\CsrfService::generateInput() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="New Password" required minlength="8">
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required minlength="8">
            </div>
            <button type="submit" class="btn-submit">Reset Password</button>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title>Complete Your Account | Casjoe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000066 0%, #000044 50%, #FFA600 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .login-container {
            background: rgba(0, 0, 102, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 166, 0, 0.2);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
            color: white;
            margin: 20px;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #FFA600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #FFA600;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background: #FFA600;
            border: none;
            color: #000066;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
        }

        .btn-login:hover {
            transform: scale(1.02);
            background: #ffb700;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid rgba(231, 76, 60, 0.5);
            color: #ffcccc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo">Casjoe</div>
        <div class="subtitle">Complete Your Account Setup</div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'phone_required'): ?>
            <div class="alert-error">
                <ion-icon name="warning-outline"></ion-icon> Phone number is required to continue.
            </div>
        <?php endif; ?>

        <form action="/auth/google/phone" method="POST">
            <div class="form-group">
                <label style="display:block; margin-bottom: 5px; font-size: 0.9rem;">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="e.g. +2348000000000" required>
            </div>

            <button type="submit" class="btn-login">Complete Sign-Up</button>
        </form>
    </div>

</body>
</html>

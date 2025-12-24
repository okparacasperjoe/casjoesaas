<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Casjoe SaaS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000066 0%, #000044 50%, #FFA600 100%); /* Brand Gradient */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .login-container {
            background: rgba(0, 0, 102, 0.4); /* Brand Blue with transparency */
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 166, 0, 0.2); /* Brand Orange Border hint */
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
            color: white;
            margin: 20px;
        }

/* ... styles ... */



        .logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #FFA600; /* Brand Orange */
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
            border-color: #FFA600; /* Brand Orange Focus */
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background: #FFA600; /* Brand Orange */
            border: none;
            color: #000066; /* Brand Blue Text */
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

        .divider {
            margin: 25px 0;
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.2);
        }
        .divider span { padding: 0 10px; }

        .btn-google {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background: white;
            color: #333;
            border: none;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.2s;
        }
        .btn-google:hover { background: #f1f1f1; }
    </style>
</head>
<body>

<div class="login-container">
    <img src="/casjoe_logo.png" alt="Casjoe Logo" class="logo-img" style="max-width: 200px; margin-bottom: 20px;">
    <!-- <div class="logo">Casjoe</div> -->
    <div class="subtitle">Create your account</div>

    <?php if (isset($error)): ?>
        <div class="alert-error">
            <ion-icon name="alert-circle-outline" style="vertical-align: middle;"></ion-icon> 
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <?= \App\Core\Services\CsrfService::getTokenField() ?>
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="text" name="business_name" class="form-control" placeholder="Business Name" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn-login">Register</button>
    </form>

    <div class="divider"><span>OR CONTINUE WITH</span></div>

    <div style="display: flex; gap: 10px;">
        <a href="/auth/google" style="text-decoration: none; flex: 1;">
            <button type="button" class="btn-google">
                <ion-icon name="logo-google" style="color: #DB4437; font-size: 1.2rem;"></ion-icon>
                Google
            </button>
        </a>
        <a href="/auth/linkedin" style="text-decoration: none; flex: 1;">
            <button type="button" class="btn-google">
                <ion-icon name="logo-linkedin" style="color: #0077b5; font-size: 1.2rem;"></ion-icon>
                LinkedIn
            </button>
        </a>
    </div>

    <p style="margin-top: 20px; font-size: 0.8rem; color: rgba(255,255,255,0.5);">
        Already have an account? <a href="/login" style="color: #FFA600; text-decoration: none;">Sign In</a>
    </p>
</div>

</body>
</html>
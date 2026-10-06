<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title>Register | Casjoe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000066 0%, #000044 50%, #FFA600 100%); /* Brand Gradient */
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .login-container {
            box-sizing: border-box;
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
    
        @media (max-width: 480px) {
            .page-wrapper { padding: 15px; }
            .login-container { padding: 30px 18px !important; border-radius: 16px !important; }
            .logo-img { max-height: 44px !important; max-width: 80% !important; }
        }
    </style>
</head>
<body>

<?php 
    $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
    if (!empty($clientId)):
?>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="<?= htmlspecialchars($clientId) ?>"
     data-login_uri="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/auth/google/onetap" ?>"
     data-auto_prompt="true"
     data-use_fedcm_for_prompt="true"
     style="position: fixed; top: 20px; right: 20px; z-index: 10000;">
</div>
<?php endif; ?>

<div class="page-wrapper">
<div class="login-container">
    <img src="/casjoe_logo.png" alt="Casjoe Logo" class="logo-img" style="max-height: 52px; max-width: 85%; width: auto; object-fit: contain; margin: 0 auto 18px auto; display: block;">
    <!-- <div class="logo">Casjoe</div> -->
    <div class="subtitle">Create your account</div>

    <?php if (isset($error)): ?>
        <div class="alert-error">
            <ion-icon name="alert-circle-outline" style="vertical-align: middle;"></ion-icon> 
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($activationPending) || !empty($_GET['registered'])): ?>
        <div style="background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #fff; padding: 25px; border-radius: 15px; margin-bottom: 25px; text-align: center;">
            <ion-icon name="mail-unread-outline" style="font-size: 3.5rem; color: #2ecc71; margin-bottom: 10px;"></ion-icon>
            <h3 style="margin: 0 0 10px 0; color: #FFA600;">Activate Your Account</h3>
            <p style="margin: 0 0 15px 0; font-size: 0.95rem; line-height: 1.5; color: rgba(255,255,255,0.9);">
                We have sent an activation link to <strong style="color: #fff;"><?= htmlspecialchars($activatedEmail ?? $_GET['email'] ?? 'your email address') ?></strong>.
            </p>
            <p style="margin: 0; font-size: 0.85rem; color: rgba(255,255,255,0.7);">
                Please check your inbox (and spam folder) and click the link to verify your email and begin your business setup.
            </p>
        </div>
    <?php else: ?>

    <?php $currentRef = htmlspecialchars($_GET['ref'] ?? ($_SESSION['ref_code'] ?? '')); ?>
    <?php if (!empty($currentRef)): ?>
        <div style="background: rgba(255, 166, 0, 0.15); border: 1px solid #FFA600; color: #FFA600; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
            <ion-icon name="gift-outline" style="font-size: 1.2rem;"></ion-icon>
            <span>You were invited with Referral Code: <strong><?= $currentRef ?></strong></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <?= \App\Core\Services\CsrfService::getTokenField() ?>
        <input type="hidden" name="ref_code" value="<?= $currentRef ?>">
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email Address" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn-login">Register</button>
    </form>
    <?php endif; ?>

    <div class="divider"><span>OR CONTINUE WITH</span></div>

    <div style="display: flex; gap: 10px; align-items: stretch;">
        <div style="flex: 1; position: relative; min-height: 44px; display: flex;">
            <button type="button" class="btn-google" style="width: 100%; height: 100%;" onclick="if(typeof google !== 'undefined' && google.accounts && google.accounts.id){ google.accounts.id.prompt(); } else { window.location.href='/auth/google'; }">
                <ion-icon name="logo-google" style="color: #DB4437; font-size: 1.2rem;"></ion-icon>
                Google
            </button>
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="continue_with"
                 data-size="large"
                 data-logo_alignment="left"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.01; overflow: hidden; z-index: 5;">
            </div>
        </div>
        <a href="/auth/linkedin" style="text-decoration: none; flex: 1; display: flex;">
            <button type="button" class="btn-google" style="width: 100%; height: 100%;">
                <ion-icon name="logo-linkedin" style="color: #0077b5; font-size: 1.2rem;"></ion-icon>
                LinkedIn
            </button>
        </a>
    </div>

    <p style="margin-top: 20px; font-size: 0.8rem; color: rgba(255,255,255,0.5);">
        Already have an account? <a href="/login" style="color: #FFA600; text-decoration: none;">Sign In</a>
    </p>
</div>
</div>

</body>
</html>

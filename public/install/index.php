<?php
// Casjoe Professional Installer
session_start();

$step = $_GET['step'] ?? 0;

function checkRequirement($name, $condition) {
    return [
        'name' => $name,
        'status' => $condition ? '✅' : '❌',
        'passed' => $condition
    ];
}

$requirements = [
    checkRequirement('PHP Version >= 8.1', version_compare(PHP_VERSION, '8.1.0', '>=')),
    checkRequirement('PDO Extension', extension_loaded('pdo')),
    checkRequirement('JSON Extension', extension_loaded('json')),
    checkRequirement('CURL Extension', extension_loaded('curl')),
    checkRequirement('OpenSSL Extension', extension_loaded('openssl')),
    checkRequirement('MBString Extension', extension_loaded('mbstring')),
    checkRequirement('Write Permissions (config/)', is_writable(__DIR__ . '/../../app/Core/config')),
];

$allPassed = true;
foreach ($requirements as $r) if (!$r['passed']) $allPassed = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casjoe — Installation Wizard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --clr-primary: #7c3aed;
            --clr-bg: #060610;
            --clr-surface: #12121f;
            --grad: linear-gradient(135deg, #7c3aed 0%, #ff6eb4 100%);
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--clr-bg);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .orb {
            position: absolute;
            width: 600px; height: 600px;
            background: var(--clr-primary);
            filter: blur(150px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 50%;
        }
        .install-card {
            background: var(--clr-surface);
            border: 1px solid rgba(255,255,255,0.05);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
        }
        .logo {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .step-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .step-dot.active { background: var(--clr-primary); width: 24px; border-radius: 10px; }

        .req-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 0.9rem;
        }
        .btn {
            background: var(--grad);
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(124,58,237,0.3); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; color: rgba(255,255,255,0.6); }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 12px;
            border-radius: 12px;
            color: white;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="orb"></div>

    <div class="install-card">
        <div class="logo">✦ Casjoe</div>
        
        <div class="step-indicator">
            <div class="step-dot <?= $step == 0 ? 'active' : '' ?>"></div>
            <div class="step-dot <?= $step == 1 ? 'active' : '' ?>"></div>
            <div class="step-dot <?= $step == 2 ? 'active' : '' ?>"></div>
            <div class="step-dot <?= $step == 3 ? 'active' : '' ?>"></div>
            <div class="step-dot <?= $step == 4 ? 'active' : '' ?>"></div>
        </div>

        <?php if ($step == 0): ?>
            <h3>License Activation</h3>
            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">Enter your Envato Purchase Code to activate Casjoe on this domain.</p>
            <form action="verify_license.php" method="POST">
                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label">Purchase Code</label>
                    <input type="text" name="purchase_code" class="form-control" placeholder="xxxx-xxxx-xxxx-xxxx" required>
                </div>
                <div style="font-size: 0.7rem; color: #ff6eb4; margin-bottom: 15px;">
                    Note: This code will be locked to <b><?= $_SERVER['HTTP_HOST'] ?></b>.
                </div>
                <button type="submit" class="btn">Verify & Continue</button>
            </form>

        <?php elseif ($step == 1): ?>
            <h3>Server Requirements</h3>
            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">Checking if your server is ready for Casjoe SaaS.</p>
            <div style="margin-top: 20px;">
                <?php foreach($requirements as $r): ?>
                    <div class="req-item">
                        <span><?= $r['name'] ?></span>
                        <span><?= $r['status'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn" onclick="window.location.href='?step=2'" <?= !$allPassed ? 'disabled' : '' ?>>Next Step</button>

        <?php elseif ($step == 2): ?>
            <h3>Database Setup</h3>
            <form action="setup.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="localhost">
                </div>
                <div class="form-group">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="db_user" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="db_pass" class="form-control">
                </div>
                <button type="submit" class="btn">Connect & Initialize</button>
            </form>

        <?php elseif ($step == 3): ?>
            <h3>Admin Creation</h3>
            <form action="setup_admin.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="admin_name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="admin_email" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="admin_pass" class="form-control">
                </div>
                <button type="submit" class="btn">Create Administrator</button>
            </form>

        <?php elseif ($step == 4): ?>
            <div style="text-align: center;">
                <div style="font-size: 4rem;">🎉</div>
                <h3>Installation Complete!</h3>
                <p style="color: rgba(255,255,255,0.6);">Casjoe has been successfully installed.</p>
                <button class="btn" onclick="window.location.href='/'">Go to Dashboard</button>
                <p style="font-size: 0.75rem; color: #ff6eb4; margin-top: 20px;">Please delete the <b>public/install/</b> directory for security.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>

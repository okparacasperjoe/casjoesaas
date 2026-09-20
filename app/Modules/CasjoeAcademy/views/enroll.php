<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Enroll in <?= htmlspecialchars($course['title']) ?> | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .pay-card {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            color: #333;
        }
    </style>
</head>
<body>
<?php require_once dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/partials/sidebar_acad_css.php'; ?>

        <div class="acad-brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe Apps" style="height: 40px;"></a></div>
        <ul class="acad-menu">
            <li class="acad-item"><a href="/academy/course/<?= $course['id'] ?>" class="acad-link"><ion-icon name="arrow-back-outline"></ion-icon> Cancel</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="pay-card">
            <h2 style="margin-top:0;">Confirm Enrollment</h2>
            <p style="color:#666; margin-bottom: 20px;">You are about to enroll in <strong><?= htmlspecialchars($course['title']) ?></strong>.</p>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="font-size: 0.9rem; color: #888;">Price</div>
                <div style="font-size: 1.5rem; font-weight: bold; color: #333;">₦<?= number_format($course['price'], 2) ?></div>
            </div>

            <form action="/academy/enroll/process" method="POST">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 8px;">Select Payment Wallet</label>
                    <select name="currency" class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="NGN">NGN Wallet (₦<?= number_format($course['price'], 2) ?>)</option>
                        <option value="USD">USD Wallet (Approx $<?= number_format($course['price'] / 1500, 2) ?>)</option>
                        <option value="CJC">CJC Token (Approx <?= number_format(($course['price'] / 1500) * 1000) ?> CJC)</option>
                    </select>
                </div>

                <button type="submit" class="btn" style="width: 100%; background: var(--secondary); color: white; padding: 12px;">Pay & Enroll</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>


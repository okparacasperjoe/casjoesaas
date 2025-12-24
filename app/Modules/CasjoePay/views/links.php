<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Links | Casjoe Pay</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .link-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
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
            <li class="nav-item"><a href="/pay/links" class="nav-link active"><ion-icon name="link-outline"></ion-icon> Payment Links</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Payment Links</h2>
        </div>

        <!-- Create Form -->
        <div class="card" style="margin-bottom: 30px;">
            <h3>Create New Link</h3>
            <form method="POST" action="/pay/links/create" style="display: flex; gap: 10px; align-items: flex-end;">
                <div style="flex: 2;">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="e.g. My E-Book" class="support-input" required>
                </div>
                <div style="flex: 1;">
                    <label>Amount (Opt)</label>
                    <input type="number" name="amount" placeholder="Fixed Amount" class="support-input">
                </div>
                <button type="submit" class="btn" style="height: 45px;">Create</button>
            </form>
        </div>

        <!-- List -->
        <h3>Your Links</h3>
        <?php foreach ($links as $link): ?>
            <div class="link-card">
                <div>
                    <div style="font-weight: bold; font-size: 1.1rem;"><?= htmlspecialchars($link['title']) ?></div>
                    <div style="font-size: 0.9rem; color: var(--secondary);">
                        <?= $link['amount'] ? '₦'.number_format($link['amount']) : 'Open Amount' ?>
                    </div>
                    <div style="font-size: 0.8rem; color: #aaa;">Views: <?= $link['views'] ?></div>
                </div>
                <div>
                    <a href="/pay/link/<?= $link['slug'] ?>" target="_blank" class="btn" style="background: transparent; border: 1px solid var(--secondary); color: var(--secondary);">View Page</a>
                    <button onclick="navigator.clipboard.writeText('<?= $_SERVER['HTTP_HOST'] ?>/pay/link/<?= $link['slug'] ?>')" class="btn">Copy</button>
                </div>
            </div>
        <?php endforeach; ?>

    </main>
</div>
</body>
</html>
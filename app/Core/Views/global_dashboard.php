<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .app-container { min-height: 100vh; display: flex; }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active"><ion-icon name="speedometer-outline"></ion-icon> Overview</a>
                </li>
                <li class="nav-item">
                    <a href="/billing" class="nav-link"><ion-icon name="card-outline"></ion-icon> Billing</a>
                </li>
                <li class="nav-item">
                    <a href="/security" class="nav-link"><ion-icon name="shield-checkmark-outline"></ion-icon> Security</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><ion-icon name="notifications-outline"></ion-icon> Notifications</a>
                </li>
                <li class="nav-item">
                    <a href="/support" class="nav-link"><ion-icon name="headset-outline"></ion-icon> Support</a>
                </li>
                <li class="nav-item">
                    <a href="/logout" class="nav-link"><ion-icon name="log-out-outline"></ion-icon> Logout</a>
                </li>
            </ul>

        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1 style="font-size: 1.5rem;">
                    <ion-icon name="person-circle-outline" style="vertical-align: middle;"></ion-icon>
                    Welcome, <?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Casjoe User') ?>
                </h1>
            </div>

            <div class="dashboard-grid" style="grid-template-columns: repeat(4, 1fr);">
                <?php foreach ($modules as $module): ?>
                    <a href="<?= htmlspecialchars($module['url']) ?>" class="card"
                        style="text-align: center; text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                        <div class="app-icon-box"
                            style="background: rgba(255,255,255,0.1); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <?php if (isset($module['image'])): ?>
                                <img src="<?= htmlspecialchars($module['image']) ?>"
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <ion-icon name="<?= htmlspecialchars($module['icon']) ?>"
                                    style="font-size: 2rem; color: <?= $module['color'] ?>;"></ion-icon>
                            <?php endif; ?>
                        </div>
                        <div style="font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;">
                            <?= htmlspecialchars($module['name']) ?>
                        </div>
                        <div class="app-status" style="opacity: 0.6; font-size: 0.8rem;">Connected</div>
                        <button class="btn" style="width: 100%; margin-top: 10px;">Open</button>
                    </a>
                <?php endforeach; ?>
            </div>



        </main>
    </div>
</body>

</html>
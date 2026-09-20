<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #ddd;
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .course-thumb {
            height: 160px;
            background: #eee;
            width: 100%;
            object-fit: cover;
        }
        .course-body {
            padding: 20px;
        }
        .course-cat {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--secondary);
            font-weight: bold;
            letter-spacing: 1px;
        }
        .course-title {
            margin: 5px 0 10px;
            font-size: 1.1rem;
            color: #333;
        }
        .course-desc {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .course-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        .seat-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
    <style>
        .main-content { margin-left: 270px; }
        @media (max-width: 992px) {
            .sidebar { left: -270px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
            .row { flex-direction: column; }
            .grid-container, .course-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'academy_market'; include __DIR__ . '/../partials/sidebar_academy.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1>Enterprise Training Catalog</h1>
            </div>

            <div class="catalog-grid">
                <?php foreach ($courses as $c): ?>
                    <div class="course-card">
                        <img src="<?= htmlspecialchars($c['thumbnail']) ?>" class="course-thumb" alt="">
                        <div class="course-body">
                            <div class="course-cat"><?= htmlspecialchars($c['category'] ?? 'General') ?></div>
                            <h3 class="course-title"><?= htmlspecialchars($c['title']) ?></h3>
                            <p class="course-desc"><?= htmlspecialchars($c['description']) ?></p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="course-price">$<?= number_format($c['price_per_seat'] ?? $c['price'] ?? 0, 0) ?> <span style="font-size: 12px; font-weight: normal; color: #999;">/ seat</span></span>
                            </div>

                            <form action="/academy/business/buy" method="POST" class="seat-selector">
                                <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                <input type="number" name="seats" value="1" min="1" max="100" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 5px;">
                                <button type="submit" class="btn" style="flex: 1; padding: 8px;">Buy Licenses</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Performance Reviews | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .star-rating { color: #f39c12; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Performance Reviews</h2>
            <a href="/erp/performance/create" class="btn"><ion-icon name="star-outline"></ion-icon> New Review</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th><th>Action</th>
                        <th>Employee</th>
                        <th>Rating</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $r): ?>
                    <tr>
                        <td><?= $r['review_date'] ?></td>
                        <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td>
                            <span class="star-rating">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <ion-icon name="<?= $i <= $r['rating'] ? 'star' : 'star-outline' ?>"></ion-icon>
                                <?php endfor; ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(substr($r['comments'], 0, 50)) ?>...</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

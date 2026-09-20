<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>New Performance Review | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>New Review</h2>
        </div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form action="/erp/performance/store" method="POST">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Review Date</label>
                    <input type="date" name="review_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label>Rating (1-5)</label>
                    <select name="rating" class="form-control" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Satisfactory</option>
                        <option value="2">2 - Needs Improvement</option>
                        <option value="1">1 - Unsatisfactory</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Comments</label>
                    <textarea name="comments" class="form-control" rows="5" placeholder="Detailed feedback..."></textarea>
                </div>

                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>

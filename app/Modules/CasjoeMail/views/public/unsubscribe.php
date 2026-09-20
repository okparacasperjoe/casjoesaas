<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .card { border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); width: 100%; max-width: 400px; padding: 40px; text-align: center; }
        .icon { font-size: 3rem; color: #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <?php if($success): ?>
            <div class="icon">✓</div>
            <h3>Unsubscribed</h3>
            <p class="text-muted">You have been successfully removed from this mailing list.</p>
        <?php else: ?>
            <div class="icon">?</div>
            <h3>Confirmation</h3>
            <p class="text-muted">Are you sure you want to unsubscribe from <strong><?= htmlspecialchars($listName) ?></strong>?</p>
            <form method="POST">
                <button type="submit" name="confirm" value="1" class="btn btn-danger w-100">Yes, Unsubscribe Me</button>
                <a href="/" class="btn btn-link mt-2">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


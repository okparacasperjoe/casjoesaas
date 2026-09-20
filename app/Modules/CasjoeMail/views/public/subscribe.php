<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .subscribe-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        .brand-logo { max-height: 40px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="subscribe-card text-center">
    <h2 class="h4 mb-3">Join our Newsletter</h2>
    <p class="text-muted mb-4 small">Stay updated with our latest news and offers.</p>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Thanks for subscribing!</div>
    <?php else: ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="/mail/subscribe/store" method="POST">
            <input type="hidden" name="list_id" value="<?= $listId ?>">
            
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
            </div>
            
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">First Name (Optional)</label>
                <input type="text" name="first_name" class="form-control" placeholder="John">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Subscribe Now</button>
        </form>
    <?php endif; ?>
    
    <div class="mt-4 text-muted small">
        &copy; <?= date('Y') ?> Powered by Casjoe Mail
    </div>
</div>

</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Create Plan | Casjoe Pay Admin</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../../../Views/layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <h1>Create Subscription Plan</h1>
            </div>

            <div class="card app-card-white" style="max-width: 600px;">
                <form action="/pay/plans/store" method="POST">
                    <div class="form-group">
                        <label>Plan Name</label>
                        <input type="text" name="name" required placeholder="e.g. Gold Tier" class="form-control">
                    </div>

                    <div class="form-group row" style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label>Price</label>
                            <input type="number" name="price" step="0.01" required placeholder="0.00" class="form-control">
                        </div>
                        <div style="flex: 1;">
                            <label>Billing Interval</label>
                            <select name="interval" class="form-control">
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Features (One per line)</label>
                        <textarea name="features" rows="5" class="form-control" placeholder="10 Users&#10;Unlimited Storage&#10;Priority Support"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Create Plan</button>
                        <a href="/pay/plans" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


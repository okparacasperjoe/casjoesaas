<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>System Settings</h2>
        </div>

        <div class="card" style="max-width: 600px;">
            <form method="POST" action="/erp/settings/update">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Timezone</label>
                    <select name="timezone" class="form-control">
                        <option value="UTC" <?= ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' ?>>UTC</option>
                        <option value="EST" <?= ($settings['timezone'] ?? '') == 'EST' ? 'selected' : '' ?>>EST</option>
                        <option value="PST" <?= ($settings['timezone'] ?? '') == 'PST' ? 'selected' : '' ?>>PST</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Save Settings</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

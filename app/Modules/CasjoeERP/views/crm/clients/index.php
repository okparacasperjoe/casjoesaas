<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Clients</h2>
            <a href="/erp/clients/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Client</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Company</th>
                        <th style="padding: 10px;">Contact</th>
                        <th style="padding: 10px;">Email</th>
                        <th style="padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($client['company_name']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($client['contact_person']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($client['email']) ?></td>
                            <td style="padding: 10px;">
                                <span style="background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                                    <?= ucfirst($client['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

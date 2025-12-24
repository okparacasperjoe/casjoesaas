<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Job Applications</h2>
            <a href="/erp/recruitment" class="btn btn-sm">Back to Jobs</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Candidate</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?= htmlspecialchars($app['title']) ?></td>
                        <td><?= htmlspecialchars($app['candidate_name']) ?></td>
                        <td><?= htmlspecialchars($app['email']) ?></td>
                        <td><?= ucfirst($app['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

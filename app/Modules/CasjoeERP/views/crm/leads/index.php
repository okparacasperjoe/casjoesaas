<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leads | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Leads</h2>
            <button onclick="document.getElementById('newLeadModal').showModal()" class="btn">Add Lead</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['name']) ?></td>
                        <td><?= htmlspecialchars($l['source']) ?></td>
                        <td><?= ucfirst($l['status']) ?></td>
                        <td><?= $l['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newLeadModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/crm/leads/store" method="POST">
            <h3>New Lead</h3>
            <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group">
                <label>Source</label>
                <select name="source" class="form-control">
                    <option value="Website">Website</option>
                    <option value="Referral">Referral</option>
                    <option value="Cold Call">Cold Call</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newLeadModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Opportunities | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Opportunities</h2>
            <button onclick="document.getElementById('newOppModal').showModal()" class="btn">New Opportunity</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Value</th>
                        <th>Stage</th>
                        <th>Related To</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $opp): ?>
                    <tr>
                        <td><?= htmlspecialchars($opp['title']) ?></td>
                        <td>$<?= number_format($opp['value'], 2) ?></td>
                        <td><?= ucfirst($opp['stage']) ?></td>
                        <td>
                            <?php if ($opp['customer_name']): ?>
                                Customer: <?= htmlspecialchars($opp['customer_name']) ?>
                            <?php elseif ($opp['lead_name']): ?>
                                Lead: <?= htmlspecialchars($opp['lead_name']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= $opp['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newOppModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/crm/opportunities/store" method="POST">
            <h3>New Opportunity</h3>
            <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label>Value ($)</label><input type="number" step="0.01" name="value" class="form-control"></div>
            
            <div class="form-group">
                <label>Link to Customer (Optional)</label>
                <select name="customer_id" class="form-control">
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Link to Lead (Optional)</label>
                <select name="lead_id" class="form-control">
                    <option value="">-- Select Lead --</option>
                    <?php foreach ($leads as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newOppModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>

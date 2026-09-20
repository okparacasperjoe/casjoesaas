<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
                        <th>Created</th><th>Actions</th>
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
    <dialog id="editOppModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/crm/opportunities/update" method="POST">
            <input type="hidden" name="id" id="edit_opp_id">
            <h3 style="color:#000066; margin-top:0;">Edit Opportunity</h3>
            <div class="form-group"><label>Title *</label><input type="text" name="title" id="edit_opp_title" class="form-control" required></div>
            <div class="form-group">
                <label>Related Lead</label>
                <select name="lead_id" id="edit_opp_lead_id" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach($leads as $ld): ?><option value="<?=$ld['id']?>"><?=$ld['name']?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Related Customer</label>
                <select name="customer_id" id="edit_opp_customer_id" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach($customers as $cu): ?><option value="<?=$cu['id']?>"><?=$cu['name']?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Amount / Value</label><input type="number" step="0.01" name="amount" id="edit_opp_amount" class="form-control" required></div>
            <div class="form-group">
                <label>Stage</label>
                <select name="stage" id="edit_opp_stage" class="form-control">
                    <option value="prospecting">Prospecting</option>
                    <option value="qualification">Qualification</option>
                    <option value="proposal">Proposal/Quote</option>
                    <option value="negotiation">Negotiation</option>
                    <option value="closed_won">Closed Won</option>
                    <option value="closed_lost">Closed Lost</option>
                </select>
            </div>
            <div class="form-group"><label>Expected Close Date</label><input type="date" name="close_date" id="edit_opp_close" class="form-control"></div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editOppModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update Opportunity</button>
            </div>
        </form>
    </dialog>
    <script>
    function editOpp(o) {
        document.getElementById('edit_opp_id').value = o.id;
        document.getElementById('edit_opp_title').value = o.title;
        document.getElementById('edit_opp_lead_id').value = o.lead_id || '';
        document.getElementById('edit_opp_customer_id').value = o.customer_id || '';
        document.getElementById('edit_opp_amount').value = o.amount;
        document.getElementById('edit_opp_stage').value = o.stage;
        document.getElementById('edit_opp_close').value = o.close_date || '';
        document.getElementById('editOppModal').showModal();
    }
    </script>
</body>
</html>

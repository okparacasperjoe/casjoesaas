<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
            <button onclick="document.getElementById('newLeadModal').showModal()" class="btn" style="background:#000066; color:#ffffff !important;"><ion-icon name="add-circle-outline" style="vertical-align:middle;"></ion-icon> Add Lead</button>
        </div>

        <div class="card" style="background: #ffffff !important; color: #1e293b; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
<style>.card table th { color: #000066 !important; font-weight: 700; border-bottom: 2px solid #e2e8f0 !important; } .card table td { color: #334155 !important; border-bottom: 1px solid #f1f5f9 !important; } .card table td small { color: #64748b !important; }</style>
            <table>
                <thead>
                    <tr>
                        <th>Name & Company</th>
                        <th>Contact</th>
                        <th>Source</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Created</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $l): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($l['name']) ?></strong><br>
                            <small style="color: #666;"><?= htmlspecialchars($l['company'] ?? 'No Company') ?></small>
                        </td>
                        <td>
                            <?php if (!empty($l['email'])): ?><a href="mailto:<?= htmlspecialchars($l['email']) ?>" style="color: #007bff; text-decoration: none;"><?= htmlspecialchars($l['email']) ?></a><br><?php endif; ?>
                            <?php if (!empty($l['phone'])): ?>
                                <small style="color: #555;"><?= htmlspecialchars($l['phone']) ?></small>
                                <?php 
                                    $whatsappNum = preg_replace('/[^0-9]/', '', $l['phone']);
                                    if(strlen($whatsappNum) > 6): 
                                ?>
                                <a href="https://wa.me/<?= $whatsappNum ?>" target="_blank" style="color: #25D366; margin-left: 5px; font-size: 1.1rem; vertical-align: middle;" title="Message on WhatsApp">
                                    <ion-icon name="logo-whatsapp"></ion-icon>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($l['source']) ?></td>
                        <td>
                            <?php
                                $score = (int)($l['ai_score'] ?? 0);
                                if ($score <= 20) { $c = '#94a3b8'; $lbl = 'Cold'; }
                                elseif ($score <= 40) { $c = '#f59e0b'; $lbl = 'Warm'; }
                                elseif ($score <= 60) { $c = '#f97316'; $lbl = 'Hot'; }
                                elseif ($score <= 80) { $c = '#ef4444'; $lbl = 'Very Hot'; }
                                else { $c = '#8b5cf6'; $lbl = 'On Fire'; }
                            ?>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span style="background:<?= $c ?>; color:#fff; padding:3px 8px; border-radius:50px; font-size:0.75rem; font-weight:700; min-width:28px; text-align:center;"><?= $score ?></span>
                                <small style="color:<?= $c ?>; font-weight:600;"><?= $lbl ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--brand-blue); color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                <?= ucfirst($l['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($l['created_at'])) ?></td>
                        <td>
                            <button onclick='editLead(<?= json_encode($l) ?>)' class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;">Edit</button>
                            <form action="/erp/crm/leads/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete this lead?');">
                                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                <button type="submit" class="btn btn-danger-outline" style="padding: 4px 8px; font-size: 0.8rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newLeadModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/crm/leads/store" method="POST">
            <h3 style="color:#000066; margin-top:0; font-size:1.3rem; margin-bottom:18px;">New Lead</h3>
            <div class="form-group"><label style="font-weight:600; color:#334155;">Name *</label><input type="text" name="name" class="form-control" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px;" required></div>
            <div class="form-group"><label style="font-weight:600; color:#334155;">Email Address</label><input type="email" name="email" class="form-control" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px;"></div>
            <div class="form-group"><label style="font-weight:600; color:#334155;">Phone Number</label><input type="text" name="phone" class="form-control" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px;"></div>
            <div class="form-group"><label style="font-weight:600; color:#334155;">Company</label><input type="text" name="company" class="form-control" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px;"></div>
            <div class="form-group"><label style="font-weight:600; color:#334155;">Notes</label><textarea name="notes" class="form-control" rows="3" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px;"></textarea></div>
            <div class="form-group">
                <label style="font-weight:600; color:#334155;">Source</label>
                <select name="source" class="form-control" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; height:40px;">
                    <option value="Website">Website</option>
                    <option value="Referral">Referral</option>
                    <option value="Cold Call">Cold Call</option>
                    <option value="Facebook">Facebook</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('newLeadModal').close()" class="btn" style="background: #f1f5f9; color:#334155 !important; border:1px solid #cbd5e1;">Cancel</button>
                <button type="submit" class="btn" style="background: #000066; color: #ffffff !important;">Save Lead</button>
            </div>
        </form>
    </dialog>
</div>
    <dialog id="editLeadModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/crm/leads/update" method="POST" id="editLeadForm">
            <input type="hidden" name="id" id="edit_lead_id">
            <h3 style="color:#000066; margin-top:0; font-size:1.3rem; margin-bottom:18px;">Edit Lead</h3>
            <div class="form-group"><label>Name *</label><input type="text" name="name" id="edit_lead_name" class="form-control" required></div>
            <div class="form-group"><label>Email Address</label><input type="email" name="email" id="edit_lead_email" class="form-control"></div>
            <div class="form-group"><label>Phone Number</label><input type="text" name="phone" id="edit_lead_phone" class="form-control"></div>
            <div class="form-group"><label>Company</label><input type="text" name="company" id="edit_lead_company" class="form-control"></div>
            <div class="form-group"><label>Notes</label><textarea name="notes" id="edit_lead_notes" class="form-control" rows="3"></textarea></div>
            <div class="form-group">
                <label>Source</label>
                <select name="source" id="edit_lead_source" class="form-control">
                    <option value="Website">Website</option>
                    <option value="Referral">Referral</option>
                    <option value="Cold Call">Cold Call</option>
                    <option value="Facebook">Facebook</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_lead_status" class="form-control">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editLeadModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update Lead</button>
            </div>
        </form>
    </dialog>
    <script>
    function editLead(lead) {
        document.getElementById('edit_lead_id').value = lead.id;
        document.getElementById('edit_lead_name').value = lead.name;
        document.getElementById('edit_lead_email').value = lead.email;
        document.getElementById('edit_lead_phone').value = lead.phone;
        document.getElementById('edit_lead_company').value = lead.company;
        document.getElementById('edit_lead_notes').value = lead.notes;
        document.getElementById('edit_lead_source').value = lead.source;
        document.getElementById('edit_lead_status').value = lead.status;
        document.getElementById('editLeadModal').showModal();
    }
    </script>
</body>
</html>

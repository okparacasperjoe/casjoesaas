<?php $title = "Form Responses"; include __DIR__ . '/layout/header.php'; ?>

<style>
    /* White Theme Overrides */
    body, .app-container { background-color: #ffffff !important; color: #333 !important; }
    h1, h2, h5, h6, .fw-bold, p { color: #333 !important; }
    
    /* Table Styles */
    .table { color: #333 !important; }
    .table thead th { 
        background-color: #000066 !important; /* Navy Blue */
        border-bottom: 3px solid #ffa600 !important; /* Orange Accent */
        color: #ffffff !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 15px;
    }
    .table td { 
        vertical-align: middle; 
        border-color: #dee2e6; 
        padding: 12px 15px;
        color: #333 !important; /* Force dark text */
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.02) !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(255, 166, 0, 0.05) !important;
    }
    
    /* Brand Accents */
    .text-brand { color: #000066 !important; }
    .btn-primary { 
        background-color: #ffa600 !important; 
        border-color: #ffa600 !important; 
        color: #000066 !important;
        font-weight: bold;
    }
</style>

<div class="container-fluid pt-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-brand small text-uppercase fw-bold ls-1">Data Management</div>
            <h2 class="fw-bold mb-0">Responses: <?= htmlspecialchars($form['title']) ?></h2>
        </div>
        <div>
            <a href="/smart-forms/stats/<?= $form['id'] ?>" class="btn btn-outline-secondary me-2">
                <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back to Stats
            </a>
            <a href="/smart-forms/export/<?= $form['id'] ?>" class="btn btn-primary">
                <ion-icon name="download-outline" style="vertical-align: middle;"></ion-icon> Export CSV
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Date</th>
                            <th>IP Address</th>
                            <?php 
                            $structure = json_decode($form['structure'], true);
                            $cols = [];
                            $fieldLabels = [];
                            foreach($structure as $field) {
                                if($field['type'] == 'content') continue;
                                $cols[] = $field['name'];
                                $fieldLabels[$field['name']] = $field['label'] ?: $field['name'];
                                echo "<th>" . htmlspecialchars($field['label'] ?: $field['name']) . "</th>";
                                if(count($cols) >= 4) break; // Limit columns for display
                            }
                            
                            // Also map remaining fields that were excluded from columns
                            foreach($structure as $field) {
                                if($field['type'] == 'content') continue;
                                if(!isset($fieldLabels[$field['name']])) {
                                    $fieldLabels[$field['name']] = $field['label'] ?: $field['name'];
                                }
                            }
                            ?>
                            <?php if(!empty(json_decode($form['settings'], true)['payment_enabled'])): ?>
                            <th>Payment</th>
                            <?php endif; ?>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($submissions)): ?>
                            <tr><td colspan="10" class="text-center p-5 text-muted">No submissions yet.</td></tr>
                        <?php else: ?>
                            <?php foreach($submissions as $index => $sub): 
                                $data = json_decode($sub['data'], true);
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('M d, H:i', strtotime($sub['created_at'])) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $sub['ip_address'] ?></span></td>
                                
                                <?php foreach($cols as $col): ?>
                                    <td>
                                        <?php 
                                            // Truncate long text or convert file paths to buttons
                                            $val = $data[$col] ?? '-';
                                            if (is_array($val)) {
                                                $htmlPieces = [];
                                                foreach($val as $v) {
                                                    if (is_string($v) && strpos($v, '/cloud/') === 0) {
                                                        $htmlPieces[] = '<a href="'.htmlspecialchars($v).'" target="_blank" class="badge bg-primary text-decoration-none p-1"><ion-icon name="document-text-outline" class="align-middle"></ion-icon> View</a>';
                                                    } elseif (is_string($v) && strpos($v, 'data:image/') === 0) {
                                                        $htmlPieces[] = '<img src="'.htmlspecialchars($v).'" style="max-height: 40px; background: #fff; border: 1px solid #ddd; border-radius: 4px;" alt="Signature">';
                                                    } else {
                                                        $htmlPieces[] = htmlspecialchars((string)$v);
                                                    }
                                                }
                                                echo implode(' ', $htmlPieces);
                                            } else {
                                                if (is_string($val) && strpos($val, '/cloud/') === 0) {
                                                    echo '<a href="'.htmlspecialchars($val).'" target="_blank" class="badge bg-primary text-decoration-none p-2 mb-1"><ion-icon name="document-text-outline" class="align-middle me-1"></ion-icon> View File</a>';
                                                } elseif (is_string($val) && strpos($val, 'data:image/') === 0) {
                                                    echo '<img src="'.htmlspecialchars($val).'" style="max-height: 40px; background: #fff; border: 1px solid #ddd; border-radius: 4px;" alt="Signature">';
                                                } else {
                                                    echo htmlspecialchars(mb_strimwidth((string)$val, 0, 30, "...")); 
                                                }
                                            }
                                        ?>
                                    </td>
                                <?php endforeach; ?>

                                <?php if(!empty(json_decode($form['settings'], true)['payment_enabled'])): ?>
                                <td>
                                    <?php if($sub['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Paid (<?= $sub['amount'] ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewDetails(<?= htmlspecialchars($sub['data']) ?>, <?= htmlspecialchars(json_encode($fieldLabels)) ?>)">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Submission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsBody">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    function viewDetails(data, labels) {
        let html = '<table class="table table-bordered">';
        for (const [key, value] of Object.entries(data)) {
            let displayLabel = labels[key] || key;
            let displayValue = value;
            if (typeof value === 'string' && value.startsWith('/cloud/')) {
                displayValue = `<a href="${value}" target="_blank" class="btn btn-sm btn-primary"><ion-icon name="document-text-outline" class="align-middle me-1"></ion-icon> View File</a>`;
            } else if (typeof value === 'string' && value.startsWith('data:image/')) {
                displayValue = `<img src="${value}" style="max-width: 100%; max-height: 150px; background: #fff; border: 1px solid #ddd; border-radius: 4px;" alt="Signature">`;
            } else if (Array.isArray(value)) {
                displayValue = value.map(v => {
                    if (typeof v === 'string' && v.startsWith('/cloud/')) {
                        return `<a href="${v}" target="_blank" class="btn btn-sm btn-primary mb-1"><ion-icon name="document-text-outline" class="align-middle me-1"></ion-icon> View File</a>`;
                    } else if (typeof v === 'string' && v.startsWith('data:image/')) {
                        return `<img src="${v}" style="max-width: 100%; max-height: 150px; background: #fff; border: 1px solid #ddd; border-radius: 4px;" alt="Signature">`;
                    }
                    return v;
                }).join('<br>');
            }
            html += `<tr><th class="bg-light" style="width: 30%">${displayLabel}</th><td>${displayValue}</td></tr>`;
        }
        html += '</table>';
        document.getElementById('detailsBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    }
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>

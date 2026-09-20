<?php $title = "Smart Forms"; include __DIR__ . '/layout/header.php'; ?>

<div class="container-fluid" style="padding-top: 20px;">
    <style>
        /* Specific Light Theme for Dashboard */
        body, .app-container { background-color: #ffffff !important; color: #333 !important; }
        h2, h5, .fw-bold, p { color: #333 !important; }
        .text-white { color: #333 !important; }
        .text-white-50 { color: #6c757d !important; }
        .text-muted { color: #6c757d !important; }
        
        /* Cards in Dashboard */
        .card { 
            background: #ffffff !important; 
            border: 1px solid rgba(0,0,0,0.1) !important; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            color: #333 !important;
        }
        .card-header {
            background: transparent !important;
            border-bottom: 1px solid rgba(0,0,0,0.1) !important;
            color: #333 !important;
        }
        
        /* Table */
        .table { color: #333 !important; }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.05) !important; color: #333 !important; }
        .table thead th { border-bottom: 1px solid #dee2e6 !important; color: #6c757d !important; }
        .table td { border-color: #dee2e6 !important; }

        /* Buttons */
        .btn-outline-light { color: #6c757d !important; border-color: #dee2e6 !important; }
        .btn-outline-light:hover { background: #f8f9fa !important; color: #333 !important; }
        
        /* Analytics Cards Specifics */
        .text-white-50.mb-0 { color: #6c757d !important; }
    </style>
    <!-- Welcome / Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1">Smart Forms</h2>
            <p class="text-white-50 mb-0">Create, manage and track your forms.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/smart-forms/templates" class="btn btn-lg d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: rgba(255,166,0,0.1); color: #FFA600; border: 1px solid #FFA600;">
                <ion-icon name="library-outline" style="font-size: 1.5rem;"></ion-icon>
                <span class="fw-bold">Templates</span>
            </a>
            <a href="/smart-forms/create" class="btn btn-lg d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #FFA600; color: #000066; border: none;">
                <ion-icon name="add-circle" style="font-size: 1.5rem;"></ion-icon>
                <span class="fw-bold">Create Form</span>
            </a>
        </div>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="background-color: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573;">
            <ion-icon name="checkmark-circle" style="font-size: 1.5rem; margin-right: 10px;"></ion-icon>
            <div>
                Form saved successfully! Share this link to start collecting responses: 
                <a href="/sf/<?= htmlspecialchars($_GET['saved']) ?>" target="_blank" class="fw-bold" style="color: #2ed573; text-decoration: underline;">
                    <?= $_SERVER['HTTP_HOST'] ?>/sf/<?= htmlspecialchars($_GET['saved']) ?>
                </a>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
        </div>
    <?php endif; ?>

    <!-- Analytics Cards -->
    <div class="row g-3 mb-5">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: rgba(0,0,102, 0.4); border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body d-flex align-items-center bg-transparent">
                    <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="background: rgba(255, 166, 0, 0.2); color: #FFA600; width: 64px; height: 64px;">
                        <ion-icon name="document-text-outline" style="font-size: 1.75rem;"></ion-icon>
                    </div>
                    <div>
                        <h6 class="text-white-50 mb-0">Total Forms</h6>
                        <h3 class="fw-bold mb-0 text-white"><?= $totalForms ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: rgba(0,0,102, 0.4); border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body d-flex align-items-center bg-transparent">
                    <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="background: rgba(46, 213, 115, 0.2); color: #2ed573; width: 64px; height: 64px;">
                        <ion-icon name="paper-plane-outline" style="font-size: 1.75rem;"></ion-icon>
                    </div>
                    <div>
                        <h6 class="text-white-50 mb-0">Total Submissions</h6>
                        <h3 class="fw-bold mb-0 text-white"><?= $totalSubmissions ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: rgba(0,0,102, 0.4); border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body d-flex align-items-center bg-transparent">
                    <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.1); color: #fff; width: 64px; height: 64px;">
                        <ion-icon name="eye-outline" style="font-size: 1.75rem;"></ion-icon>
                    </div>
                    <div>
                        <h6 class="text-white-50 mb-0">Total Views</h6>
                        <h3 class="fw-bold mb-0 text-white"><?= $totalViews ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms List -->
    <div class="card border-0 shadow-sm mt-5">
        <div class="card-header py-3">
            <h5 class="mb-0 fw-bold">Your Forms</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Form Title</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($forms)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <ion-icon name="file-tray-outline" style="font-size: 3rem; opacity: 0.5;"></ion-icon>
                                        <p class="mt-2 text-muted">No forms created yet.</p>
                                        <a href="/smart-forms/create" class="btn btn-sm btn-outline-primary mt-2">Create your first form</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($forms as $form): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded p-2 me-3" style="background: rgba(255, 166, 0, 0.1); color: #FFA600;">
                                            <ion-icon name="clipboard-outline"></ion-icon>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white"><?= htmlspecialchars($form['title']) ?></div>
                                            <div class="small text-white-50">ID: <?= $form['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <?php if(isset($_SESSION['user_id']) && $form['user_id'] == $_SESSION['user_id']): ?>
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   data-form-id="<?= $form['id'] ?>" 
                                                   <?= $form['status'] === 'active' ? 'checked' : '' ?>
                                                   style="cursor: pointer; width: 3em; height: 1.5em;">
                                        <?php else: ?>
                                            <input class="form-check-input" type="checkbox" disabled checked style="width: 3em; height: 1.5em; opacity: 0.5;">
                                        <?php endif; ?>
                                        <label class="form-check-label ms-2">
                                            <span class="badge <?= $form['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?> status-badge-<?= $form['id'] ?>">
                                                <?= $form['status'] === 'active' ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-white-50 small">
                                    <?= date('M d, Y', strtotime($form['created_at'])) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="/sf/<?= $form['id'] ?>" target="_blank" class="btn btn-sm btn-outline-light" title="View Public Link">
                                            <ion-icon name="open-outline"></ion-icon>
                                        </a>

                                        <?php if(isset($_SESSION['user_id']) && $form['user_id'] == $_SESSION['user_id']): ?>
                                            <a href="/smart-forms/stats/<?= $form['id'] ?>" class="btn btn-sm btn-outline-light" title="Analytics">
                                                <ion-icon name="bar-chart-outline"></ion-icon>
                                            </a>
                                            <a href="/smart-forms/edit/<?= $form['id'] ?>" class="btn btn-sm" style="background:#FFA600; color:#000;" title="Edit">
                                                <ion-icon name="create-outline"></ion-icon>
                                            </a>
                                            <form action="/smart-forms/delete/<?= $form['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this form?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <ion-icon name="trash-outline"></ion-icon>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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

<script>
// Handle form status toggle
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const formId = this.dataset.formId;
        const newStatus = this.checked ? 'active' : 'inactive';
        const badge = document.querySelector(`.status-badge-${formId}`);
        
        fetch(`/smart-forms/toggle/${formId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                badge.textContent = newStatus === 'active' ? 'Active' : 'Inactive';
                badge.className = `badge ${newStatus === 'active' ? 'bg-success' : 'bg-secondary'} status-badge-${formId}`;
            } else {
                this.checked = !this.checked;
                alert('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.checked = !this.checked;
            alert('An error occurred');
        });
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>

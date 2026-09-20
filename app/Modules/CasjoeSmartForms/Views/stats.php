<?php $title = "Form Analytics"; include __DIR__ . '/layout/header.php'; ?>

<style>
    /* White Theme Overrides */
    body, .app-container { background-color: #ffffff !important; color: #333 !important; }
    h1, h2, h5, h6, .fw-bold, p { color: #333 !important; }
    .text-muted { color: #6c757d !important; }
    
    /* Card Styles */
    .card { 
        background: #ffffff !important; 
        border: 1px solid rgba(0,0,0,0.1) !important; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        color: #333 !important;
    }
    .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        color: #333 !important;
    }
    
    /* Brand Accents */
    .text-brand { color: #ffa600 !important; }
    .btn-primary { 
        background-color: #ffa600 !important; 
        border-color: #ffa600 !important; 
        color: #000 !important;
        font-weight: bold;
    }
    .btn-outline-primary {
        color: #ffa600 !important;
        border-color: #ffa600 !important;
    }
    .btn-outline-primary:hover {
        background-color: #ffa600 !important;
        color: #000 !important;
    }
</style>

<div class="container-fluid pt-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-brand small text-uppercase fw-bold ls-1">Analytics Dashboard</div>
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($form['title']) ?></h2>
        </div>
        <div>
            <a href="/sf/<?= $form['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm me-2"><ion-icon name="open-outline" style="vertical-align: middle;"></ion-icon> View Form</a>
            <a href="/smart-forms/edit/<?= $form['id'] ?>" class="btn btn-primary btn-sm"><ion-icon name="create-outline" style="vertical-align: middle;"></ion-icon> Edit Form</a>
        </div>
    </div>

    <!-- 1. Top KPI Strip -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Views -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Views</h6>
                    <h2 class="fw-bold mb-0 display-6"><?= number_format($kpi['views']) ?></h2>
                    <div class="small text-muted mt-2">How many people opened it</div>
                </div>
            </div>
        </div>
        <!-- Card 2: Form Starts -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Form Starts</h6>
                    <h2 class="fw-bold mb-0 display-6"><?= number_format($kpi['starts']) ?></h2>
                    <div class="small mt-2 <?= $funnel['starts']['drop_off'] > 50 ? 'text-danger' : 'text-success' ?>">
                        <ion-icon name="<?= $funnel['starts']['drop_off'] > 50 ? 'arrow-down' : 'arrow-forward' ?>"></ion-icon> 
                        <?= 100 - $funnel['starts']['drop_off'] ?>% Conversion
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3: Completed Submissions -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Submissions</h6>
                    <h2 class="fw-bold mb-0 display-6"><?= number_format($kpi['submissions']) ?></h2>
                    <div class="small mt-2 <?= $funnel['submitted']['drop_off'] > 50 ? 'text-danger' : 'text-success' ?>">
                        <ion-icon name="<?= $funnel['submitted']['drop_off'] > 50 ? 'arrow-down' : 'arrow-forward' ?>"></ion-icon>
                         <?= 100 - $funnel['submitted']['drop_off'] ?>% Conversion
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4: Payments (Conditional) -->
        <?php if (!empty($settings['payment_enabled'])): ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Payments</h6>
                    <h2 class="fw-bold mb-0 display-6"><?= number_format($kpi['payments']) ?></h2>
                    <div class="small mt-2 <?= $funnel['paid']['drop_off'] > 50 ? 'text-danger' : 'text-success' ?>">
                         <ion-icon name="cash-outline"></ion-icon> Paid Users
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <!-- 2. Conversion Funnel Snapshot -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="fw-bold mb-0">Conversion Funnel</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 100px;">Views</div>
                        <div class="flex-grow-1 mx-3">
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-primary" style="width: 100%"><?= $views ?></div>
                            </div>
                        </div>
                        <div class="small text-muted">100%</div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 100px;">Started</div>
                        <div class="flex-grow-1 mx-3">
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-info" style="width: <?= $views > 0 ? ($starts/$views)*100 : 0 ?>%"><?= $starts ?></div>
                            </div>
                        </div>
                        <div class="small text-danger"><?= $funnel['starts']['drop_off'] ?>% Drop</div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 100px;">Submitted</div>
                        <div class="flex-grow-1 mx-3">
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-success" style="width: <?= $starts > 0 ? ($submissions/$starts)*100 : 0 ?>%"><?= $submissions ?></div>
                            </div>
                        </div>
                        <div class="small text-danger"><?= $funnel['submitted']['drop_off'] ?>% Drop</div>
                    </div>

                    <?php if (!empty($settings['payment_enabled'])): ?>
                    <div class="d-flex align-items-center">
                        <div style="width: 100px;">Paid</div>
                        <div class="flex-grow-1 mx-3">
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-warning" style="color: black; width: <?= $submissions > 0 ? ($payments/$submissions)*100 : 0 ?>%"><?= $payments ?></div>
                            </div>
                        </div>
                        <div class="small text-danger"><?= $funnel['paid']['drop_off'] ?>% Drop</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column Group -->
        <div class="col-md-6">
            <!-- 3. Biggest Problem Alert -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-start">
                    <ion-icon name="alert-circle" class="text-warning me-3" style="font-size: 2rem;"></ion-icon>
                    <div>
                        <h6 class="fw-bold mb-1">Insight</h6>
                        <p class="mb-0 text-muted"><?= $alert ?></p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header py-3">
                    <h6 class="fw-bold mb-0">Traffic Quality</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h4 class="fw-bold mb-0"><?= $devices['mobile'] ?>%</h4>
                            <div class="small text-muted">Mobile</div>
                        </div>
                        <div class="col-6">
                            <h4 class="fw-bold mb-0"><?= $devices['desktop'] ?>%</h4>
                            <div class="small text-muted">Desktop</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Field Interaction Drop-offs -->
            <?php if (!empty($fieldInteractions)): ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header py-3">
                    <h6 class="fw-bold mb-0">Field Engagement (Drop-off points)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Field</th>
                                <th class="text-end">Interactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($fieldInteractions as $fi): ?>
                            <tr>
                                <td><?= htmlspecialchars($fi['field_name'] ?? 'Unknown') ?></td>
                                <td class="text-end fw-bold"><?= number_format($fi['count']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="row g-4">
        <!-- 5. Revenue Snapshot (If Payment On) -->
        <?php if (!empty($settings['payment_enabled'])): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success me-4">
                        <ion-icon name="wallet-outline" style="font-size: 2.5rem;"></ion-icon>
                    </div>
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold">Total Revenue Generated</h6>
                        <h2 class="fw-bold mb-0">₦<?= number_format($revenue, 2) ?></h2>
                        <div class="small text-muted mt-1">
                            <?= $submissions > 0 ? round(($payments/$submissions)*100) : 0 ?>% Payment Conversion Rate
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 6. Recent Activity -->
        <div class="col-md-<?= !empty($settings['payment_enabled']) ? '6' : '12' ?>">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="fw-bold mb-0">Recent Activity</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if(empty($activity)): ?>
                            <div class="text-center p-4 text-muted">No activity yet.</div>
                        <?php else: ?>
                            <?php foreach($activity as $act): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if($act['type'] == 'submission'): ?>
                                            <span class="badge bg-success me-2">Submission</span>
                                        <?php else: ?>
                                            <span class="badge bg-info me-2">View</span>
                                        <?php endif; ?>
                                        <span class="small text-muted"><?= htmlspecialchars(substr($act['details'], 0, 50)) ?>...</span>
                                    </div>
                                    <small class="text-muted"><?= date('H:i', strtotime($act['created_at'])) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Clear Action Buttons -->
    <div class="text-end mt-4 mb-5">
        <a href="/smart-forms/edit/<?= $form['id'] ?>" class="btn btn-primary px-4 me-2">Edit Form</a>
        <a href="/smart-forms/responses/<?= $form['id'] ?>" class="btn btn-outline-dark px-4 me-2">View Responses</a>
        <a href="/smart-forms/export/<?= $form['id'] ?>" class="btn btn-outline-success px-4" target="_blank">
            <ion-icon name="download-outline" style="vertical-align: middle;"></ion-icon> Export Excel
        </a>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>


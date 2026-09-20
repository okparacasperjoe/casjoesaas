<?php $title = "Template Library"; include __DIR__ . '/layout/header.php'; ?>

<div class="container-fluid pt-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #333;">Template Library</h2>
            <p class="text-muted mb-0">Choose a pre-built template to get started instantly.</p>
        </div>
        <div>
            <a href="/smart-forms" class="btn btn-outline-secondary">
                <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back to Forms
            </a>
            <a href="/smart-forms/create" class="btn btn-primary shadow-sm ms-2" style="background-color: #FFA600; border:none; color: #000066; font-weight:bold;">
                Start from Scratch
            </a>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($templates as $t): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow:hidden; transition: transform 0.2s;">
                    <div style="background: linear-gradient(135deg, rgba(0,0,102,0.8), rgba(255,166,0,0.8)); height: 120px; display:flex; align-items:center; justify-content:center;">
                        <ion-icon name="document-text" style="font-size: 48px; color: white; opacity:0.8;"></ion-icon>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($t['title']) ?></h4>
                        <p class="text-muted mb-4 flex-grow-1" style="font-size: 15px;"><?= htmlspecialchars($t['description']) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <span class="badge bg-light text-dark border"><?= count($t['structure']) ?> fields</span>
                            <a href="/smart-forms/templates/use/<?= htmlspecialchars($t['id']) ?>" class="btn btn-sm btn-dark d-inline-flex align-items-center gap-2">
                                <ion-icon name="color-wand-outline"></ion-icon> Use Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Specific styling for the template cards */
    body, .app-container { background-color: #ffffff !important; color: #333 !important; }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include __DIR__ . '/layout/footer.php'; ?>

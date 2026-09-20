<?php 
$active_app = 'finance';
$active_sub = 'goals';
$title = "Business Goals";
require __DIR__ . '/../layout/header.php'; 
?>


    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Business Goals</h1>
            <p class="text-muted">Track your financial and operational targets.</p>
        </div>
        <a href="/erp/goals/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create New Goal
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissiblefade show" role="alert">
            <?php 
                if($_GET['success'] == 'created') echo "Goal created successfully!";
                if($_GET['success'] == 'updated') echo "Goal updated successfully!";
                if($_GET['success'] == 'deleted') echo "Goal deleted successfully!";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach($goals as $goal): 
            $target = $goal['target_value'] ?? 0;
            $current = $goal['current_value'] ?? 0;
            $percent = $target > 0 ? min(100, ($current / $target) * 100) : 0;
            
            $statusColor = 'primary';
            if($goal['status'] == 'achieved') $statusColor = 'success';
            if($goal['status'] == 'failed') $statusColor = 'danger';
            if($goal['status'] == 'archived') $statusColor = 'secondary';
        ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0 fw-bold"><?= htmlspecialchars($goal['title']) ?></h5>
                        <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($goal['status']) ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <h2 class="display-6 fw-bold mb-0">
                            <?php if($goal['unit'] == 'NGN' || $goal['unit'] == 'USD'): ?>
                                <?= $goal['unit'] == 'NGN' ? '₦' : '$' ?><?= number_format($current) ?>
                            <?php else: ?>
                                <?= number_format($current) ?> <span class="fs-6 text-muted"><?= $goal['unit'] ?></span>
                            <?php endif; ?>
                        </h2>
                        <div class="text-muted small">Target: 
                            <?php if($goal['unit'] == 'NGN' || $goal['unit'] == 'USD'): ?>
                                <?= $goal['unit'] == 'NGN' ? '₦' : '$' ?><?= number_format($target) ?>
                            <?php else: ?>
                                <?= number_format($target) ?> <?= $goal['unit'] ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-<?= $statusColor ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-calendar"></i> Due: <?= $goal['deadline'] ? date('M d, Y', strtotime($goal['deadline'])) : 'No deadline' ?></span>
                        <a href="/erp/goals/edit?id=<?= $goal['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($goals)): ?>
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-flag display-1 text-light"></i>
                    <p class="mt-3">No goals set yet.</p>
                    <a href="/erp/goals/create" class="btn btn-outline-primary">Set Your First Goal</a>
                </div>
            </div>
        <?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>

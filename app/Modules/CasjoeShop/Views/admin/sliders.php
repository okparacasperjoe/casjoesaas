<?php
$title = "Manage Shop Sliders";
include __DIR__ . '/../../../../Core/Views/admin/header.php';

$csrfToken = \App\Core\Services\CsrfService::getToken();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Storefront Sliders</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSliderModal">
        <ion-icon name="add-outline"></ion-icon> Add New Slider
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Slider saved successfully!</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-danger">Slider deleted successfully!</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Image</th>
                        <th>Title / Subtitle</th>
                        <th>Badge</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sliders as $slide): ?>
                        <tr>
                            <td><?= $slide['sort_order'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($slide['image_url'] ?? '') ?>" alt="Slider" style="height:50px; border-radius:4px;">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($slide['title']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($slide['subtitle'] ?? '') ?></small>
                            </td>
                            <td>
                                <?php if($slide['badge_text']): ?>
                                    <span class="badge bg-<?= $slide['badge_color'] ?>"><?= htmlspecialchars($slide['badge_text']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $slide['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
                            </td>
                            <td>
                                <form action="/<?= ADMIN_PATH ?>/shop/sliders/delete/<?= $slide['id'] ?>" method="POST" onsubmit="return confirm('Delete this slider?');" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <button class="btn btn-sm btn-danger"><ion-icon name="trash-outline"></ion-icon> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sliders)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No sliders configured yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Slider Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="/<?= ADMIN_PATH ?>/shop/sliders/store" method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="modal-header">
                <h5 class="modal-title">Add New Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title (H1)</label>
                        <input type="text" name="title" class="form-control" required placeholder="Summer Super Deals">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Get up to 50% off...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Badge Text (Optional)</label>
                        <input type="text" name="badge_text" class="form-control" placeholder="🔥 Flash Sale">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Badge Color</label>
                        <select name="badge_color" class="form-select">
                            <option value="primary">Primary</option>
                            <option value="danger">Danger (Red)</option>
                            <option value="success">Success (Green)</option>
                            <option value="warning">Warning (Yellow)</option>
                            <option value="info">Info (Blue)</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Button Text</label>
                        <input type="text" name="button_text" class="form-control" placeholder="Shop Now">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Button Link</label>
                        <input type="text" name="button_link" class="form-control" placeholder="/shop/category/new">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image Upload</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Or Image URL</label>
                        <input type="text" name="image_url" class="form-control" placeholder="/assets/images/slider1.png">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label" for="isActive">Set as Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Slider</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../../Core/Views/admin/footer.php'; ?>

<?php
// Admin Context Email Templates List
$active = 'email-templates';
$pageTitle = "Email Templates";
require __DIR__ . '/../header.php';
?>
<style>
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .category-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
    .cat-tab { padding: 6px 18px; border-radius: 20px; cursor: pointer; border: 1px solid rgba(255, 166, 0, 0.3); background: #13141f; font-size: 0.85rem; font-weight: 600; color: #f1f5f9; transition: all 0.2s; }
    .cat-tab:hover, .cat-tab.active { background: #FFA600; color: #090a0f; border-color: #FFA600; }
    .templates-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
    .template-card { background: #13141f; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255, 166, 0, 0.2); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4); transition: box-shadow 0.2s, transform 0.2s; }
    .template-card:hover { box-shadow: 0 8px 30px rgba(255, 166, 0, 0.2); transform: translateY(-3px); border-color: #FFA600; }
    .template-thumb { width: 100%; padding-top: 65%; position: relative; overflow: hidden; background: #0c0d14; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .template-thumb iframe { position: absolute; top: 0; left: 0; width: 400%; height: 400%; border: 0; pointer-events: none; transform: scale(0.25); transform-origin: 0 0; background: #ffffff; }
    .template-meta { padding: 14px; }
    .template-meta h3 { margin: 0 0 4px; font-size: 0.95rem; color: #ffffff; font-weight: 600; }
    .template-actions { display: flex; gap: 8px; align-items: center; margin-top: 12px; }
    .template-actions a, .template-actions button { font-size: 0.8rem; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; border: none; transition: all 0.15s; font-weight: 600; }
    .btn-edit { background: rgba(255, 255, 255, 0.08); color: #f1f5f9; border: 1px solid rgba(255, 255, 255, 0.1) !important; }
    .btn-edit:hover { background: rgba(255, 166, 0, 0.2); color: #FFA600; border-color: #FFA600 !important; }
    .btn-del { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .btn-del:hover { background: rgba(239, 68, 68, 0.2); }
    .badge-global { background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 10px; padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge-private { background: rgba(255, 255, 255, 0.1); color: #94a3b8; font-size: 10px; padding: 2px 6px; border-radius: 4px; }
</style>

<div class="content-wrapper" style="padding: 20px;">
    <?php if (!empty($_GET['message'])): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            ✅ <?= htmlspecialchars($_GET['message']) ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <h2>Email Templates</h2>
        <a href="/<?= ADMIN_PATH ?>/email-templates/create" class="btn btn-primary">+ Create Template</a>
    </div>

    <!-- Tabs -->
    <div class="category-tabs" id="catTabs">
        <div class="cat-tab active" onclick="filterCat('All', this)">All</div>
        <?php foreach (array_keys($grouped ?? []) as $c): ?>
            <div class="cat-tab" onclick="filterCat('<?= htmlspecialchars($c) ?>', this)"><?= htmlspecialchars($c) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="templates-grid" id="templatesGrid">
        <?php if(empty($templates)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: #777; padding: 40px;">No templates found.</p>
        <?php endif; ?>

        <?php foreach ($templates as $t): ?>
            <div class="template-card" data-cat="<?= htmlspecialchars($t['category'] ?: 'Uncategorised') ?>">
                <div class="template-thumb">
                    <iframe srcdoc="<?= htmlspecialchars($t['content']) ?>"></iframe>
                </div>
                <div class="template-meta">
                    <h3><?= htmlspecialchars($t['name']) ?></h3>
                    <div style="margin-bottom: 5px;">
                        <?php if ($t['tenant_id'] === null): ?>
                            <span class="badge-global">Global System</span>
                        <?php else: ?>
                            <span class="badge-private">Admin Private</span>
                        <?php endif; ?>
                    </div>
                    <div class="template-actions">
                        <a href="/<?= ADMIN_PATH ?>/email-templates/edit/<?= $t['id'] ?>" class="btn-edit">Edit</a>
                        <a href="/<?= ADMIN_PATH ?>/email-templates/delete/<?= $t['id'] ?>" class="btn-del" onclick="return confirm('Delete this template?');">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function filterCat(cat, el) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    
    document.querySelectorAll('.template-card').forEach(card => {
        if (cat === 'All' || card.dataset.cat === cat) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
<?php require __DIR__ . '/../footer.php'; ?>

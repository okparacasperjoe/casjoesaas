<?php
$pageTitle = 'Edit Page';
require __DIR__ . '/../header.php';
use App\Core\Services\CsrfService;
?>

<div class="top-bar" style="margin-bottom: 30px;">
    <h2 style="color: #FFA600; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="document-text-outline" style="font-size: 1.8rem; color: #FFA600;"></ion-icon> Edit Page: <?= htmlspecialchars($page['title']) ?>
    </h2>
    <a href="/<?= ADMIN_PATH ?>/cms" class="btn-primary" style="background: rgba(255, 255, 255, 0.08); color: #f1f5f9; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.1); display: inline-flex; align-items: center; gap: 6px;">
        <ion-icon name="arrow-back-outline"></ion-icon> Back to CMS
    </a>
</div>

<div class="settings-container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <div class="card" style="background:#13141f; padding: 30px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.4); border:1px solid rgba(255, 166, 0, 0.2);">
        <form action="/<?= ADMIN_PATH ?>/cms/page/update/<?= $page['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= CsrfService::generateToken() ?>">
            
            <div class="form-group mb-4" style="margin-bottom: 24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#FFA600; font-size: 0.95rem;">Page Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required style="width:100%; padding:12px 16px; border:1px solid rgba(255, 255, 255, 0.12); border-radius:8px; color:#ffffff; background:#191b2a; font-size: 0.95rem;">
            </div>

            <div class="form-group mb-4" style="margin-bottom: 24px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#94a3b8; font-size: 0.95rem;">Slug URL (Read Only)</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($page['slug']) ?>" disabled style="width:100%; padding:12px 16px; border:1px solid rgba(255, 255, 255, 0.06); border-radius:8px; color:#64748b; background:#0f1017; font-family: monospace; font-size: 0.95rem;">
            </div>

            <div class="form-group mb-4" style="margin-bottom: 30px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#FFA600; font-size: 0.95rem;">Content (HTML / Rich Text)</label>
                <!-- Quill Editor Container -->
                <div id="quill-editor" style="height: 420px; background: #191b2a; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px;"></div>
                <input type="hidden" name="content" id="hiddenContent">
                <style>
                    /* Ensure Quill dark mode text is visible and styled properly */
                    .ql-editor { color: #ffffff !important; min-height: 250px; font-size: 1rem; line-height: 1.6; }
                    .ql-toolbar.ql-snow { background: #0f1017; border: 1px solid rgba(255, 255, 255, 0.12); border-top-left-radius: 8px; border-top-right-radius: 8px; border-bottom: none; }
                    .ql-container.ql-snow { border: 1px solid rgba(255, 255, 255, 0.12); border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
                    .ql-snow .ql-stroke { stroke: #e2e8f0 !important; }
                    .ql-snow .ql-fill { fill: #e2e8f0 !important; }
                    .ql-snow .ql-picker { color: #e2e8f0 !important; }
                    .ql-snow .ql-picker-options { background-color: #191b2a !important; border-color: rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; }
                </style>
            </div>

            <button type="submit" class="btn-primary" style="background-color:#FFA600; color:#090a0f; border:none; padding:12px 28px; border-radius:8px; font-weight:700; font-size:0.95rem; cursor:pointer; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.25); display: inline-flex; align-items: center; gap: 8px;">
                <ion-icon name="save-outline" style="font-size: 1.2rem;"></ion-icon> Save Changes
            </button>
        </form>
    </div>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    }
});

// Load existing content
quill.root.innerHTML = <?= json_encode($page['content']) ?>;

// Sync content to hidden input on form submit
document.querySelector('form').onsubmit = function() {
    var content = document.querySelector('#hiddenContent');
    content.value = quill.root.innerHTML;
    // Check if empty
    if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
         content.value = '';
    }
};
</script>

<?php require __DIR__ . '/../footer.php'; ?>
</body>
</html>

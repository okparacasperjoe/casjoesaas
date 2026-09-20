<?php
// Admin Context Email Templates - Create
$active = 'email-templates';
$pageTitle = "Create Global Template";
$sectors = [
    '📢 Promotions & Offers', '📰 Newsletter', '🛒 eCommerce / Order Updates',
    '🏦 Finance / Fintech', '🎉 Events & Invitations', '🤝 Onboarding / Welcome',
    '🔔 Transactional / Alerts', '📊 Reports & Summaries'
];
require __DIR__ . '/../header.php';
?>
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .editor-wrap { display: grid; grid-template-columns: 280px 1fr; gap: 20px; height: calc(100vh - 150px); }
    .settings-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; overflow-y: auto; color: #1e293b; }
    .settings-panel label { color: #1e293b !important; }
    .settings-panel input, .settings-panel select, .settings-panel datalist { color: #1e293b !important; background-color: #ffffff !important; }
    .editor-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden; }
    .editor-toolbar-extra { display: flex; gap: 8px; padding: 10px 14px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .editor-toolbar-extra button { padding: 5px 12px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.82rem; display: flex; align-items: center; gap: 5px; }
    .editor-toolbar-extra button:hover { background: #f0f4ff; border-color: #000066; color: #000066; }
    .ql-editor { color: black !important; min-height: 200px; font-size: 16px; font-family: 'Inter', sans-serif; }
    /* Import Modal */
    .import-modal { display: none; position: fixed; inset: 0; z-index: 1050; }
    .import-modal.open { display: flex; align-items: center; justify-content: center; }
    .import-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .import-box { position: relative; background: white; border-radius: 14px; padding: 28px; width: 90%; max-width: 700px; z-index: 1; }
    .import-box textarea { width: 100%; height: 260px; padding: 12px; font-family: monospace; font-size: 0.82rem; border: 1px solid #ccc; border-radius: 8px; }
</style>

<div class="content-wrapper" style="padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
        <h2 style="margin:0;"><?= $pageTitle ?></h2>
        <div style="display:flex;gap:10px;">
            <a href="/<?= ADMIN_PATH ?>/email-templates" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">&laquo; Back</a>
            <button class="btn btn-primary btn-sm" onclick="submitForm()" style="padding: 6px 12px;">💾 Save Template</button>
        </div>
    </div>

    <form id="templateForm" action="/<?= ADMIN_PATH ?>/email-templates/store" method="POST" onsubmit="syncContent()">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
        
        <div class="editor-wrap">
            <div class="settings-panel">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block;margin-bottom:5px;"><strong>Template Name</strong></label>
                    <input type="text" name="name" class="form-control" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block;margin-bottom:5px;"><strong>Subject Line</strong></label>
                    <input type="text" name="subject" class="form-control" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block;margin-bottom:5px;"><strong>Category</strong></label>
                    <input type="text" name="category" list="category-list" class="form-control" placeholder="Select or type a category..." style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                    <datalist id="category-list">
                        <?php foreach ($sectors as $sector): ?>
                            <option value="<?= htmlspecialchars($sector) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <label style="display:flex; align-items:center; gap: 8px; cursor: pointer; margin: 0; font-weight: bold; color: #0f172a;">
                        <input type="checkbox" name="is_global" value="1" checked style="width:18px;height:18px;">
                        Global System Template
                    </label>
                    <p style="margin: 6px 0 0; font-size: 0.75rem; color: #64748b; line-height: 1.4;">
                        If checked, this template will be available to ALL users/tenants in their marketing dashboard. If unchecked, it will only be visible to Master Admins.
                    </p>
                </div>

                <input type="hidden" name="content" id="hiddenContent">
            </div>

            <div class="editor-panel">
                <div class="editor-toolbar-extra">
                    <button type="button" onclick="openImportModal()">📥 Import HTML</button>
                </div>
                <div id="editor" style="flex:1;"></div>
            </div>
        </div>
    </form>
</div>

<!-- Import Modal -->
<div class="import-modal" id="importModal">
    <div class="import-backdrop" onclick="closeImportModal()"></div>
    <div class="import-box">
        <h3 style="margin-top:0;">Import Raw HTML</h3>
        <p>Paste your HTML code below. This will overwrite the current editor content.</p>
        <textarea id="importHtmlCode"></textarea>
        <div style="text-align: right; margin-top: 15px;">
            <button class="btn btn-secondary" style="padding:6px 12px;margin-right:10px;" onclick="closeImportModal()">Cancel</button>
            <button class="btn btn-primary" style="padding:6px 12px;" onclick="applyImportHtml()">Apply HTML</button>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'super' }, { 'script': 'sub' }],
                [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                ['direction', { 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    function syncContent() {
        var html = document.querySelector('.ql-editor').innerHTML;
        document.getElementById('hiddenContent').value = html;
    }

    function submitForm() {
        syncContent();
        document.getElementById('templateForm').submit();
    }

    function openImportModal() {
        document.getElementById('importHtmlCode').value = document.querySelector('.ql-editor').innerHTML;
        document.getElementById('importModal').classList.add('open');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.remove('open');
    }

    function applyImportHtml() {
        var html = document.getElementById('importHtmlCode').value;
        const clipboard = quill.getModule('clipboard');
        clipboard.dangerouslyPasteHTML(html);
        closeImportModal();
    }
</script>
<?php require __DIR__ . '/../footer.php'; ?>

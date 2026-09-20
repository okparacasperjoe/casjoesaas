<?php
// Admin Context Email Campaigns - Edit
$active = 'email-campaigns';
$pageTitle = "Edit Campaign";
require __DIR__ . '/../header.php';
?>
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .editor-wrap { display: grid; grid-template-columns: 320px 1fr; gap: 20px; height: calc(100vh - 150px); }
    .settings-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; overflow-y: auto; color: #1e293b; }
    .settings-panel label { color: #1e293b !important; }
    .settings-panel input, .settings-panel select, .settings-panel datalist { color: #1e293b !important; background-color: #ffffff !important; }
    .editor-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden; }
    .ql-editor { color: black !important; min-height: 200px; font-size: 16px; font-family: 'Inter', sans-serif; }
    .template-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
    .template-card:hover { border-color: #000066; background: #f8fafc; }
    .btn-send { background: #10b981; color: white; }
    .btn-send:hover { background: #059669; }
</style>

<div class="content-wrapper" style="padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
        <h2 style="margin:0;"><?= $pageTitle ?>: <?= htmlspecialchars($campaign['name']) ?></h2>
        <div style="display:flex;gap:10px;">
            <a href="/<?= ADMIN_PATH ?>/email-campaigns" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">&laquo; Back</a>
            <button class="btn btn-primary btn-sm" onclick="submitForm()" style="padding: 6px 12px;">💾 Update Draft</button>
            <button class="btn btn-send btn-sm" onclick="alert('Sending campaigns directly from here will be integrated with the system broadcast engine.')" style="padding: 6px 12px;">🚀 Send Now</button>
        </div>
    </div>

    <form id="campaignForm" action="/<?= ADMIN_PATH ?>/email-campaigns/update/<?= $campaign['id'] ?>" method="POST" onsubmit="syncContent()">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
        
        <div class="editor-wrap">
            <div class="settings-panel">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block;margin-bottom:5px;"><strong>Campaign Name</strong></label>
                    <input type="text" name="name" class="form-control" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" value="<?= htmlspecialchars($campaign['name']) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block;margin-bottom:5px;"><strong>Subject Line</strong></label>
                    <input type="text" name="subject" class="form-control" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" value="<?= htmlspecialchars($campaign['subject']) ?>">
                </div>
                
                <div style="padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px; font-size: 0.85rem; color: #475569;">
                    <strong>Status:</strong> <span style="text-transform:uppercase; font-weight:bold;"><?= htmlspecialchars($campaign['status']) ?></span>
                </div>

                <hr style="border:0; border-top:1px solid #e2e8f0; margin: 20px 0;">
                
                <h4 style="margin-top:0; color: #0f172a;">Load from Template</h4>
                <p style="font-size:0.8rem; color:#64748b; margin-bottom: 15px;">Click a template below to load it into the editor (overwrites current).</p>
                
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($templates as $t): ?>
                        <div class="template-card" onclick="loadTemplate(<?= htmlspecialchars(json_encode($t['content'])) ?>)">
                            <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($t['name']) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;"><?= htmlspecialchars($t['category'] ?? 'Uncategorised') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="content" id="hiddenContent">
            </div>

            <div class="editor-panel">
                <div id="editor" style="flex:1;"></div>
            </div>
        </div>
    </form>
</div>

<div id="initial-content" style="display:none;"><?= htmlspecialchars($campaign['content']) ?></div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'header': '1' }, { 'header': '2' }, 'blockquote'],
                [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    // Load initial content
    var initialHtml = document.getElementById('initial-content').innerText;
    quill.clipboard.dangerouslyPasteHTML(initialHtml);

    function syncContent() {
        var html = document.querySelector('.ql-editor').innerHTML;
        document.getElementById('hiddenContent').value = html;
    }

    function submitForm() {
        syncContent();
        document.getElementById('campaignForm').submit();
    }

    function loadTemplate(htmlContent) {
        if (confirm("This will overwrite the current editor content. Continue?")) {
            const clipboard = quill.getModule('clipboard');
            clipboard.dangerouslyPasteHTML(htmlContent);
        }
    }
</script>
<?php require __DIR__ . '/../footer.php'; ?>

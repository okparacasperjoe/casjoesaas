<?php
// CASJOE MAIL - CREATE NEW TEMPLATE
$active = 'templates';
$sectors = [
    '📢 Promotions & Offers',
    '📰 Newsletter',
    '🛒 eCommerce / Order Updates',
    '🏦 Finance / Fintech',
    '🎉 Events & Invitations',
    '🤝 Onboarding / Welcome',
    '🔔 Transactional / Alerts',
    '📊 Reports & Summaries',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Create Template | Casjoe Biz</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .editor-wrap { display: grid; grid-template-columns: 280px 1fr; gap: 20px; height: calc(100vh - 120px); }
        .settings-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; overflow-y: auto; }
        .editor-panel { background: white; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden; }
        .editor-toolbar-extra { display: flex; gap: 8px; padding: 10px 14px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .editor-toolbar-extra button { padding: 5px 12px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.82rem; display: flex; align-items: center; gap: 5px; }
        .editor-toolbar-extra button:hover { background: #f0f4ff; border-color: #000066; color: #000066; }
        .ql-editor { color: black !important; min-height: 200px; font-size: 16px; font-family: 'Inter', sans-serif; }
        .ql-snow .ql-stroke { stroke: #333 !important; }
        .ql-snow .ql-fill { fill: #333 !important; }
        .ql-snow .ql-picker { color: #333 !important; }
        /* Import HTML Modal */
        .import-modal { display: none; position: fixed; inset: 0; z-index: 1050; }
        .import-modal.open { display: flex; align-items: center; justify-content: center; }
        .import-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
        .import-box {
            position: relative; background: white; border-radius: 14px;
            padding: 28px; width: 90%; max-width: 700px; z-index: 1;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .import-box h3 { margin: 0 0 6px; }
        .import-box p { color: #666; font-size: 0.85rem; margin: 0 0 14px; }
        .import-box textarea { width: 100%; height: 260px; padding: 12px; font-family: monospace; font-size: 0.82rem; border: 1px solid #ccc; border-radius: 8px; resize: vertical; box-sizing: border-box; }
        /* Cloud Image picker */
        .upload-zone { border: 3px dashed #000066; border-radius: 12px; padding: 30px; text-align: center; cursor: pointer; background: #f8f9ff; transition: all 0.2s; }
        .upload-zone:hover { border-color: #FFA600; background: #f0f4ff; }
        .cloud-file-item:hover { border-color: #FFA600 !important; transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="app-container">
    <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

    <main class="main-content" style="overflow: hidden;">
        <div class="top-bar">
            <h2>Create New Template</h2>
            <div style="display:flex;gap:10px;">
                <a href="/mail/templates" class="btn btn-secondary btn-sm">&laquo; Back</a>
                <button class="btn btn-primary btn-sm" onclick="submitForm()">💾 Save Template</button>
            </div>
        </div>

        <form id="templateForm" action="/mail/templates/save" method="POST" onsubmit="syncContent()">
            <div class="editor-wrap">
                <!-- Settings Panel -->
                <div class="settings-panel">
                    <div class="form-group mb-3">
                        <label><strong>Template Name</strong></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Summer Promo Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label><strong>Subject Line</strong></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. 🔥 Don't miss this!">
                    </div>
                    <div class="form-group mb-3">
                        <label><strong>Sector / Category</strong></label>
                        <select name="category" class="form-control">
                            <option value="">-- Select Sector --</option>
                            <?php foreach ($sectors as $sector): ?>
                                <option value="<?= htmlspecialchars($sector) ?>"><?= htmlspecialchars($sector) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="content" id="hiddenContent">
                    <hr>
                    <p style="font-size:0.8rem;color:#888;margin:0;">Tip: Use the 🖼️ image button in the toolbar to insert images from Casjoe Cloud.</p>
                </div>

                <!-- Editor Panel -->
                <div class="editor-panel">
                    <div class="editor-toolbar-extra">
                        <button type="button" onclick="openImportModal()">
                            📥 Import HTML
                        </button>
                        <button type="button" onclick="openCloudPicker()">
                            🖼️ Insert Image
                        </button>
                    </div>
                    <div id="quill-editor" style="flex:1;background:white;color:black;"></div>
                </div>
            </div>
        </form>
    </main>
</div>

<!-- Import HTML Modal -->
<div class="import-modal" id="importModal">
    <div class="import-backdrop" onclick="closeImportModal()"></div>
    <div class="import-box">
        <h3>📥 Import HTML Code</h3>
        <p>Paste your raw HTML email code below and click "Load" to import it into the editor.</p>
        <textarea id="htmlImportArea" placeholder="Paste your HTML here..."></textarea>
        <div style="display:flex;gap:10px;margin-top:14px;justify-content:flex-end;">
            <button type="button" onclick="closeImportModal()" style="padding:8px 18px;border-radius:8px;border:1px solid #ccc;background:white;cursor:pointer;">Cancel</button>
            <button type="button" onclick="loadImportedHtml()" style="padding:8px 18px;border-radius:8px;border:none;background:#000066;color:white;cursor:pointer;font-weight:600;">✅ Load into Editor</button>
        </div>
    </div>
</div>

<!-- Cloud Picker Modal -->
<div class="modal fade" id="cloudPickerModal" tabindex="-1" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; overflow: hidden;">
    <div class="modal-dialog modal-xl" style="max-width: 900px; margin: 30px auto;">
        <div class="modal-content" style="background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: #f8f9fa; border-bottom: 1px solid #eee; padding: 15px 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="margin: 0; font-size: 1.2rem;">📁 Insert Image</h5>
                <button type="button" onclick="closeCloudModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div style="display: flex; border-bottom: 1px solid #dee2e6; margin-bottom: 20px;">
                    <button onclick="switchCloudTab('upload')" id="tab-upload" style="background:none;border:none;padding:10px 20px;cursor:pointer;border-bottom:2px solid #000066;color:#000066;font-weight:bold;">⬆️ Upload New</button>
                    <button onclick="switchCloudTab('cloud')" id="tab-cloud" style="background:none;border:none;padding:10px 20px;cursor:pointer;color:#666;">☁️ Casjoe Cloud</button>
                </div>
                <div id="view-upload">
                    <div class="upload-zone" id="uploadZone">
                        <ion-icon name="cloud-upload-outline" style="font-size: 48px; color: #000066;"></ion-icon>
                        <h4 style="margin: 10px 0;">Drop image here or click to browse</h4>
                        <small>JPG, PNG, GIF, WebP (Max 10MB)</small>
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div id="view-cloud" style="display: none;">
                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <input type="text" id="cloudSearch" class="form-control" placeholder="🔍 Search files..." style="flex:1;padding:8px;border:1px solid #ccc;border-radius:4px;">
                        <button onclick="loadCloudFiles(document.getElementById('cloudSearch').value)" style="padding:8px 15px;border:1px solid #000066;background:white;color:#000066;border-radius:4px;cursor:pointer;">Search</button>
                    </div>
                    <div id="cloudGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; max-height: 350px; overflow-y: auto;">
                        <div style="grid-column:1/-1;text-align:center;color:#888;padding:30px;">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalBackdrop" style="display: none; position: fixed; inset:0; background: rgba(0,0,0,0.5); z-index: 1040;"></div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: {
            container: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['blockquote', 'code-block'],
                ['clean']
            ],
            handlers: {
                'image': function() { openCloudPicker(); }
            }
        }
    }
});

// ----- Form submission -----
function syncContent() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
}
function submitForm() {
    syncContent();
    document.getElementById('templateForm').submit();
}

// ----- Import HTML Modal -----
function openImportModal() {
    document.getElementById('importModal').classList.add('open');
}
function closeImportModal() {
    document.getElementById('importModal').classList.remove('open');
}
function loadImportedHtml() {
    const html = document.getElementById('htmlImportArea').value.trim();
    if (!html) { alert('Please paste some HTML first.'); return; }
    quill.root.innerHTML = html;
    closeImportModal();
    document.getElementById('htmlImportArea').value = '';
}

// ----- Cloud Picker -----
function openCloudPicker() {
    document.getElementById('cloudPickerModal').style.display = 'block';
    document.getElementById('modalBackdrop').style.display = 'block';
    switchCloudTab('upload');
    loadCloudFiles();
}
function closeCloudModal() {
    document.getElementById('cloudPickerModal').style.display = 'none';
    document.getElementById('modalBackdrop').style.display = 'none';
}
function switchCloudTab(tab) {
    const isUpload = tab === 'upload';
    document.getElementById('view-upload').style.display = isUpload ? 'block' : 'none';
    document.getElementById('view-cloud').style.display  = isUpload ? 'none'  : 'block';
    document.getElementById('tab-upload').style.borderBottom = isUpload ? '2px solid #000066' : 'none';
    document.getElementById('tab-upload').style.color = isUpload ? '#000066' : '#666';
    document.getElementById('tab-cloud').style.borderBottom  = isUpload ? 'none' : '2px solid #000066';
    document.getElementById('tab-cloud').style.color  = isUpload ? '#666'  : '#000066';
    if (!isUpload) loadCloudFiles();
}
const uploadZone  = document.getElementById('uploadZone');
const imageUpload = document.getElementById('imageUpload');
uploadZone.addEventListener('click', () => imageUpload.click());
uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.style.borderColor = '#FFA600'; });
uploadZone.addEventListener('drop', e => { e.preventDefault(); uploadZone.style.borderColor = '#000066'; handleUpload(e.dataTransfer.files[0]); });
imageUpload.addEventListener('change', e => handleUpload(e.target.files[0]));

async function handleUpload(file) {
    if (!file || !file.type.startsWith('image/')) { alert('Please select an image file'); return; }
    const fd = new FormData(); fd.append('file', file);
    try {
        const res  = await fetch('/cloud/upload', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.url) { insertImage(data.url); closeCloudModal(); }
        else alert('Upload failed: ' + (data.error || 'Unknown error'));
    } catch(e) { alert('Upload error: ' + e.message); }
}
async function loadCloudFiles(search = '') {
    const res  = await fetch(`/cloud/files?type=image&search=${search}`);
    const data = await res.json();
    const grid = document.getElementById('cloudGrid');
    if (!data.files || !data.files.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:#888;padding:30px;">No images found</div>';
        return;
    }
    grid.innerHTML = data.files.map(f => `
        <div class="cloud-file-item" onclick="insertImage('${f.url}')" style="cursor:pointer;border:2px solid #e2e8f0;border-radius:8px;padding:5px;text-align:center;transition:all 0.2s;background:white;">
            <div style="height:90px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border-radius:4px;margin-bottom:4px;overflow:hidden;">
                <img src="${f.url}" style="max-width:100%;max-height:100%;object-fit:contain;">
            </div>
            <small style="display:block;font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${f.name}</small>
        </div>`).join('');
}
function insertImage(url) {
    const range = quill.getSelection(true);
    const index = range ? range.index : quill.getLength();
    quill.insertEmbed(index, 'image', url);
    quill.insertText(index + 1, '\n');
    quill.setSelection(index + 2);
    closeCloudModal();
}
</script>
</body>
</html>

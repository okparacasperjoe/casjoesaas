<?php
$pageTitle = 'Edit Post';
require __DIR__ . '/../header.php';
use App\Core\Services\CsrfService;
?>

<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    /* ─── Edit Post Styles ─── */
    .ep-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
    }
    .ep-header h2 { color: #000066; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px; }
    .ep-header .ep-actions { display: flex; gap: 10px; align-items: center; }
    .ep-back {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
        background: #f0f2f5; color: #555; border-radius: 8px; text-decoration: none;
        font-weight: 500; font-size: 0.9rem; transition: all 0.2s;
    }
    .ep-back:hover { background: #e2e5ea; color: #333; }

    .ep-container { max-width: 960px; margin: 0 auto; }

    /* Two-column layout */
    .ep-grid { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
    @media (max-width: 860px) { .ep-grid { grid-template-columns: 1fr; } }

    .ep-card {
        background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); overflow: hidden;
    }
    .ep-card-header {
        padding: 16px 20px; border-bottom: 1px solid #f0f2f5;
        font-weight: 700; font-size: 0.85rem; color: #000066;
        text-transform: uppercase; letter-spacing: 0.5px;
        display: flex; align-items: center; gap: 8px;
    }
    .ep-card-body { padding: 20px; }

    /* Form fields */
    .ep-field { margin-bottom: 20px; }
    .ep-field:last-child { margin-bottom: 0; }
    .ep-label {
        display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.88rem; color: #334155;
    }
    .ep-hint { font-size: 0.78rem; color: #94a3b8; margin-top: 4px; }
    .ep-input, .ep-textarea {
        width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 0.95rem; color: #1e293b; background: #fafbfc; transition: border 0.2s;
        font-family: inherit; box-sizing: border-box;
    }
    .ep-input:focus, .ep-textarea:focus { outline: none; border-color: #000066; background: #fff; }
    .ep-textarea { resize: vertical; min-height: 80px; }

    /* Publish Toggle */
    .ep-toggle-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 0; border-bottom: 1px solid #f0f2f5;
    }
    .ep-toggle-row:last-child { border-bottom: none; }
    .ep-toggle-label { font-weight: 600; font-size: 0.9rem; color: #334155; }
    .ep-toggle-sub { font-size: 0.78rem; color: #94a3b8; }
    .ep-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
    .ep-switch input { opacity: 0; width: 0; height: 0; }
    .ep-switch .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background: #cbd5e1; border-radius: 26px; transition: 0.3s;
    }
    .ep-switch .slider:before {
        content: ""; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px;
        background: white; border-radius: 50%; transition: 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .ep-switch input:checked + .slider { background: #000066; }
    .ep-switch input:checked + .slider:before { transform: translateX(22px); }

    /* Image Preview */
    .ep-img-preview {
        width: 100%; height: 160px; border-radius: 8px; border: 2px dashed #e2e8f0;
        display: flex; align-items: center; justify-content: center; overflow: hidden;
        background: #f8fafc; margin-top: 10px; transition: border-color 0.2s;
    }
    .ep-img-preview:hover { border-color: #cbd5e1; }
    .ep-img-preview img { width: 100%; height: 100%; object-fit: cover; border-radius: 6px; }
    .ep-img-placeholder { text-align: center; color: #94a3b8; }
    .ep-img-placeholder ion-icon { font-size: 2rem; margin-bottom: 4px; }
    .ep-img-placeholder p { font-size: 0.78rem; margin: 0; }

    /* Quill overrides */
    .ql-toolbar.ql-snow { border: 1.5px solid #e2e8f0 !important; border-bottom: none !important; border-radius: 8px 8px 0 0 !important; background: #fafbfc; }
    .ql-container.ql-snow { border: 1.5px solid #e2e8f0 !important; border-radius: 0 0 8px 8px !important; font-size: 15px; }
    .ql-editor { min-height: 350px; color: #1e293b !important; line-height: 1.7; }
    .ql-snow .ql-stroke { stroke: #475569 !important; }
    .ql-snow .ql-fill { fill: #475569 !important; }
    .ql-snow .ql-picker { color: #475569 !important; }
    .ql-snow .ql-picker-options { background: #fff !important; border: 1px solid #e2e8f0 !important; border-radius: 8px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

    /* AI Button */
    .ep-ai-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
        background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff;
        border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
    }
    .ep-ai-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124,58,237,0.35); }
    .ep-ai-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
    .ep-ai-loading { display: none; font-size: 0.82rem; color: #7c3aed; margin-left: 8px; }

    /* Submit Button */
    .ep-submit {
        width: 100%; padding: 14px; background: #000066; color: #FFA600;
        border: none; border-radius: 8px; font-size: 1rem; font-weight: 700;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center;
        justify-content: center; gap: 8px;
    }
    .ep-submit:hover { background: #000055; box-shadow: 0 4px 15px rgba(0,0,102,0.3); }

    /* Meta info */
    .ep-meta { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #94a3b8; padding: 12px 20px; border-top: 1px solid #f0f2f5; }
</style>

<div class="ep-container">
    <!-- Header -->
    <div class="ep-header">
        <h2><ion-icon name="create-outline"></ion-icon> Edit Post</h2>
        <div class="ep-actions">
            <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="ep-back" style="background:#eef2ff; color:#000066;">
                <ion-icon name="eye-outline"></ion-icon> Preview
            </a>
            <a href="/<?= ADMIN_PATH ?>/cms" class="ep-back">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to CMS
            </a>
        </div>
    </div>

    <form action="/<?= ADMIN_PATH ?>/cms/post/update/<?= $post['id'] ?>" method="POST" id="editPostForm" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= CsrfService::generateToken() ?>">

        <div class="ep-grid">
            <!-- LEFT: Main Content -->
            <div>
                <!-- Title & Slug Card -->
                <div class="ep-card" style="margin-bottom: 24px;">
                    <div class="ep-card-header"><ion-icon name="text-outline"></ion-icon> Post Details</div>
                    <div class="ep-card-body">
                        <div class="ep-field">
                            <label class="ep-label">Post Title</label>
                            <input type="text" name="title" class="ep-input" value="<?= htmlspecialchars($post['title']) ?>" required placeholder="Enter a compelling title...">
                        </div>
                        <div class="ep-field">
                            <label class="ep-label">URL Slug</label>
                            <input type="text" name="slug" class="ep-input" value="<?= htmlspecialchars($post['slug']) ?>" required>
                            <div class="ep-hint">casjoe.com/blog/<strong><?= htmlspecialchars($post['slug']) ?></strong></div>
                        </div>
                        <div class="ep-field">
                            <label class="ep-label">Excerpt</label>
                            <textarea name="excerpt" class="ep-textarea" rows="3" placeholder="A short summary for SEO & previews..."><?= htmlspecialchars($post['excerpt']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Card -->
                <div class="ep-card" style="margin-bottom: 24px;">
                    <div class="ep-card-header" style="justify-content: space-between;">
                        <span style="display:flex; align-items:center; gap:8px;"><ion-icon name="code-slash-outline"></ion-icon> Content</span>
                        <div>
                            <button type="button" id="btn-ai-generate" class="ep-ai-btn">✨ AI Generate</button>
                            <span id="ai-loading" class="ep-ai-loading">⏳ Generating...</span>
                        </div>
                    </div>
                    <div style="padding: 0;">
                        <div id="quill-editor"></div>
                        <input type="hidden" name="content" id="hiddenContent">
                    </div>
                </div>

                <!-- SEO Card -->
                <div class="ep-card">
                    <div class="ep-card-header"><ion-icon name="search-outline"></ion-icon> SEO Settings</div>
                    <div class="ep-card-body">
                        <div class="ep-field">
                            <label class="ep-label">Meta Title</label>
                            <input type="text" name="meta_title" id="metaTitleInput" class="ep-input" value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>" placeholder="Optional Meta Title for search engines">
                            <div class="ep-hint">If left blank, the Post Title will be used. (Recommended: 50-60 characters)</div>
                        </div>
                        <div class="ep-field">
                            <label class="ep-label">Meta Description</label>
                            <textarea name="meta_description" class="ep-textarea" rows="3" placeholder="Brief summary of the page for search results"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                            <div class="ep-hint">If left blank, the Excerpt will be used. (Recommended: 150-160 characters)</div>
                        </div>
                        <div class="ep-field">
                            <label class="ep-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="ep-input" value="<?= htmlspecialchars($post['meta_keywords'] ?? '') ?>" placeholder="e.g. finance, business, software, africa">
                            <div class="ep-hint">Comma-separated list of keywords.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Sidebar -->
            <div>
                <!-- Publish Card -->
                <div class="ep-card" style="margin-bottom: 24px;">
                    <div class="ep-card-header"><ion-icon name="rocket-outline"></ion-icon> Publish</div>
                    <div class="ep-card-body">
                        <div class="ep-toggle-row">
                            <div>
                                <div class="ep-toggle-label">Published</div>
                                <div class="ep-toggle-sub">Visible on blog</div>
                            </div>
                            <label class="ep-switch">
                                <input type="checkbox" name="is_published" value="1" <?= ($post['is_published'] ?? 1) ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div style="margin-top: 16px;">
                            <button type="submit" class="ep-submit">
                                <ion-icon name="save-outline"></ion-icon> Save Changes
                            </button>
                        </div>
                    </div>
                    <div class="ep-meta">
                        <ion-icon name="time-outline"></ion-icon>
                        Created: <?= date('M j, Y', strtotime($post['created_at'])) ?>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="ep-card">
                    <div class="ep-card-header"><ion-icon name="image-outline"></ion-icon> Featured Image</div>
                    <div class="ep-card-body">
                        <div class="ep-field">
                            <label class="ep-label">Image URL / Upload Image</label>
                            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" name="image_url" id="imageUrlInput" class="ep-input" value="<?= htmlspecialchars($post['image_url'] ?? '') ?>" placeholder="https://example.com/image.jpg" style="flex: 1;">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('fileInput').click()" style="padding: 10px 14px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; color: #334155; font-weight: 600; font-size: 0.85rem; gap: 5px;">
                                    <ion-icon name="cloud-upload-outline" style="font-size: 1.1rem;"></ion-icon> Upload File
                                </button>
                            </div>
                            <input type="file" id="fileInput" name="featured_image" accept="image/*" style="display: none;">
                            <div class="ep-hint">Paste an image URL or click "Upload File" to select an image from your computer.</div>
                        </div>
                        <div class="ep-img-preview" id="imagePreview">
                            <?php if (!empty($post['image_url'])): ?>
                                <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Featured image">
                            <?php else: ?>
                                <div class="ep-img-placeholder">
                                    <ion-icon name="cloud-upload-outline"></ion-icon>
                                    <p>Select or upload an image</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Start writing your blog post...',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
        ]
    }
});

// Load existing content
quill.root.innerHTML = <?= json_encode($post['content']) ?>;

// Sync content to hidden input on form submit
document.getElementById('editPostForm').onsubmit = function() {
    var content = document.getElementById('hiddenContent');
    content.value = quill.root.innerHTML;
    if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
         content.value = '';
    }
};

// AI Generate
document.getElementById('btn-ai-generate').addEventListener('click', function() {
    const topic = prompt("What should the blog post be about?");
    if (!topic) return;

    const btn = this;
    const loading = document.getElementById('ai-loading');

    btn.disabled = true;
    loading.style.display = 'inline';

    fetch('/<?= ADMIN_PATH ?>/cms/ai-generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ topic: topic })
    })
    .then(response => response.json())
    .then(data => {
        if (data.content) {
            quill.root.innerHTML = data.content;
        } else if (data.error) {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => {
        alert('Network error or server failed to respond.');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        loading.style.display = 'none';
    });
});

// Image URL Preview
document.getElementById('imageUrlInput').addEventListener('input', function() {
    const url = this.value.trim();
    const preview = document.getElementById('imagePreview');
    if (url) {
        preview.innerHTML = '<img src="' + url + '" alt="Featured image" onerror="this.parentElement.innerHTML=\'<div class=ep-img-placeholder><ion-icon name=alert-circle-outline></ion-icon><p>Invalid image URL</p></div>\'">';
    } else {
        preview.innerHTML = '<div class="ep-img-placeholder"><ion-icon name="cloud-upload-outline"></ion-icon><p>Select or upload an image</p></div>';
    }
});

// File Upload Preview
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('imagePreview');
        const url = URL.createObjectURL(file);
        preview.innerHTML = '<img src="' + url + '" alt="Local preview">';
        // Clear the URL input so the system knows we are using the uploaded file instead
        document.getElementById('imageUrlInput').value = '';
    }
});
</script>

<?php require __DIR__ . '/../footer.php'; ?>

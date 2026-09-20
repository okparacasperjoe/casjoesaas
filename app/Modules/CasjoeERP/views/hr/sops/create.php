<?php
$isEdit = isset($sop);
$title = $isEdit ? "Edit SOP" : "Draft New SOP";
require __DIR__ . '/../../layout/header.php';
?>
<div class="mb-4">
    <h2><?= $title ?></h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="/erp/sops/<?= $isEdit ? 'update' : 'store' ?>" method="POST">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $sop['id'] ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($sop['title'] ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Content</label>
                <!-- Quill Editor Container -->
                <div id="quill-editor" style="height: 300px;"><?= $sop['content'] ?? '' ?></div>
                <textarea name="content" id="sop_content" style="display:none;"><?= htmlspecialchars($sop['content'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save SOP</button>
            <a href="/erp/sops" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<!-- Load QuillJS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Make Quill Editor White with Black Text */
    .ql-toolbar.ql-snow {
        background-color: #f8f9fa;
        border-color: #ccc;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }
    .ql-container.ql-snow {
        background-color: #ffffff;
        color: #000000;
        border-color: #ccc;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    #quill-editor {
        height: auto;
        min-height: 300px;
    }
    .ql-editor {
        min-height: 300px;
        font-size: 16px;
    }
</style>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['link', 'image', 'video'],
        ['clean']
    ];

    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: toolbarOptions
        }
    });

    var form = document.querySelector('form');
    form.onsubmit = function() {
        var content = document.querySelector('#sop_content');
        content.value = quill.root.innerHTML;
    };
</script>
<?php require __DIR__ . '/../../layout/footer.php'; ?>

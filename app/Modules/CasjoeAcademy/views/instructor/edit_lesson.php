<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson: <?= htmlspecialchars($lesson['title']) ?></title>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Force Quill Content Black */
        .ql-editor { color: black !important; min-height: 400px; font-size: 16px; }
        .ql-snow .ql-stroke { stroke: #333 !important; }
        .ql-snow .ql-fill { fill: #333 !important; }
        .ql-snow .ql-picker { color: #333 !important; }
    </style>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .editor-container { max-width: 900px; margin: 0 auto; background: white; color: #333; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; font-family: inherit; color: #333; background: #fff; }
        .help-text { font-size: 13px; color: #666; margin-top: 5px; }
        a { text-decoration: none; }
    </style>
    <style>
        .main-content { margin-left: 270px; }
        @media (max-width: 992px) {
            .sidebar { left: -270px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
            .row { flex-direction: column; }
            .grid-container, .course-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_acad_css.php'; ?>

        <?php include dirname(__DIR__) . '/partials/sidebar_academy_css.php'; ?>
        <div class="acad-brand">
            <ion-icon name="school-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
            <span>Casjoe Business School</span>
        </div>
        <ul class="acad-menu">
            <li class="acad-item"><a href="#" onclick="history.back(); return false;" class="acad-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Course</a></li>
            <li class="acad-item"><a href="#" class="acad-link acad-active"><ion-icon name="create-outline"></ion-icon> Edit Lesson</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
             <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h1>Edit Lesson</h1>
                <div>
                     <!-- Actions if needed -->
                </div>
            </div>
        </div>

        <div class="editor-container">
            <form action="/academy/instructor/lesson/update" method="POST">
                <input type="hidden" name="id" value="<?= $lesson['id'] ?>">
                <!-- Fallback Logic for course_id -->
                <input type="hidden" name="course_id" value="<?= $lesson['course_id'] ?? ($lesson['section_id'] ?? 0) ?>">

                <div class="form-group">
                    <label>Lesson Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Video URL (Optional)</label>
                    <input type="text" name="video_url" value="<?= htmlspecialchars($lesson['video_url'] ?? '') ?>" class="form-control" placeholder="https://youtube.com/...">
                </div>

                <div class="form-group">
                    <label>Lesson Content</label>
                    <div id="quill-editor" style="background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px;"></div>
                    <input type="hidden" name="content" id="hiddenContent">
                </div>

                <div style="text-align: right;">
                    <a href="javascript:history.back()" class="btn" style="background: #eee; color: #333; margin-right: 10px;">Cancel</a>
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</div>

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
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'video'],
                ['clean']
            ],
            handlers: {
                image: imageHandler
            }
        }
    }
});

function imageHandler() {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = async () => {
        var file = input.files[0];
        var formData = new FormData();
        formData.append('file', file);
        
        // Use the existing TinyMCE endpoint which expects a standard file POST
        // Note: The endpoint expects the file at `current($_FILES)`, so name doesn't matter
        try {
            const response = await fetch('/academy/instructor/upload-image', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.location) {
                var range = this.quill.getSelection();
                this.quill.insertEmbed(range.index, 'image', result.location, Quill.sources.USER);
            } else {
                alert('Upload failed.');
            }
        } catch (error) {
            console.error('Error uploading image:', error);
            alert('Upload failed.');
        }
    };
}

// Load existing content
quill.root.innerHTML = <?= json_encode($lesson['content']) ?>;

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
</body>
</html>

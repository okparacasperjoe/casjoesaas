<!-- Cache Bust: <?= time() ?> -->
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Force Quill Content Black */
        .ql-editor { color: black !important; min-height: 200px; font-size: 16px; }
        .ql-snow .ql-stroke { stroke: #333 !important; }
        .ql-snow .ql-fill { fill: #333 !important; }
        .ql-snow .ql-picker { color: #333 !important; }
    </style>
    <style>
        .form-card { max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group label { font-weight: 600; color: #444; margin-bottom: 8px; display: block; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; transition: border 0.3s; color: #000066 !important; background-color: #ffffff !important; }
        .form-control:focus { border-color: var(--primary); outline: none; }
        .upload-box { border: 2px dashed #ddd; padding: 30px; text-align: center; border-radius: 8px; cursor: pointer; transition: background 0.2s; }
        .upload-box:hover { background: #f9f9f9; border-color: #ccc; }
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
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_acad_css.php'; ?>

            <?php include dirname(__DIR__) . '/partials/sidebar_academy_css.php'; ?>
            <div class="acad-brand">
                <ion-icon name="school-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
                <span>Casjoe Business School</span>
            </div>
            <ul class="acad-menu">
                <li class="acad-item"><a href="/academy/instructor" class="acad-link"><ion-icon name="arrow-back-outline"></ion-icon> Cancel</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="form-card">
                <h2 style="margin-top: 0; margin-bottom: 25px; text-align: center; color: #000066;">Name Your Course</h2>
                
                <form action="/academy/instructor/store" method="POST" enctype="multipart/form-data">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Course Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Master Class in Strategic Finance" required>
                    </div>

                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="Operations">Operations</option>
                                <option value="Sales">Sales</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Technology">Technology</option>
                                <option value="Finance">Finance</option>
                                <option value="HR">HR & Leadership</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Price ($)</label>
                            <input type="number" name="price" class="form-control" value="0.00" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Thumbnail Image</label>
                        <div class="upload-box" onclick="document.getElementById('thumbInput').click()">
                            <ion-icon name="image-outline" style="font-size: 2rem; color: #888;"></ion-icon>
                            <p style="margin: 10px 0 0; color: #666;">Click to upload cover image</p>
                            <input type="file" name="thumbnail" id="thumbInput" style="display: none;" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label>Description</label>
                        <div id="quill-editor" style="background: white; border: 1px solid #ddd; border-radius: 6px;"></div>
                        <input type="hidden" name="description" id="hiddenDescription">
                        <button type="button" class="btn" id="ai-gen-btn" style="margin-top: 15px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; border: none; padding: 10px 20px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; transition: transform 0.2s, box-shadow 0.2s;" onclick="generateOutline()">
                            <ion-icon name="color-wand" style="font-size: 1.2rem;"></ion-icon> <span id="ai-btn-text">AI Auto-Fill Description & Outline</span>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;">Create Course & Build Curriculum</button>
                </form>
            </div>
            
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Briefly describe what students will learn...',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
});

// Sync content to hidden input on form submit
document.querySelector('form').onsubmit = function() {
    var content = document.querySelector('#hiddenDescription');
    content.value = quill.root.innerHTML;
    if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
         content.value = '';
    }
};

            async function generateOutline() {
                const title = document.querySelector('input[name="title"]').value;
                const category = document.querySelector('select[name="category"]').value;
                const btn = document.getElementById('ai-gen-btn');
                const btnText = document.getElementById('ai-btn-text');
                const btnIcon = btn.querySelector('ion-icon');
                
                if (!title) return alert('Please enter a Course Title first.');
                
                btnText.innerText = 'Generating Curriculum...';
                btnIcon.setAttribute('name', 'sync');
                btnIcon.classList.add('spin-anim'); // We'll add this CSS via style tag or just let it rotate
                btn.style.opacity = '0.7';
                btn.disabled = true;
                
                try {
                    const formData = new FormData();
                    formData.append('title', title);
                    formData.append('category', category);
                    
                    const res = await fetch('/academy/ai/generate', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.content) {
                        quill.root.innerHTML = data.content;
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error occurred'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Could not connect to AI generator. Please check your network connection.');
                } finally {
                    btnText.innerText = 'AI Auto-Fill Description & Outline';
                    btnIcon.setAttribute('name', 'color-wand');
                    btnIcon.classList.remove('spin-anim');
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }
            }
            </script>
            <style>
                @keyframes spin { 100% { transform: rotate(360deg); } }
                .spin-anim { animation: spin 1s linear infinite; }
                #ai-gen-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }
            </style>
        </main>
    </div>
</body>
</html>


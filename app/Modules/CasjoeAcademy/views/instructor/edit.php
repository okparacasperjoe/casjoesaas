<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course: <?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        /* Force Quill Content Black */
        .ql-editor { color: black !important; min-height: 200px; font-size: 16px; }
        .ql-snow .ql-stroke { stroke: #333 !important; }
        .ql-snow .ql-fill { fill: #333 !important; }
        .ql-snow .ql-picker { color: #333 !important; }
    </style>
    <style>
        .builder-container { display: grid; grid-template-columns: 250px 1fr; gap: 30px; }
        .builder-main { background: white; color: #333; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .section-block { background: #f8f9fa; color: #333; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 20px; overflow: hidden; }
        .section-header { padding: 15px; background: #eee; color: #333; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
        .lesson-list { padding: 10px; }
        .lesson-item { 
            background: white; padding: 10px; margin-bottom: 8px; border-radius: 4px; border: 1px solid #ddd; 
            display: flex; justify-content: space-between; align-items: center;
        }
        .drag-handle { cursor: grab; color: #aaa; margin-right: 10px; }
        .drag-handle:active { cursor: grabbing; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%; }
        .hidden { display: none; }
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
    <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_acad_css.php'; ?>

        <?php include dirname(__DIR__) . '/partials/sidebar_academy_css.php'; ?>
        <div class="acad-brand">
            <ion-icon name="school-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
            <span>Casjoe Business School</span>
        </div>
        <ul class="acad-menu">
            <li class="acad-item"><a href="/academy/instructor" class="acad-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Dashboard</a></li>
            <li class="acad-item"><a href="#" onclick="showView('settings')" id="nav-settings" class="acad-link"><ion-icon name="settings-outline"></ion-icon> Course Settings</a></li>
            <li class="acad-item"><a href="#" onclick="showView('curriculum')" id="nav-curriculum" class="acad-link acad-active"><ion-icon name="create-outline"></ion-icon> Edit Curriculum</a></li>
            <?php
            $previewLink = "/academy/course/" . $course['id']; // Fallback
            foreach ($sections as $sec) {
                if (!empty($sec['lessons'])) {
                    $previewLink = "/academy/learn/" . $sec['lessons'][0]['id'];
                    break;
                }
            }
            ?>
            <li class="acad-item" style="margin-top: 30px;">
                <a href="<?= $previewLink ?>" target="_blank" class="acad-link" style="background: rgba(255,255,255,0.1);"><ion-icon name="eye-outline"></ion-icon> Preview Course</a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h1>Editing: <?= htmlspecialchars($course['title']) ?></h1>
            <div>
                <?php if($course['status'] === 'draft'): ?>
                    <a href="/academy/instructor/publish/<?= $course['id'] ?>" class="btn">Publish Course</a>
                <?php else: ?>
                    <span class="badge" style="background: #2ed573; color: white; padding: 5px 12px; border-radius: 12px;">Published</span>
                <?php endif; ?>
                <button onclick="showView('settings')" class="btn" style="background: #34495e; color: white; margin-left: 10px; font-weight: 500;">Course Settings</button>
            </div>
        </div>

        <!-- Curriculum View -->
        <div class="builder-main" id="curriculum-view">
            <h3>Curriculum Builder</h3>
            <p style="margin-bottom: 20px; color: #666;">Organize your course into sections and lessons.</p>

            <!-- Sections Loop -->
            <div id="sections-container">
            <?php foreach ($sections as $section): ?>
                <div class="section-block" data-id="<?= $section['id'] ?>">
                    <div class="section-header">
                        <span>
                            <ion-icon name="menu-outline" class="drag-handle section-handle" style="font-size: 1.2rem; vertical-align: middle;"></ion-icon>
                            <ion-icon name="folder-open-outline" style="vertical-align: middle;"></ion-icon> <?= htmlspecialchars($section['title']) ?>
                        </span>
                        <a href="/academy/instructor/section/delete/<?= $section['id'] ?>" onclick="return confirm('Delete section?')" style="color: #ff4757;"><ion-icon name="trash-outline"></ion-icon></a>
                    </div>
                    <div class="lesson-list" data-section-id="<?= $section['id'] ?>">
                        <?php if(empty($section['lessons'])): ?>
                            <div style="text-align: center; padding: 10px; font-size: 0.9rem; color: #999;">No lessons yet.</div>
                        <?php else: ?>
                            <?php foreach ($section['lessons'] as $lesson): ?>
                                <div class="lesson-item" data-id="<?= $lesson['id'] ?>">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <ion-icon name="menu-outline" class="drag-handle lesson-handle"></ion-icon>
                                        <ion-icon name="<?= ($lesson['content_type'] ?? 'text') == 'video' ? 'videocam' : (($lesson['content_type'] ?? 'text') == 'pdf' ? 'document' : 'text') ?>-outline" style="color: var(--secondary);"></ion-icon>
                                        <div>
                                            <strong><?= htmlspecialchars($lesson['title']) ?></strong>
                                            <span style="font-size: 0.8rem; color: #888; margin-left: 5px; text-transform: uppercase;"><?= $lesson['content_type'] ?? 'TEXT' ?></span>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <a href="/academy/instructor/lesson/edit/<?= $lesson['id'] ?>" style="color: #3498db; font-size: 1.2rem;"><ion-icon name="create-outline"></ion-icon></a>
                                        <a href="/academy/instructor/lesson/delete/<?= $lesson['id'] ?>" onclick="return confirm('Delete lesson?')" style="color: #aaa; font-size: 1.2rem;"><ion-icon name="close-circle-outline"></ion-icon></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <button onclick="openLessonModal(<?= $section['id'] ?>)" class="btn" style="width: 100%; margin-top: 10px; background: transparent; border: 1px dashed #ccc; color: #666;">+ Add Lesson</button>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

            <!-- Quizzes Section -->
            <div class="section-block" style="border-left: 4px solid #9b59b6;">
                <div class="section-header">
                    <span><ion-icon name="help-circle-outline" style="vertical-align: middle;"></ion-icon> Course Quizzes</span>
                </div>
                <div class="lesson-list">
                    <?php if(empty($quizzes)): ?>
                        <div style="text-align: center; padding: 10px; font-size: 0.9rem; color: #999;">No quizzes added yet.</div>
                    <?php else: ?>
                        <?php foreach ($quizzes as $quiz): ?>
                            <div class="lesson-item">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <ion-icon name="checkmark-done-circle-outline" style="color: #9b59b6;"></ion-icon>
                                    <div>
                                        <strong><?= htmlspecialchars($quiz['title']) ?></strong>
                                        <span style="font-size: 0.8rem; color: #888; margin-left: 5px;">Pass: <?= $quiz['passing_score'] ?>%</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="/academy/quiz/delete/<?= $quiz['id'] ?>" onclick="return confirm('Delete quiz?')" style="color: #aaa; font-size: 1.2rem;"><ion-icon name="trash-outline"></ion-icon></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <a href="/academy/quiz/create/<?= $course['id'] ?>" class="btn" style="display:block; text-align:center; margin-top: 10px; background: transparent; border: 1px dashed #9b59b6; color: #9b59b6;">+ Add Quiz</a>
                </div>
            </div>

            <!-- Add Section Button -->
            <div style="margin-top: 20px; text-align: center;">
                 <form action="/academy/instructor/section/add" method="POST" style="display: inline-block;">
                     <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                     <input type="text" name="title" placeholder="New Section Title" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 300px;">
                     <button type="submit" class="btn">Add Section</button>
                 </form>
            </div>
        </div>

        <!-- Settings View (Hidden by Default) -->
        <div class="builder-main hidden" id="settings-view">
            <h3>Course Settings</h3>
            <p style="margin-bottom: 20px; color: #666;">Update course details, branding, and certificate options.</p>

            <form action="/academy/instructor/course/update" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                <input type="hidden" name="id" value="<?= $course['id'] ?>">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color:#333; font-weight: bold;">Price (₦)</label>
                    <input type="number" name="price" value="<?= htmlspecialchars($course['price'] ?? 0) ?>" step="0.01" min="0" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; color: #333;">
                    <small style="color: #666;">Set to 0 for free courses.</small>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color:#333; font-weight: bold;">Course Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; color: #333;">
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="color:#333; font-weight: bold; margin-bottom: 10px; display:block;">Course Thumbnail</label>
                    
                    <!-- Unified File Picker -->
                    <div class="file-picker-widget" style="border: 2px dashed #ddd; padding: 20px; border-radius: 8px; text-align: center; background: #fafafa;">
                        
                        <!-- Preview Area -->
                        <div id="thumb-preview-area" style="margin-bottom: 15px; <?= empty($course['thumbnail']) ? 'display:none;' : '' ?>">
                            <img id="thumb-preview-img" src="<?= htmlspecialchars($course['thumbnail'] ?? '') ?>" style="max-height: 150px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="margin-top: 5px;">
                                <button type="button" onclick="clearSelection('thumb')" class="btn" style="padding: 2px 8px; font-size: 0.8rem; background: #e74c3c; color: white;">Remove</button>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="picker-actions" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <!-- 1. Computer -->
                            <button type="button" onclick="triggerFileInput('thumb')" class="btn" style="background: white; border: 1px solid #ddd; color: #333; display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="laptop-outline"></ion-icon> Upload from Computer
                            </button>
                            <!-- 2. Cloud -->
                            <button type="button" onclick="openCloudPicker('thumb')" class="btn" style="background: white; border: 1px solid #ddd; color: #333; display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="cloud-upload-outline"></ion-icon> Choose from Cloud
                            </button>
                        </div>

                        <!-- Hidden Real Inputs -->
                        <input type="file" id="thumb-file-input" name="thumbnail_file" accept="image/*" style="display: none;" onchange="handleFileSelect(this, 'thumb')">
                        <input type="hidden" id="thumb-url-input" name="thumbnail" value="<?= htmlspecialchars($course['thumbnail'] ?? '') ?>" onchange="updatePreviewFromUrl(this, 'thumb')">
                    </div>
                </div>

                <!-- Instructor Signature -->
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="color:#333; font-weight: bold; margin-bottom: 10px; display:block;">Instructor Signature</label>
                    <p style="font-size: 0.85rem; color: #666; margin-top: -5px; margin-bottom: 10px;">For Certificates</p>
                    
                    <div class="file-picker-widget" style="border: 2px dashed #ddd; padding: 20px; border-radius: 8px; text-align: center; background: #fafafa;">
                         <!-- Preview Area -->
                        <div id="sig-preview-area" style="margin-bottom: 15px; <?= empty($course['instructor_signature']) ? 'display:none;' : '' ?>">
                            <img id="sig-preview-img" src="<?= htmlspecialchars($course['instructor_signature'] ?? '') ?>" style="max-height: 80px; border: 1px solid #eee; background: white; padding: 5px;">
                            <div style="margin-top: 5px;">
                                <button type="button" onclick="clearSelection('sig')" class="btn" style="padding: 2px 8px; font-size: 0.8rem; background: #e74c3c; color: white;">Remove</button>
                            </div>
                        </div>

                         <!-- Buttons -->
                        <div class="picker-actions" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <button type="button" onclick="triggerFileInput('sig')" class="btn" style="background: white; border: 1px solid #ddd; color: #333; display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="laptop-outline"></ion-icon> Computer
                            </button>
                            <button type="button" onclick="openCloudPicker('sig')" class="btn" style="background: white; border: 1px solid #ddd; color: #333; display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="cloud-upload-outline"></ion-icon> Cloud
                            </button>
                        </div>

                         <!-- Hidden Inputs -->
                        <input type="file" id="sig-file-input" name="signature_file" accept="image/*" style="display: none;" onchange="handleFileSelect(this, 'sig')">
                        <input type="hidden" id="sig-url-input" name="signature_url" value="<?= htmlspecialchars($course['instructor_signature'] ?? '') ?>" onchange="updatePreviewFromUrl(this, 'sig')">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color:#333; font-weight: bold;">Description</label>
                    <div id="quill-editor" style="background: white; border: 1px solid #ddd; border-radius: 5px; height: 300px;"></div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn" style="padding: 12px 30px; font-size: 1rem;">Save Changes</button>
                    <button type="button" onclick="showView('curriculum')" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333; margin-left: 10px;">Cancel</button>
                </div>
            </form>
        </div>

    </main>
    
    <!-- CLOUD PICKER MODAL -->
    <div id="cloudPickerModal" class="modal" style="z-index: 9999;">
        <div class="modal-content" style="max-width: 800px; height: 80vh; display: flex; flex-direction: column;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:10px;">
                <h3 style="margin:0; color:#333;">Select from Cloud</h3>
                <button type="button" onclick="closeCloudPicker()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div id="cloud-loading" style="text-align:center; padding: 20px;">Loading images...</div>
            <div id="cloud-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; overflow-y: auto; flex: 1; padding: 10px;">
                <!-- Images Loaded Here -->
            </div>
        </div>
    </div>
    
    <script>
    // ... Existing Scripts ...
    
    // --- File Picker Logic ---
    let currentPickerTarget = null; // 'thumb' or 'sig'

    function triggerFileInput(target) {
        document.getElementById(target + '-file-input').click();
    }

    function toggleUrlInput(target) {
        const el = document.getElementById(target + '-url-container');
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }

    function handleFileSelect(input, target) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(target + '-preview-img');
                img.src = e.target.result;
                document.getElementById(target + '-preview-area').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updatePreviewFromUrl(input, target) {
        const url = input.value;
        if(url) {
            const img = document.getElementById(target + '-preview-img');
            img.src = url;
            document.getElementById(target + '-preview-area').style.display = 'block';
        }
    }

    function clearSelection(target) {
        document.getElementById(target + '-file-input').value = ''; // clear file
        document.getElementById(target + '-url-input').value = ''; // clear url
        document.getElementById(target + '-preview-img').src = '';
        document.getElementById(target + '-preview-area').style.display = 'none';
        
        // Also clear legacy hidden inputs if any? NO, we rely on these names.
        // NOTE: If user clears, we send empty fields, controller should handle.
    }

    // --- Cloud Picker Logic ---
    function openCloudPicker(target) {
        currentPickerTarget = target;
        document.getElementById('cloudPickerModal').style.display = 'flex';
        loadCloudImages();
    }

    function closeCloudPicker() {
        document.getElementById('cloudPickerModal').style.display = 'none';
    }

    function loadCloudImages() {
        const grid = document.getElementById('cloud-grid');
        const loader = document.getElementById('cloud-loading');
        
        // Avoid reload if already populated? Nah, refresh is good.
        grid.innerHTML = '';
        loader.style.display = 'block';

        fetch('/cloud/api/images')
            .then(res => res.json())
            .then(data => {
                loader.style.display = 'none';
                if(data.success && data.files.length > 0) {
                    data.files.forEach(file => {
                        const div = document.createElement('div');
                        div.style.cssText = 'cursor: pointer; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; position: relative; aspect-ratio: 1;';
                        div.innerHTML = `<img src="${file.thumbnail}" style="width:100%; height:100%; object-fit: cover;">`;
                        div.onclick = () => selectCloudImage(file.url);
                        grid.appendChild(div);
                    });
                } else {
                    grid.innerHTML = '<p style="text-align:center; color:#666; width:100%;">No images found in cloud.</p>';
                }
            })
            .catch(err => {
                loader.style.display = 'none';
                grid.innerHTML = '<p style="color:red; text-align:center;">Error loading images.</p>';
            });
    }

    function selectCloudImage(url) {
        // Use the URL input to store the cloud URL
        const urlInput = document.getElementById(currentPickerTarget + '-url-input');
        urlInput.value = url;
        
        // Trigger update
        updatePreviewFromUrl(urlInput, currentPickerTarget);
        
        // Clear file input so it doesn't override
        document.getElementById(currentPickerTarget + '-file-input').value = '';
        
        closeCloudPicker();
    }
    </script>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });

    // Load existing content
    quill.root.innerHTML = <?= json_encode($course['description'] ?? '') ?>;

    // Sync content to hidden input on form submit
    document.querySelector('form[action="/academy/instructor/course/update"]').onsubmit = function() {
        var content = document.querySelector('#hiddenDescription');
        content.value = quill.root.innerHTML;
        if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
             content.value = '';
        }
    };
    </script>

    <!-- Add Lesson Modal -->
    <div id="lessonModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: #333;">Add New Lesson</h3>
            <form action="/academy/instructor/lesson/add" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="hidden" name="section_id" id="modalSectionId">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color:#333">Lesson Title</label>
                    <input type="text" name="title" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color:#333">Content Type</label>
                    <select name="type" id="lessonType" onchange="toggleInputs()" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="text">Text Article</option>
                        <option value="video">Video Upload</option>
                        <option value="pdf">PDF Document</option>
                    </select>
                </div>

                <div id="input-text" class="type-input">
                    <label style="color:#333; margin-bottom: 10px; display: block;">Lesson Content</label>
                    <?php 
                        $editorName = 'content';
                        $editorValue = ''; // Empty for new lessons
                        require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                    ?>
                </div>

                <div id="input-file" class="type-input hidden">
                    <label style="color:#333">Upload File (Video/PDF)</label>
                    <input type="file" name="lesson_file">
                </div>

                 <div id="input-url" class="type-input hidden">
                    <label style="color:#333">Or Video URL (YouTube/Vimeo)</label>
                    <input type="text" name="video_url" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal()" class="btn" style="background: #ccc; color: #333;">Cancel</button>
                    <button type="submit" class="btn">Add Lesson</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function showView(viewName) {
        // Toggle Nav Active State
        document.querySelectorAll('.acad-link').forEach(el => el.classList.remove('acad-active'));
        document.getElementById('nav-' + viewName).classList.add('acad-active');

        // Toggle Views
        if(viewName === 'settings') {
            document.getElementById('curriculum-view').classList.add('hidden');
            document.getElementById('settings-view').classList.remove('hidden');
        } else {
            document.getElementById('curriculum-view').classList.remove('hidden');
            document.getElementById('settings-view').classList.add('hidden');
        }
    }

    function openLessonModal(sectionId) {
        document.getElementById('modalSectionId').value = sectionId;
        document.getElementById('lessonModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('lessonModal').style.display = 'none';
    }
    // Toggle Inputs function...
    function toggleInputs() {
        const type = document.getElementById('lessonType').value;
        document.querySelectorAll('.type-input').forEach(el => el.classList.add('hidden'));

        if(type === 'text') document.getElementById('input-text').classList.remove('hidden');
        if(type === 'video') {
            document.getElementById('input-file').classList.remove('hidden');
            document.getElementById('input-url').classList.remove('hidden');
        }
        if(type === 'pdf') document.getElementById('input-file').classList.remove('hidden');
    }

    // Initialize SortableJS
    document.addEventListener('DOMContentLoaded', function() {
        // Sections Sortable
        const sectionsContainer = document.getElementById('sections-container');
        if (sectionsContainer) {
            new Sortable(sectionsContainer, {
                handle: '.section-handle',
                animation: 150,
                onEnd: function (evt) {
                    const order = Array.from(sectionsContainer.children).map(el => el.getAttribute('data-id'));
                    fetch('/academy/instructor/sort-sections', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({order: order})
                    });
                }
            });
        }

        // Lessons Sortable
        document.querySelectorAll('.lesson-list').forEach(function(list) {
            new Sortable(list, {
                group: 'shared', // Allows dragging lessons between sections
                handle: '.lesson-handle',
                animation: 150,
                onEnd: function (evt) {
                    const parentList = evt.to;
                    const sectionId = parentList.getAttribute('data-section-id');
                    
                    const order = Array.from(parentList.querySelectorAll('.lesson-item')).map(el => {
                        return {
                            id: el.getAttribute('data-id'),
                            section_id: sectionId
                        };
                    });
                    
                    fetch('/academy/instructor/sort-lessons', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({order: order})
                    });
                }
            });
        });
    });
</script>

</body>
</html>


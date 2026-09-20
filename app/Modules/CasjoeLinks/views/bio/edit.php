<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bio Page | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --brand-primary: #000066;
            --brand-accent: #FFA600;
        }
        .main-content { background: #f8fafc; min-height: 100vh; padding: 0; }
        
        /* Two Column Editor Layout */
        .editor-container {
            display: flex; height: 100vh;
        }
        
        /* LEFT: Settings Panel (Now a Form) */
        .settings-panel {
            width: 400px; background: white; border-right: 1px solid #e2e8f0;
            overflow-y: auto; display: flex; flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            z-index: 10;
        }
        
        .panel-header {
            padding: 20px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);
            position: sticky; top: 0; z-index: 20;
        }
        .panel-title { font-weight: 700; color: var(--brand-primary); font-size: 18px; }

        .panel-content { padding: 20px; flex: 1; }
        
        .block-list { margin-top: 20px; }
        .block-item {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 15px; margin-bottom: 10px; cursor: move;
            transition: 0.2s; position: relative;
        }
        .block-item:hover { border-color: var(--brand-accent); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .block-handle { color: #cbd5e1; margin-right: 10px; cursor: grab; }
        
        .add-block-btn {
            width: 100%; border: 2px dashed #cbd5e1; background: transparent;
            padding: 15px; border-radius: 10px; color: #64748b; font-weight: 600;
            cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .add-block-btn:hover { border-color: var(--brand-primary); color: var(--brand-primary); background: #f8fafc; }

        /* RIGHT: Preview (Phone Mockup) */
        .preview-area {
            flex: 1; display: flex; align-items: center; justify-content: center;
            background: #f1f5f9; background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px; overflow: hidden;
        }
        
        .phone-mockup {
            width: 360px; height: 740px;
            background: #fff; border-radius: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 12px solid #1e293b;
            position: relative; overflow: hidden;
            transform: scale(0.9);
        }
        .phone-notch {
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 120px; height: 30px; background: #1e293b;
            border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;
            z-index: 20;
        }
        
        .preview-iframe {
            width: 100%; height: 100%; border: none;
            background: white;
        }

        /* Form Inputs */
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; }
        .form-control {
            width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;
            font-size: 14px; margin-bottom: 15px;
            color: #1e293b; background: white;
        }
        .form-control:focus { outline: none; border-color: var(--brand-primary); }
    </style>
</head>
<body style="overflow: hidden;"> <!-- Prevent body scroll -->
    
    <div style="position: fixed; top: 20px; left: 20px; z-index: 100;">
        <a href="/links/bio" class="btn" style="background: white; color: #333; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px;">
            <ion-icon name="arrow-back"></ion-icon> Dashboard
        </a>
    </div>

    <div class="editor-container">
        <!-- SETTINGS PANEL -->
        <form class="settings-panel" id="editForm" action="/links/bio/update" method="POST" enctype="multipart/form-data">
            <div class="panel-header">
                <span class="panel-title">Editor</span>
                <button type="submit" class="btn btn-sm" style="background: var(--brand-primary); color: #FFA600; border: 1px solid rgba(255,255,255,0.1); font-weight: bold;">Save Changes</button>
            </div>
            
            <div class="panel-content">
                <?php $settings = json_decode($page['settings'] ?? '{}', true); ?>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                <input type="hidden" name="id" value="<?= $page['id'] ?>">

                <!-- Tabs -->
                <div style="display: flex; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px;">
                    <button type="button" class="tab-btn active" onclick="switchTab('links')" style="padding: 10px; border:none; background:none; border-bottom: 2px solid var(--brand-primary); font-weight:600;">Links</button>
                    <button type="button" class="tab-btn" onclick="switchTab('appearance')" style="padding: 10px; border:none; background:none; color: #64748b;">Appearance</button>
                </div>

                <!-- LINKS TAB -->
                <div id="tab-links">
                    <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                        <div class="add-block-btn" onclick="addLinkBlock()" style="flex: 1; min-width: 150px;">
                            <ion-icon name="add-circle"></ion-icon> Add New Link
                        </div>
                        <div class="add-block-btn" onclick="addSubscribeBlock()" style="flex: 1; min-width: 150px; border-color: #6366f1; color: #6366f1;">
                            <ion-icon name="mail-unread"></ion-icon> Add Newsletter Block
                        </div>
                        <div class="add-block-btn" onclick="addEmbedBlock()" style="flex: 1; min-width: 150px; border-color: #10b981; color: #10b981;">
                            <ion-icon name="videocam"></ion-icon> Add Media Embed
                        </div>
                    </div>
                    
                    <div id="blocksContainer" class="block-list">
                        <!-- Blocks will be injected here via JS -->
                    </div>
                </div>

                <!-- APPEARANCE TAB -->
                <div id="tab-appearance" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Theme</label>
                        <?php $currentThemeConfig = json_decode($page['theme_config'] ?? '{}', true); $currentThemeName = $currentThemeConfig['name'] ?? 'Custom/Default'; ?>
                        <div style="margin-bottom: 10px; font-size: 13px; color: #64748b;">Current: <strong><?= htmlspecialchars($currentThemeName) ?></strong></div>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 5px;">
                            <?php foreach ($templates as $key => $tpl): ?>
                            <div onclick="selectTheme('<?= $key ?>')" style="border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='var(--brand-primary)'" onmouseout="this.style.borderColor='#e2e8f0'">
                                <div style="height: 60px; background: <?= htmlspecialchars($tpl['bg_color']) ?>; padding: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px;">
                                    <div style="width: 80%; height: 10px; background: <?= htmlspecialchars($tpl['btn_bg']) ?>; border: <?= htmlspecialchars($tpl['border'] ?? 'none') ?>; border-radius: 10px;"></div>
                                    <div style="width: 80%; height: 10px; background: <?= htmlspecialchars($tpl['btn_bg']) ?>; border: <?= htmlspecialchars($tpl['border'] ?? 'none') ?>; border-radius: 10px;"></div>
                                </div>
                                <div style="padding: 6px; font-size: 11px; text-align: center; background: #f8fafc; font-weight: 600; color: #334155; border-top: 1px solid #e2e8f0;">
                                    <?= htmlspecialchars($tpl['name']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="template_key" id="templateKeyInput">
                        
                        <script>
                        function selectTheme(key) {
                            if(confirm("Apply this theme? This will overwrite your current background colors.")) {
                                document.getElementById('templateKeyInput').value = key;
                                document.getElementById('editForm').submit();
                            }
                        }
                        </script>
                        <small style="color: #64748b; font-size: 12px; display: block; margin-top: 5px;">Selecting a new theme will save changes immediately.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <?php if (!empty($settings['profile_image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img id="profilePreview" src="<?= htmlspecialchars($settings['profile_image']) ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div style="margin-bottom: 10px;">
                                <img id="profilePreview" src="" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; display: none;">
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; gap: 10px;">
                            <input type="file" name="profile_image" class="form-control" accept="image/*" style="margin-bottom:0;">
                            <button type="button" class="btn btn-sm" onclick="openCloudPicker()" style="background: #e0e7ff; color: #000066; border: 1px solid #c7d2fe; white-space: nowrap;">
                                <ion-icon name="cloud-upload"></ion-icon> Select from Cloud
                            </button>
                        </div>
                        <input type="hidden" name="profile_image_url" id="profileImageUrl">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Page Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Name</label>
                        <input type="text" name="profile_name" class="form-control" value="<?= htmlspecialchars($settings['profile_name'] ?? $page['title']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($settings['description'] ?? '') ?></textarea>
                    </div>

                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 15px;">🖼️ Banner Image</h4>

                    <div class="form-group">
                        <label class="form-label">Banner / Header Image</label>
                        <?php if (!empty($settings['banner_image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img id="bannerPreview" src="<?= htmlspecialchars($settings['banner_image']) ?>" style="width: 100%; max-height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                            </div>
                        <?php else: ?>
                            <div style="margin-bottom: 10px;">
                                <img id="bannerPreview" src="" style="width: 100%; max-height: 80px; object-fit: cover; border-radius: 8px; display: none;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="banner_image" class="form-control" accept="image/*">
                        <input type="hidden" name="banner_image_url" id="bannerImageUrl">
                    </div>

                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 15px;">🎨 Background Customization</h4>

                    <div class="form-group">
                        <label class="form-label">Background Color</label>
                        <input type="color" name="custom_bg_color" class="form-control" value="<?= htmlspecialchars($settings['custom_bg_color'] ?? '#b8c5b4') ?>" style="height: 40px; padding: 3px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Background Image (optional, overrides color)</label>
                        <?php if (!empty($settings['custom_bg_image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img id="bgPreview" src="<?= htmlspecialchars($settings['custom_bg_image']) ?>" style="width: 100%; max-height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="custom_bg_image" class="form-control" accept="image/*">
                        <input type="hidden" name="bg_image_url" id="bgImageUrl">
                    </div>
                </div>
                
                <!-- Hidden Input for Blocks JSON -->
                <input type="hidden" name="blocks" id="blocksJson">
                <input type="hidden" name="settings" id="settingsJson">
            </div>
        </form>

        <!-- PREVIEW AREA -->
        <div class="preview-area">
            <div class="phone-mockup">
                <div class="phone-notch"></div>
                <iframe src="/bio/<?= $page['slug'] ?>?t=<?= time() ?>" class="preview-iframe" id="previewFrame"></iframe>
            </div>
            <div style="position: absolute; bottom: 20px; color: #64748b; font-size: 0.9rem;">
                <ion-icon name="eye"></ion-icon> Live Preview
            </div>
        </div>
    </div>

    <script>
        // Initialize Blocks from Server
        let blocks = <?= $page['blocks'] ?: '[]' ?>;
        
        function renderBlocks() {
            const container = document.getElementById('blocksContainer');
            container.innerHTML = '';
            
            blocks.forEach((block, index) => {
                const div = document.createElement('div');
                div.className = 'block-item';
                
                if (block.type === 'subscribe') {
                    div.innerHTML = `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="font-weight: 600; font-size: 14px; color: #6366f1;"><ion-icon name="mail-unread"></ion-icon> Newsletter Subscribe Block</span>
                            <span style="color: #ef4444; cursor: pointer;" onclick="removeBlock(${index})"><ion-icon name="trash"></ion-icon></span>
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                             <label class="form-label">Title</label>
                             <input type="text" class="form-control" placeholder="Subscribe to my newsletter" value="${block.title || ''}" oninput="updateBlock(${index}, 'title', this.value)" style="margin-bottom:0;">
                        </div>
                        
                        <label class="form-label">Button Text</label>
                        <input type="text" class="form-control" placeholder="Subscribe" value="${block.button_text || ''}" oninput="updateBlock(${index}, 'button_text', this.value)" style="margin-bottom:0;">
                    `;
                } else if (block.type === 'embed') {
                    div.innerHTML = `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="font-weight: 600; font-size: 14px; color: #10b981;"><ion-icon name="videocam"></ion-icon> Media Embed (YouTube / Spotify)</span>
                            <span style="color: #ef4444; cursor: pointer;" onclick="removeBlock(${index})"><ion-icon name="trash"></ion-icon></span>
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                             <label class="form-label">Media URL</label>
                             <input type="url" class="form-control" placeholder="Paste YouTube or Spotify URL here" value="${block.url || ''}" oninput="updateBlock(${index}, 'url', this.value)" style="margin-bottom:0;">
                             <small style="color:#94a3b8;font-size:12px;">Supported: youtube.com, youtu.be, open.spotify.com</small>
                        </div>
                    `;
                } else {
                    // Icon Options
                    const icons = [
                        {val: 'globe-outline', label: 'Website'},
                        {val: 'logo-instagram', label: 'Instagram'},
                        {val: 'logo-facebook', label: 'Facebook'},
                        {val: 'logo-twitter', label: 'Twitter/X'},
                        {val: 'logo-linkedin', label: 'LinkedIn'},
                        {val: 'logo-youtube', label: 'YouTube'},
                        {val: 'logo-tiktok', label: 'TikTok'},
                        {val: 'logo-whatsapp', label: 'WhatsApp'},
                        {val: 'mail-outline', label: 'Email'}
                    ];
                    
                    let iconOptions = icons.map(i => 
                        `<option value="${i.val}" ${block.icon === i.val ? 'selected' : ''}>${i.label}</option>`
                    ).join('');

                    div.innerHTML = `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="font-weight: 600; font-size: 14px;">Link Block</span>
                            <span style="color: #ef4444; cursor: pointer;" onclick="removeBlock(${index})"><ion-icon name="trash"></ion-icon></span>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <div style="flex: 1;">
                                <label class="form-label">Icon</label>
                                <select class="form-control" onchange="updateBlock(${index}, 'icon', this.value)" style="margin-bottom:0;">
                                    ${iconOptions}
                                </select>
                            </div>
                            <div style="flex: 2;">
                                 <label class="form-label">Title</label>
                                 <input type="text" class="form-control" placeholder="Link Title" value="${block.title || ''}" oninput="updateBlock(${index}, 'title', this.value)" style="margin-bottom:0;">
                            </div>
                        </div>
                        
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" placeholder="https://..." value="${block.url || ''}" oninput="updateBlock(${index}, 'url', this.value)" style="margin-bottom:0;">
                    `;
                }
                container.appendChild(div);
            });
            
            // Update hidden input
            document.getElementById('blocksJson').value = JSON.stringify(blocks);
        }

        function addLinkBlock() {
            blocks.push({ type: 'link', title: '', url: '', icon: 'globe-outline' });
            renderBlocks();
        }

        function addSubscribeBlock() {
            blocks.push({ type: 'subscribe', title: 'Subscribe to my Newsletter', button_text: 'Subscribe' });
            renderBlocks();
        }

        function addEmbedBlock() {
            blocks.push({ type: 'embed', url: '' });
            renderBlocks();
        }

        function removeBlock(index) {
            if(confirm('Delete this link?')) {
                blocks.splice(index, 1);
                renderBlocks();
            }
        }

        function updateBlock(index, key, value) {
            blocks[index][key] = value;
            document.getElementById('blocksJson').value = JSON.stringify(blocks);
        }

        // Tab Switching
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.borderBottom = 'none';
                btn.style.fontWeight = 'normal';
                btn.style.color = '#64748b';
            });
            event.target.style.borderBottom = '2px solid var(--brand-primary)';
            event.target.style.fontWeight = '600';
            event.target.style.color = 'black';
            
            document.getElementById('tab-links').style.display = tab === 'links' ? 'block' : 'none';
            document.getElementById('tab-appearance').style.display = tab === 'appearance' ? 'block' : 'none';
        }

        // Init
        renderBlocks();
        
        // Auto-save settings on form submit
        document.getElementById('editForm').addEventListener('submit', function() {
             // Handle Settings aggregation if needed
        });

        // Cloud Picker Logic
        function openCloudPicker() {
            window.open('/cloud/picker', 'CasjoeCloud', 'width=800,height=600');
        }

        window.addEventListener('message', function(event) {
            if (event.data.type === 'fileSelected') {
                const url = event.data.url;
                document.getElementById('profileImageUrl').value = url;
                
                // Update Preview
                const img = document.getElementById('profilePreview');
                img.src = url;
                img.style.display = 'block';
                
                alert('Image selected from cloud!');
            }
        });
    </script>
</body>
</html>

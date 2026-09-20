<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Static Website | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin { animation: spin 1s linear infinite; }
        .code-textarea {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
            background: #090d16 !important;
            color: #38bdf8 !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 12px !important;
            padding: 16px !important;
            white-space: pre !important;
            overflow-x: auto !important;
            tab-size: 4 !important;
        }
        html.light-theme .code-textarea {
            background: #0f172a !important;
            color: #38bdf8 !important;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'static_sites'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <div>
                        <h1>Edit Website: <?= htmlspecialchars($site['subdomain']) ?></h1>
                        <p>Customize with AI prompts, edit the source code directly, or replace files.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="/sites/<?= htmlspecialchars($site['subdomain']) ?>/" target="_blank" class="cl-btn" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e !important;">
                            <ion-icon name="open-outline"></ion-icon> View Live Website
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['saved'])): ?>
                    <div class="cl-pill cl-pill-success" style="width: 100%; padding: 12px 18px; margin-bottom: 20px; font-size: 15px;">
                        <ion-icon name="checkmark-circle-outline" style="font-size: 20px;"></ion-icon> Website code & settings updated successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['ai_updated'])): ?>
                    <div class="cl-pill cl-pill-success" style="width: 100%; padding: 12px 18px; margin-bottom: 20px; font-size: 15px; background: rgba(255, 166, 0, 0.15); border-color: rgba(255, 166, 0, 0.4); color: #FFA600;">
                        <ion-icon name="sparkles-outline" style="font-size: 20px;"></ion-icon> AI has successfully refined and updated your website code!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="cl-error">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                        <div>
                            <?php 
                            if ($_GET['error'] === 'missing_fields') echo 'Please fill in all required fields.';
                            elseif ($_GET['error'] === 'invalid_name') echo 'Invalid website name. Use only lowercase letters, numbers, and hyphens.';
                            elseif ($_GET['error'] === 'name_taken') echo 'This website name is already taken.';
                            elseif ($_GET['error'] === 'empty_refine_prompt') echo 'Please enter instructions for the AI to update your site.';
                            else echo htmlspecialchars($_GET['error']);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                    
                    <!-- SECTION 1: AI Prompt Refinement -->
                    <div class="cl-form-card" style="max-width: 100%; border: 1px solid rgba(255, 166, 0, 0.3);">
                        <div class="cl-section" style="margin-top: 0;">
                            <ion-icon name="sparkles-outline" style="color: #FFA600;"></ion-icon>
                            <h2>✨ Edit Website with AI</h2>
                        </div>
                        <p style="color: var(--cl-text-muted); margin-bottom: 18px; font-size: 14px;">
                            Tell the AI what changes or additions you want to make. It will rewrite your website's HTML/CSS while keeping your existing content intact.
                        </p>

                        <form action="/links/static/ai-refine/<?= $site['id'] ?>" method="POST" id="aiRefineForm">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                            <div class="cl-form-group">
                                <label class="cl-label">Change Instructions</label>
                                <textarea name="refine_prompt" rows="3" class="cl-input" required placeholder="e.g., Change the main background to dark navy blue, add a 3-tier pricing section, and update the phone number to +234 800 000 0000."></textarea>
                            </div>

                            <div id="ai-refine-loading" style="display: none; background: rgba(255, 166, 0, 0.1); border: 1px solid rgba(255, 166, 0, 0.3); padding: 12px 16px; border-radius: var(--cl-radius-sm); margin-bottom: 16px; color: var(--cl-text);">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <ion-icon name="sync-outline" class="spin" style="font-size: 20px; color: var(--cl-accent);"></ion-icon>
                                    <span style="font-size: 14px;">AI is updating your website code... Please wait.</span>
                                </div>
                            </div>

                            <button type="submit" id="refineBtn" class="cl-btn" style="background: linear-gradient(135deg, #FFA600, #FFC107);">
                                <ion-icon name="sparkles-outline"></ion-icon> Apply AI Changes
                            </button>
                        </form>
                    </div>

                    <!-- SECTION 2: Code Editor & Settings -->
                    <div class="cl-form-card" style="max-width: 100%;">
                        <div class="cl-section" style="margin-top: 0;">
                            <ion-icon name="code-slash-outline"></ion-icon>
                            <h2>Code Editor & Site Settings</h2>
                        </div>

                        <form action="/links/static/update/<?= $site['id'] ?>" method="POST" enctype="multipart/form-data" id="updateForm">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">

                            <div class="cl-form-group">
                                <label class="cl-label">Website Name (URL Path)</label>
                                <div class="cl-input-group">
                                    <span class="cl-input-prefix">/sites/</span>
                                    <input type="text" name="subdomain" class="cl-input" value="<?= htmlspecialchars($site['subdomain']) ?>" required pattern="[a-z0-9-]+">
                                </div>
                            </div>

                            <?php if (!empty($htmlCode)): ?>
                                <div class="cl-form-group">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <label class="cl-label" style="margin: 0;">Source Code Editor (index.html)</label>
                                        <span class="cl-hint" style="margin: 0;">You can edit any HTML, text, or CSS directly here</span>
                                    </div>
                                    <textarea name="html_code" rows="18" class="cl-input code-textarea"><?= htmlspecialchars($htmlCode) ?></textarea>
                                </div>
                            <?php endif; ?>

                            <div class="cl-form-group">
                                <label class="cl-label">Replace with ZIP Upload <span class="optional">(Optional)</span></label>
                                <input type="file" name="site_file" class="cl-input" accept=".zip">
                                <div class="cl-hint">Upload a new ZIP file to replace all current site files.</div>
                            </div>

                            <div id="build-indicator" style="display:none; margin-bottom: 1rem; color: var(--cl-text-muted);">
                                <ion-icon name="sync-outline" class="spin" style="margin-right:0.5rem; vertical-align:-2px;"></ion-icon>
                                Uploading ZIP and processing files...
                            </div>

                            <div style="display:flex; gap:1rem; align-items:center; margin-top:2rem;">
                                <button type="submit" id="submit-btn" class="cl-btn">
                                    <ion-icon name="save-outline"></ion-icon> Save Code & Settings
                                </button>
                                <a href="/links/static" class="cl-back">Cancel & Back</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <script>
            document.getElementById('aiRefineForm').addEventListener('submit', function() {
                document.getElementById('refineBtn').disabled = true;
                document.getElementById('refineBtn').style.opacity = '0.6';
                document.getElementById('refineBtn').innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Applying Changes...';
                document.getElementById('ai-refine-loading').style.display = 'block';
            });

            document.getElementById('updateForm').addEventListener('submit', function(e) {
                const fileInput = document.querySelector('input[name="site_file"]');
                if (fileInput.files.length > 0) {
                    document.getElementById('submit-btn').disabled = true;
                    document.getElementById('submit-btn').style.opacity = '0.5';
                    document.getElementById('submit-btn').innerText = 'Processing ZIP...';
                    document.getElementById('build-indicator').style.display = 'block';
                }
            });
            </script>
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Social Post | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .composer-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 25px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .composer-layout {
                grid-template-columns: 1fr;
            }
        }
        .composer-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .preview-sticky {
            position: sticky;
            top: 20px;
        }
        .platform-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .platform-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: #475569;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }
        .platform-pill input[type="checkbox"] {
            display: none;
        }
        .platform-pill.selected {
            border-color: #FFA600 !important;
            background: rgba(255, 166, 0, 0.15) !important;
            color: #FFA600 !important;
            box-shadow: 0 0 12px rgba(255, 166, 0, 0.25) !important;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: var(--cl-text);
            font-size: 0.92rem;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--cl-border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: var(--cl-surface-input);
            color: var(--cl-text);
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #FFA600;
            box-shadow: 0 0 0 3px rgba(255,166,0,0.15);
        }
        textarea.form-control {
            min-height: 140px;
            resize: vertical;
        }
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }
        /* AI BOX */
        .ai-assistant-card {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            border: 1.5px solid #c4b5fd;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 22px;
        }
        .ai-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .ai-header h4 {
            margin: 0;
            color: #5b21b6;
            font-size: 14.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-ai-generate {
            background: #7c3aed;
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }
        .btn-ai-generate:hover {
            background: #6d28d9;
        }
        .ai-variations {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ai-var-card {
            background: #fff;
            border: 1px solid #ddd6fe;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
        }
        .ai-var-card:hover {
            border-color: #7c3aed;
            background: #faf5ff;
            transform: translateY(-1px);
        }
        /* PREVIEW CARD */
        .social-preview-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        }
        .preview-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .preview-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #000066;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .preview-content {
            padding: 16px;
            font-size: 14px;
            line-height: 1.55;
            color: #1e293b;
            min-height: 60px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .preview-media {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            display: none;
            border-top: 1px solid #f1f5f9;
        }
        .preview-link-card {
            margin: 0 16px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            background: #f8fafc;
            display: none;
        }
        .preview-link-card a {
            color: #000066;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: block;
        }
        .preview-actions {
            padding: 10px 16px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-around;
            color: #64748b;
            font-size: 18px;
        }
        /* SUBMIT BUTTONS */
        .btn-submit {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-schedule {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
        }
        .btn-schedule:hover {
            background: rgba(255, 255, 255, 0.18);
        }
        html.light-theme .btn-schedule {
            background: #000066;
            color: #ffffff !important;
            border-color: #000066;
        }
        .btn-publish {
            background: linear-gradient(135deg, #FFA600, #FFC107);
            color: #0f172a !important;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(255, 166, 0, 0.35);
        }
        .btn-publish:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(255, 166, 0, 0.45);
        }
        .composer-card,
        .social-preview-box {
            background: var(--cl-surface);
            border-color: var(--cl-border);
            backdrop-filter: blur(20px);
        }
        html.dark-theme .composer-card,
        html.dark-theme .social-preview-box {
            background: var(--cl-surface);
            border-color: var(--cl-border);
        }
        html.dark-theme .form-control {
            background: var(--cl-surface-input);
            border-color: var(--cl-border);
            color: #f8fafc;
        }
        html.dark-theme .form-group label,
        html.dark-theme .preview-content {
            color: #f8fafc;
        }
        html.dark-theme .platform-pill {
            background: rgba(255, 255, 255, 0.04);
            border-color: var(--cl-border);
            color: #cbd5e1;
        }
        html.dark-theme .ai-assistant-card {
            background: rgba(30, 27, 75, 0.6);
            border-color: rgba(99, 102, 241, 0.3);
        }
        html.dark-theme .ai-header h4 {
            color: #c7d2fe;
        }
        html.dark-theme .ai-var-card {
            background: rgba(15, 23, 42, 0.7);
            border-color: rgba(99, 102, 241, 0.25);
            color: #e0e7ff;
        }
        html.dark-theme .preview-header,
        html.dark-theme .preview-actions {
            border-color: var(--cl-border);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'social_planner'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="page-header" style="margin-bottom: 24px;">
                    <a href="/links/social" class="cl-link" style="font-size: 0.9rem; margin-bottom: 8px;">
                        <ion-icon name="arrow-back-outline"></ion-icon> Back to Social Planner
                    </a>
                    <h1 style="margin: 4px 0 0 0; font-size: 28px; font-weight: 800; color: var(--cl-text);">Compose Social Campaign</h1>
                    <p style="color: var(--cl-text-muted); margin: 6px 0 0 0; font-size: 0.95rem;">Publish or schedule posts directly across your connected channels with AI assistance</p>
                </div>

            <form method="POST" action="/links/social/store" id="postForm">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                
                <div class="composer-layout">
                    <!-- Left Column: Inputs -->
                    <div class="composer-card">
                        <!-- Select Channels -->
                        <div class="form-group">
                            <label>Target Social Channels</label>
                            <div class="platform-pills">
                                <label class="platform-pill selected">
                                    <input type="checkbox" name="platforms[]" value="facebook" checked>
                                    <ion-icon name="logo-facebook" style="color: #1877F2;"></ion-icon> Facebook
                                </label>
                                <label class="platform-pill selected">
                                    <input type="checkbox" name="platforms[]" value="instagram" checked>
                                    <ion-icon name="logo-instagram" style="color: #E1306C;"></ion-icon> Instagram
                                </label>
                                <label class="platform-pill">
                                    <input type="checkbox" name="platforms[]" value="twitter">
                                    <ion-icon name="logo-twitter"></ion-icon> X / Twitter
                                </label>
                                <label class="platform-pill">
                                    <input type="checkbox" name="platforms[]" value="linkedin">
                                    <ion-icon name="logo-linkedin" style="color: #0A66C2;"></ion-icon> LinkedIn
                                </label>
                                <label class="platform-pill">
                                    <input type="checkbox" name="platforms[]" value="tiktok">
                                    <ion-icon name="logo-tiktok"></ion-icon> TikTok
                                </label>
                            </div>
                        </div>

                        <!-- AI Assistant Card -->
                        <div class="ai-assistant-card">
                            <div class="ai-header">
                                <h4><ion-icon name="sparkles"></ion-icon> AI Copywriter Assistant</h4>
                                <button type="button" class="btn-ai-generate" id="openAiBtn">
                                    <ion-icon name="flash"></ion-icon> Generate Captions
                                </button>
                            </div>
                            <div id="aiInputs" style="display: none; margin-top: 10px;">
                                <input type="text" id="aiTopic" class="form-control" placeholder="What is this post about? (e.g. 50% Off Flash Sale, New Coaching Program)" style="margin-bottom: 8px;">
                                <button type="button" class="btn-ai-generate" id="runAiBtn" style="width: 100%; justify-content: center;">
                                    <ion-icon name="sparkles"></ion-icon> Generate 3 Variations
                                </button>
                            </div>
                            <div id="aiVariations" class="ai-variations" style="display: none;"></div>
                        </div>

                        <!-- Content Textarea -->
                        <div class="form-group">
                            <label for="postContent">Post Copy / Caption <span style="color: #dc3545;">*</span></label>
                            <textarea name="content" id="postContent" class="form-control" required placeholder="Write your engaging social copy here... Include hashtags and a call-to-action!"></textarea>
                            <div class="char-counter"><span id="charCount">0</span> characters</div>
                        </div>

                        <!-- Attach Link / Funnel -->
                        <div class="form-group">
                            <label>Attach Funnel or Bio Page</label>
                            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 10px;">
                                <select name="link_type" id="linkType" class="form-control">
                                    <option value="none">No Link</option>
                                    <option value="funnel">Sales Funnel</option>
                                    <option value="bio">Bio Page</option>
                                    <option value="custom">Custom URL</option>
                                </select>
                                
                                <div id="linkSelectorWrapper">
                                    <input type="url" name="link_url" id="linkUrl" class="form-control" placeholder="https://..." style="display: none;">
                                    
                                    <select id="funnelSelect" class="form-control" style="display: none;">
                                        <option value="">Choose a Funnel...</option>
                                        <?php foreach ($funnels as $f): ?>
                                            <option value="/f/<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select id="bioSelect" class="form-control" style="display: none;">
                                        <option value="">Choose a Bio Page...</option>
                                        <?php foreach ($bioPages as $b): ?>
                                            <option value="/@<?= $b['slug'] ?>"><?= htmlspecialchars($b['title']) ?> (/@<?= $b['slug'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Media URL -->
                        <div class="form-group">
                            <label for="mediaUrl">Image / Media URL (Optional)</label>
                            <input type="url" name="media_url" id="mediaUrl" class="form-control" placeholder="https://images.unsplash.com/... or image link">
                        </div>

                        <!-- Scheduling Mode -->
                        <div class="form-group" style="padding-top: 15px; border-top: 1px solid #e2e8f0;">
                            <label>Publication Timing</label>
                            <div style="display: flex; gap: 20px; margin-bottom: 12px;">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                    <input type="radio" name="timing_mode" value="now" checked> Publish Now
                                </label>
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                    <input type="radio" name="timing_mode" value="later"> Schedule for Later
                                </label>
                            </div>
                            
                            <div id="scheduleTimeWrapper" style="display: none;">
                                <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control" style="max-width: 280px;">
                                <small style="display: block; margin-top: 4px; color: #64748b;">Posts are published automatically via server cron</small>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div style="display: flex; gap: 10px; margin-top: 25px;">
                            <input type="hidden" name="status" id="formStatus" value="publish_now">
                            <button type="submit" class="btn-submit btn-publish" id="submitBtn">
                                <ion-icon name="paper-plane-outline"></ion-icon> Publish Immediately
                            </button>
                            <button type="button" class="btn-submit" id="saveDraftBtn" style="background: #f1f5f9; color: #475569;">
                                Save Draft
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Live Mock Preview -->
                    <div class="preview-sticky">
                        <h3 style="margin: 0 0 12px 0; font-size: 15px; color: #475569; font-weight: 600;">Live Feed Preview</h3>
                        <div class="social-preview-box">
                            <div class="preview-header">
                                <div class="preview-avatar">CJ</div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: #1e293b;">Your Brand</div>
                                    <div style="font-size: 12px; color: #94a3b8;">Just now &bull; <ion-icon name="globe-outline" style="vertical-align: middle;"></ion-icon></div>
                                </div>
                            </div>
                            <div class="preview-content" id="previewText">Your social copy will appear here live as you type or generate with AI...</div>
                            <img id="previewMedia" class="preview-media" src="" alt="Media Preview">
                            <div id="previewLinkCard" class="preview-link-card">
                                <a href="#" id="previewLinkTitle">Attached Funnel / Page</a>
                                <span id="previewLinkDomain" style="font-size: 11px; color: #64748b;">casjoe.com</span>
                            </div>
                            <div class="preview-actions">
                                <ion-icon name="heart-outline"></ion-icon>
                                <ion-icon name="chatbubble-outline"></ion-icon>
                                <ion-icon name="share-social-outline"></ion-icon>
                                <ion-icon name="bookmark-outline"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </main>
    </div>

    <script>
        // Platform pill toggle
        document.querySelectorAll('.platform-pill').forEach(pill => {
            pill.addEventListener('click', function(e) {
                const cb = this.querySelector('input[type="checkbox"]');
                if (e.target !== cb) {
                    cb.checked = !cb.checked;
                }
                if (cb.checked) {
                    this.classList.add('selected');
                } else {
                    this.classList.remove('selected');
                }
            });
        });

        // Content & preview sync
        const postContent = document.getElementById('postContent');
        const charCount = document.getElementById('charCount');
        const previewText = document.getElementById('previewText');
        
        postContent.addEventListener('input', function() {
            const val = this.value;
            charCount.textContent = val.length;
            previewText.textContent = val.trim() ? val : 'Your social copy will appear here live as you type or generate with AI...';
        });

        // Media sync
        const mediaUrlInput = document.getElementById('mediaUrl');
        const previewMedia = document.getElementById('previewMedia');
        mediaUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                previewMedia.src = url;
                previewMedia.style.display = 'block';
            } else {
                previewMedia.style.display = 'none';
            }
        });

        // Link selector sync
        const linkType = document.getElementById('linkType');
        const linkUrl = document.getElementById('linkUrl');
        const funnelSelect = document.getElementById('funnelSelect');
        const bioSelect = document.getElementById('bioSelect');
        const previewLinkCard = document.getElementById('previewLinkCard');
        const previewLinkTitle = document.getElementById('previewLinkTitle');

        function updateLinkDisplay() {
            const type = linkType.value;
            linkUrl.style.display = 'none';
            funnelSelect.style.display = 'none';
            bioSelect.style.display = 'none';
            previewLinkCard.style.display = 'none';

            if (type === 'custom') {
                linkUrl.style.display = 'block';
                if (linkUrl.value) {
                    previewLinkTitle.textContent = linkUrl.value;
                    previewLinkCard.style.display = 'block';
                }
            } else if (type === 'funnel') {
                funnelSelect.style.display = 'block';
                if (funnelSelect.value) {
                    linkUrl.value = window.location.origin + funnelSelect.value;
                    previewLinkTitle.textContent = funnelSelect.options[funnelSelect.selectedIndex].text;
                    previewLinkCard.style.display = 'block';
                }
            } else if (type === 'bio') {
                bioSelect.style.display = 'block';
                if (bioSelect.value) {
                    linkUrl.value = window.location.origin + bioSelect.value;
                    previewLinkTitle.textContent = bioSelect.options[bioSelect.selectedIndex].text;
                    previewLinkCard.style.display = 'block';
                }
            } else {
                linkUrl.value = '';
            }
        }

        linkType.addEventListener('change', updateLinkDisplay);
        linkUrl.addEventListener('input', function() {
            if (this.value) {
                previewLinkTitle.textContent = this.value;
                previewLinkCard.style.display = 'block';
            } else {
                previewLinkCard.style.display = 'none';
            }
        });
        funnelSelect.addEventListener('change', function() {
            linkUrl.value = window.location.origin + this.value;
            previewLinkTitle.textContent = this.options[this.selectedIndex].text;
            previewLinkCard.style.display = 'block';
        });
        bioSelect.addEventListener('change', function() {
            linkUrl.value = window.location.origin + this.value;
            previewLinkTitle.textContent = this.options[this.selectedIndex].text;
            previewLinkCard.style.display = 'block';
        });

        // Timing mode
        const timingRadios = document.querySelectorAll('input[name="timing_mode"]');
        const scheduleTimeWrapper = document.getElementById('scheduleTimeWrapper');
        const formStatus = document.getElementById('formStatus');
        const submitBtn = document.getElementById('submitBtn');

        timingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'later') {
                    scheduleTimeWrapper.style.display = 'block';
                    formStatus.value = 'scheduled';
                    submitBtn.className = 'btn-submit btn-schedule';
                    submitBtn.innerHTML = '<ion-icon name="calendar-outline"></ion-icon> Schedule Post';
                } else {
                    scheduleTimeWrapper.style.display = 'none';
                    formStatus.value = 'publish_now';
                    submitBtn.className = 'btn-submit btn-publish';
                    submitBtn.innerHTML = '<ion-icon name="paper-plane-outline"></ion-icon> Publish Immediately';
                }
            });
        });

        document.getElementById('saveDraftBtn').addEventListener('click', function() {
            formStatus.value = 'draft';
            document.getElementById('postForm').submit();
        });

        // AI Assistant
        const openAiBtn = document.getElementById('openAiBtn');
        const aiInputs = document.getElementById('aiInputs');
        const aiTopic = document.getElementById('aiTopic');
        const runAiBtn = document.getElementById('runAiBtn');
        const aiVariations = document.getElementById('aiVariations');

        openAiBtn.addEventListener('click', function() {
            aiInputs.style.display = aiInputs.style.display === 'none' ? 'block' : 'none';
        });

        runAiBtn.addEventListener('click', async function() {
            const topic = aiTopic.value.trim();
            if (!topic) {
                alert('Please enter a topic or promotion goal.');
                return;
            }

            runAiBtn.disabled = true;
            runAiBtn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Generating...';
            
            try {
                const formData = new FormData();
                formData.append('topic', topic);
                formData.append('platform', 'all');
                formData.append('link_url', linkUrl.value || '');

                const resp = await fetch('/links/social/ai-generate', {
                    method: 'POST',
                    body: formData
                });
                const data = await resp.json();

                aiVariations.innerHTML = '';
                if (data.variations && data.variations.length > 0) {
                    data.variations.forEach((v, idx) => {
                        const card = document.createElement('div');
                        card.className = 'ai-var-card';
                        const hashtags = (v.hashtags || []).join(' ');
                        card.innerHTML = `<strong>Option ${idx+1}:</strong> ${v.text} <span style="color:#7c3aed;">${hashtags}</span> <div style="font-size:11px; color:#64748b; margin-top:4px;">Click to use this copy &rarr;</div>`;
                        card.addEventListener('click', function() {
                            postContent.value = v.text + (hashtags ? '\n\n' + hashtags : '');
                            postContent.dispatchEvent(new Event('input'));
                        });
                        aiVariations.appendChild(card);
                    });
                    aiVariations.style.display = 'flex';
                } else {
                    aiVariations.innerHTML = '<div style="font-size:13px; color:#ef4444;">Could not generate variations. Please try again.</div>';
                    aiVariations.style.display = 'block';
                }
            } catch (err) {
                alert('AI generation failed. Please check network.');
            } finally {
                runAiBtn.disabled = false;
                runAiBtn.innerHTML = '<ion-icon name="sparkles"></ion-icon> Generate 3 Variations';
            }
        });
    </script>
</body>
</html>

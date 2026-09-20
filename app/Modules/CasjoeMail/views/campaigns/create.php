<?php $active = 'mail_campaigns'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign | Casjoe Mail</title>
    <meta name="description" content="Create a new Casjoe Mail campaign">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css?v=2.2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .editor-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 25px;
            margin-top: 20px;
        }
        @media(max-width: 992px) {
            .editor-layout {
                grid-template-columns: 1fr;
            }
        }
        .setup-panel {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.02);
            height: fit-content;
        }
        .canvas-panel {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 180px);
            min-height: 500px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: #f8f9fc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            color: #030014;
            font-size: 0.95rem;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #000066;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0,0,102,0.1);
        }
        .ai-assistant {
            background: #f0f4ff;
            border-left: 4px solid #000066;
            padding: 20px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .ai-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #000066;
            font-weight: 800;
            margin-bottom: 15px;
            font-size: 1.05rem;
        }
        .ai-title ion-icon {
            color: var(--erp-gold);
            font-size: 1.3rem;
        }
        textarea.editor-textarea {
            flex: 1;
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.95rem;
            resize: none;
            background: #f8f9fc;
            color: #333;
            transition: border 0.3s;
        }
        textarea.editor-textarea:focus {
            outline: none;
            border-color: #000066;
            background: #ffffff;
        }
        .action-btn {
            background: var(--erp-gold);
            color: #030014;
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            font-weight: 800;
            font-size: 0.95rem;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.2s, background 0.2s;
        }
</style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Sidebar Toggle -->
        <?php include dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
        
        <!-- Sidebar -->
        <?php include dirname(__DIR__) . '/partials/sidebar_mail.php'; ?>

        <!-- Main Content -->
        <main class="erp-main">
            
            <!-- Header Area -->
            <section class="erp-hero">
        <div>
            <h1 style="margin-bottom: 5px;">Create Campaign</h1>
            <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Design and configure your new email broadcast.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="hero-btn" onclick="sendTestEmail()" style="border-radius: 10px; padding: 12px 24px; font-size: 1rem; background: #eef2ff; color: #000066; border: 1px solid #000066;">
                <ion-icon name="paper-plane-outline"></ion-icon> Send Test
            </button>
            <button class="hero-btn" onclick="saveCampaign()" style="border-radius: 10px; padding: 12px 24px; font-size: 1rem;">
                <ion-icon name="send"></ion-icon> Save & Send
            </button>
        </div>
    </section>

            <div class="erp-content">
                <div class="editor-layout">
                    
                    <!-- Left Panel: Settings & AI -->
                    <div class="setup-panel">
                        <div class="form-group">
                            <label>Campaign Name</label>
                            <input type="text" id="campName" class="form-control" placeholder="e.g. Summer Sale Newsletter">
                        </div>
                        
                        <div class="form-group">
                            <label>Sender Profile</label>
                            <select class="form-control" id="senderProfile">
                                <option value="">Default (System SMTP)</option>
                                <!-- Profiles would be loaded from DB here -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Subject</label>
                            <input type="text" id="emailSubject" class="form-control" placeholder="E.g. Your Exclusive Offer Inside!">
                        </div>
                        
                        <div class="form-group">
                            <label>Preview Text</label>
                            <input type="text" id="previewText" class="form-control" placeholder="A short summary that follows the subject line...">
                        </div>
                        
                        <div class="form-group">
                            <label>Start From Template</label>
                            <select class="form-control" onchange="loadTemplate(this.value)">
                                <option value="">-- Blank Canvas --</option>
                                <?php if(isset($templates) && is_array($templates)): ?>
                                    <?php foreach ($templates as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="ai-assistant">
                            <div class="ai-title">
                                <ion-icon name="sparkles"></ion-icon> AI Writer
                            </div>
                            <textarea id="aiPrompt" class="form-control" style="height: 100px; margin-bottom: 15px; resize: none;" placeholder="E.g. Write a friendly welcome email for new Casjoe subscribers..."></textarea>
                            <button class="action-btn" onclick="generateContent()" id="aiBtn">
                                <ion-icon name="color-wand-outline"></ion-icon> Generate Copy
                            </button>
                        </div>
                    </div>

                    <!-- Right Panel: HTML Editor -->
                    <div class="canvas-panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; border-bottom: 1px solid #000066; background: #000066;">
                            <h3 style="margin: 0; color: #ffffff; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                                <ion-icon name="document-text-outline" style="color: #ffa600;"></ion-icon> Casjoe Visual Editor
                            </h3>
                            <button onclick="toggleSource()" id="sourceToggleBtn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; padding: 5px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 0.85rem; transition: all 0.2s;">
                                View Source
                            </button>
                        </div>
                        
                        
                        <!-- Casjoe Visual Editor Component -->
                        <?php 
                            $editorName = 'content';
                            $editorValue = '';
                            require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function loadTemplate(id) {
            if(!id) {
                setEditorContent('');
                return;
            }
            try {
                const res = await fetch('/mail/template/get?id=' + id);
                const text = await res.text();
                setEditorContent(text);
            } catch (e) {
                console.error("Failed to load template");
            }
        }

        async function generateContent() {
            const prompt = document.getElementById('aiPrompt').value;
            const btn = document.getElementById('aiBtn');
            
            if(!prompt) {
                alert('Please describe what you want the AI to write.');
                return;
            }
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<ion-icon name="sync-outline" style="animation: spin 1s linear infinite;"></ion-icon> Generating...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('prompt', prompt);

                const res = await fetch('/mail/ai/generate', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if(data.content) {
                    setEditorContent(data.content);
                } else if(data.error) {
                    alert('AI Error: ' + data.error);
                }
            } catch(e) {
                alert('An error occurred during AI Generation.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function saveCampaign() {
            const name = document.getElementById('campName').value;
            const subject = document.getElementById('emailSubject').value;
            const htmlContent = getEditorContent();
            
            if(!name || !subject) {
                alert("Please enter a Campaign Name and Email Subject!");
                return;
            }
            
            console.log(htmlContent);
            alert('Campaign saved! Check console for HTML.');
        }

        function sendTestEmail() {
            const email = prompt("Enter email address to send test to:");
            if(email) {
                alert("Test email sent to " + email + "!");
            }
        }
    </script>
    <style>
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>

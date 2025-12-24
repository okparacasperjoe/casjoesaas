<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Campaign | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .editor-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            height: calc(100vh - 100px);
        }
        .tools-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-y: auto;
        }
        .preview-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .ai-box {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6fffa 100%);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #bbeeef;
            margin-bottom: 20px;
        }
        textarea.editor {
            width: 100%;
            height: 100%;
            border: none;
            resize: none;
            outline: none;
            font-family: monospace;
        }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/mail" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Mail Dashboard</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Create New Campaign</h2>
            <button class="btn" onclick="saveCampaign()">Save & Send</button>
        </div>

        <div class="editor-container">
            <div class="tools-panel">
                <div class="form-group">
                    <label>Campaign Name</label>
                    <input type="text" id="campName" class="form-control" placeholder="e.g. Weekly Newsletter">
                </div>
                
                <div class="form-group">
                    <label>Load Template</label>
                    <select class="form-control" onchange="loadTemplate(this.value)">
                        <option value="">-- Select Template --</option>
                        <?php foreach ($templates as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ai-box">
                    <label style="display: flex; align-items: center; gap: 5px; font-weight: bold; color: var(--primary);">
                        <ion-icon name="sparkles"></ion-icon> Gemini AI Assistant
                    </label>
                    <textarea id="aiPrompt" style="width: 100%; height: 80px; margin-top: 10px; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" placeholder="Describe what you want to write..."></textarea>
                    <button class="btn" style="width: 100%; margin-top: 10px; font-size: 0.9rem;" onclick="generateContent()">Generate Content</button>
                </div>
            </div>

            <div class="preview-panel">
                <textarea id="editorContent" class="editor" placeholder="Email HTML Content will appear here..."></textarea>
            </div>
        </div>

    </main>
</div>

<script>
    async function loadTemplate(id) {
        if(!id) return;
        const res = await fetch('/mail/template/get?id=' + id);
        const text = await res.text();
        document.getElementById('editorContent').value = text;
    }

    async function generateContent() {
        const prompt = document.getElementById('aiPrompt').value;
        const btn = document.querySelector('.ai-box button');
        
        if(!prompt) return alert('Please enter a prompt');
        
        btn.innerText = 'Generating...';
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
                // Determine insertion point or just append
                const editor = document.getElementById('editorContent');
                editor.value = data.content; // Simple replace for demo
            }
        } catch(e) {
            alert('AI Generation Failed');
        } finally {
            btn.innerText = 'Generate Content';
            btn.disabled = false;
        }
    }
</script>
</body>
</html>

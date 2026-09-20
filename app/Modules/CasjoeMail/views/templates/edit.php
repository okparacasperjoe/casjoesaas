<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Edit Template | Casjoe Mail</title>
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
            border: 1px solid var(--glass-border);
        }
        .preview-panel {
            background: white;
            padding: 0;
            border-radius: 10px;
            border: 1px solid #ccc;
            overflow: hidden;
        }
        .editor {
            width: 100%;
            height: 100%;
            border: none;
            padding: 20px;
            resize: none;
            outline: none;
            font-family: monospace;
            font-size: 14px;
        }
        .preview-frame {
            width: 100%;
            height: 100%;
            border: none;
            background: #fff;
        }
        .tab-btn {
            padding: 10px 20px;
            background: #f4f6f9;
            border: none;
            cursor: pointer;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        .tab-btn.active {
            background: white;
            border-bottom: none;
                <h2>Edit Template</h2>
                <div>
                     <a href="/mail/templates" class="btn" style="background: transparent; color: #333; border: 1px solid #ccc; margin-right: 10px;">Cancel</a>
                    <button type="submit" class="btn">Save Template</button>
                </div>
            </div>

            <div class="editor-container">
                <div class="tools-panel">
                    <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($template['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Default Subject</label>
                        <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($template['subject']) ?>" required>
                    </div>
                </div>

                <div class="preview-panel" style="display: flex; flex-direction: column;">
                     <div style="background: #f4f6f9; border-bottom: 1px solid #ccc;">
                        <button type="button" class="tab-btn active" onclick="switchTab('code')">HTML Code</button>
                        <button type="button" class="tab-btn" onclick="switchTab('preview')">Live Preview</button>
                    </div>
                    <div id="codeView" style="flex: 1;">
                        
                    <!-- Casjoe Visual Editor Component -->
                    <?php 
                        $editorName = 'content';
                        // The initial value is populated directly from PHP since we are in PHP
                        $editorValue = $template['content'] ?? '';
                        require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                    ?>
    
                    </div>
                    <div id="previewView" style="flex: 1; display: none;">
                        <iframe id="previewFrame" class="preview-frame"></iframe>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
    function switchTab(tab) {
        if(tab === 'code') {
            document.getElementById('codeView').style.display = 'block';
            document.getElementById('previewView').style.display = 'none';
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.querySelectorAll('.tab-btn')[1].classList.remove('active');
        } else {
            updatePreview();
            document.getElementById('codeView').style.display = 'none';
            document.getElementById('previewView').style.display = 'block';
            document.querySelectorAll('.tab-btn')[0].classList.remove('active');
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
        }
    }

    function updatePreview() {
        const html = document.getElementById('editorContent').value;
        document.getElementById('previewFrame').srcdoc = html;
    }
</script>
</body>
</html>

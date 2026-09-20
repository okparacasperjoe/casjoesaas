<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Edit Campaign | Casjoe Mail</title>
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
                <h2>Edit Campaign</h2>
                <div>
                     <a href="/mail/campaigns" class="btn" style="background: transparent; color: #333; border: 1px solid #ccc; margin-right: 10px;">Cancel</a>
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </div>

            <div class="editor-container">
                <div class="tools-panel">
                    <div class="form-group">
                        <label>Campaign Name</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($campaign['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Subject</label>
                        <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($campaign['subject']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Recipient List</label>
                        <select name="list_id" class="form-control" required>
                            <option value="">-- Select List --</option>
                            <?php foreach ($lists as $list): ?>
                                <option value="<?= $list['id'] ?>" <?= $campaign['list_id'] == $list['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($list['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    
                    <div class="form-group">
                         <label>Load Template (Overwrites Content)</label>
                         <select class="form-control" onchange="loadTemplate(this.value)">
                            <option value="">-- Select Template --</option>
                            <?php foreach ($templates as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="preview-panel">
                    
                    <!-- Casjoe Visual Editor Component -->
                    <?php 
                        $editorName = 'content';
                        // The initial value is populated directly from PHP since we are in PHP
                        $editorValue = $campaign['content'] ?? '';
                        require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                    ?>
    
                </div>
            </div>
        </form>
    </main>
</div>

<script>
    async function loadTemplate(id) {
        if(!id) return;
        if(!confirm('This will overwrite current content. Continue?')) return;
        
        const res = await fetch('/mail/template/get?id=' + id);
        const text = await res.text();
        document.getElementById('casjoe-workspace').innerHTML = text;
    }
</script>
</body>
</html>

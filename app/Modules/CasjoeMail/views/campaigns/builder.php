<!DOCTYPE html>
<!-- Cache Bust: <?= time() ?> -->
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Builder | Casjoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    
    <!-- GrapesJS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-preset-newsletter"></script>
    
    <style>
        :root { --primary-color: #000066; --accent-color: #FFA600; --bg-color: #f4f6f8; }
        body { background-color: var(--bg-color); height: 100vh; overflow: hidden; font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        
        /* Header */
        .builder-header { 
            height: 60px; 
            background: white; 
            border-bottom: 1px solid #ddd; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 20px; 
        }
        .back-link { text-decoration: none; color: #666; font-weight: 500; display: flex; align-items: center; gap: 5px; }
        
        /* Main Layout */
        #gjs {
            height: calc(100vh - 60px) !important;
            border: none;
        }
        
        /* Custom UI Tweaks for GrapesJS */
        .gjs-one-bg { background-color: white; color: #333; }
        .gjs-two-color { color: var(--primary-color); }
        .gjs-three-bg { background-color: var(--primary-color); color: white; }
        .gjs-four-color, .gjs-four-color-h:hover { color: var(--accent-color); }
        .gjs-pn-panel { border-bottom: 1px solid #ddd; box-shadow: none; }
        
        .gjs-pn-btn.gjs-pn-active {
            color: var(--accent-color);
            background-color: rgba(0,0,102, 0.1);
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="builder-header">
    <div class="d-flex align-items-center gap-3">
        <a href="/mail/campaigns" class="back-link"><i class="bi bi-arrow-left"></i> Exit</a>
        <div class="vr mx-2"></div>
        <input type="text" id="campaignName" class="form-control form-control-sm border-0 fw-bold" style="width: 200px; background: #f8f9fa;" value="Untitled Campaign">
        <input type="text" id="campaignSubject" class="form-control form-control-sm border-0" style="width: 300px; background: #f8f9fa;" placeholder="Subject Line...">
    </div>
    <div class="d-flex align-items-center gap-2">
        <button id="saveBtn" class="btn btn-primary btn-sm" onclick="saveCampaign()">Next: Review & Send <i class="bi bi-arrow-right"></i></button>
    </div>
</header>

<div id="gjs"></div>

<script>
    // Initialize GrapesJS
    const editor = grapesjs.init({
        container: '#gjs',
        fromElement: true,
        height: '100%',
        width: 'auto',
        storageManager: false, // We'll handle saving manually
        plugins: ['gjs-preset-newsletter'],
        pluginsOpts: {
            'gjs-preset-newsletter': {
                modalTitleImport: 'Import template',
            }
        },
    });
    
    // Add default blank template blocks if empty
    editor.on('load', () => {
        const wrapper = editor.DomComponents.getWrapper();
        if (wrapper.components().length === 0) {
            editor.setComponents(`
                <table class="main" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f6f8" style="background-color: #f4f6f8; min-height: 500px;">
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellspacing="0" cellpadding="20" border="0" bgcolor="#ffffff" style="background-color: #ffffff; margin-top: 40px; border-radius: 8px;">
                                <tr>
                                    <td align="center">
                                        <h1 style="color: #000066; font-family: sans-serif;">Hello!</h1>
                                        <p style="color: #555555; font-family: sans-serif; font-size: 16px; line-height: 1.5;">Drag and drop blocks from the right panel to build your email.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            `);
        }
    });

    // Save Campaign logic
    function saveCampaign() {
        const btn = document.getElementById('saveBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        btn.disabled = true;
        
        // Use GrapesJS inline HTML exporter for maximum email client compatibility
        try {
            const html = editor.runCommand('gjs-get-inlined-html');
            
            const name = document.getElementById('campaignName').value || 'Untitled Campaign';
            const subject = document.getElementById('campaignSubject').value || name;
            const listId = '1'; 

            const formData = new FormData();
            formData.append('name', name);
            formData.append('subject', subject); 
            formData.append('content', html);
            formData.append('list_id', listId);
            
            fetch('/mail/campaigns/save-visual', { 
                method: 'POST', 
                body: formData 
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = '/mail/campaigns';
                } else {
                    alert('Failed to save campaign. Please try again.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error saving campaign:', err);
                alert('Network error while saving. Please check your connection.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        } catch (e) {
            console.error(e);
            alert("Error exporting email HTML.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>

</body>
</html>

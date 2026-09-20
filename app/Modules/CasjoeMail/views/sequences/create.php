<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sequence | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php $active = 'sequences'; include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <a href="/mail/sequences" class="btn" style="background: transparent; border: 1px solid #ccc; color: #ccc; margin-bottom: 10px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back
                </a>
                <h1 style="color: #ffffff; margin-top: 5px;">Create New Sequence</h1>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="card" style="background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <form action="/mail/sequences/store" method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1a1a1a;">Sequence Name *</label>
                    <input type="text" name="name" required 
                           placeholder="e.g., Welcome Series, Follow-Up Sequence" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1a1a1a;">Description</label>
                    <textarea name="description" rows="3" 
                              placeholder="Briefly describe what this sequence does..."
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;"></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1a1a1a;">Trigger Event</label>
                    <select name="trigger_event" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                        <option value="manual">Manual (Enroll subscribers manually)</option>
                        <option value="form_submit">Form Submit (Auto-enroll when form submitted)</option>
                        <option value="list_join">List Join (Auto-enroll when added to list)</option>
                    </select>
                    <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                        Choose how subscribers are added to this sequence
                    </small>
                </div>

                <div style="background: #e6f4ea; border-left: 4px solid #28a745; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="margin: 0; color: #155724; font-size: 0.95rem;">
                        <strong>💡 Next Step:</strong> After creating your sequence, you'll add email steps with customizable delays.
                    </p>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn" style="background: #000066; color: #ffffff;">
                        Create Sequence & Add Steps
                    </button>
                    <a href="/mail/sequences" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<style>
    /* Sidebar contrast fixes */
    .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { 
        color: #ffffff !important; 
        background: rgba(255, 255, 255, 0.1); 
    }
    .sidebar .nav-link ion-icon { color: inherit !important; }
</style>
</body>
</html>

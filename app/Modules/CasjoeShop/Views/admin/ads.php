<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Settings - Advertisements | Admin</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <!-- Including Bootstrap for Form Config Checkboxes/Grids if not already global, 
         but trying to match existing admin style where possible or usage of bootstrap classes implies it.
         Safest is to include it for this specific view modules often need specific UI libraries. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .main-content { padding: 20px; overflow-y: auto; background: #f4f6f9; min-height: 100vh; margin-left: 0 !important; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-header { font-weight: 600; }
        .app-container { display: flex; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { 
            width: 260px; 
            background: #000066; /* Brand Color */
            color: white; 
            display: flex; 
            flex-direction: column; 
            padding: 20px; 
            overflow-y: auto; 
            height: 100vh; 
            position: sticky; 
            top: 0; 
            scrollbar-width: thin; 
            scrollbar-color: rgba(255,255,255,0.2) #000066; 
        }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: #000066; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white !important; }

        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white !important; }
        
        /* Visibility Overrides for Admin Light Theme */
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333 !important; }
        .card { background: #fff !important; color: #333 !important; }
        .form-label, label, h1, h2, h3, h4, h5, h6 { color: #333 !important; }
        .form-control { 
            background-color: #fff !important; 
            color: #000 !important; 
            border: 1px solid #ced4da !important;
        }
        .form-control:focus {
            background-color: #fff !important;
            color: #000 !important;
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../../../Core/Views/admin/sidebar.php'; ?>
    
    <main class="main-content w-100">
        <div class="container-fluid">
            <h2 class="mb-4">Advertisement Banners</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">Ad settings updated successfully!</div>
            <?php endif; ?>

            <form action="/<?= ADMIN_PATH ?>/shop/ads/update" method="POST">
                
                <!-- Top Banner Settings -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Top Banner (Below Header)</h5>
                    </div>
                    <div class="card-body">
                        <?php $ad = $adsByLocation['top'] ?? []; ?>
                        <input type="hidden" name="top[banner_location]" value="top">
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="top[is_active]" id="topActive" <?= (!empty($ad['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="topActive">Enable Top Banner</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Banner Title</label>
                                <input type="text" name="top[title]" class="form-control" value="<?= htmlspecialchars($ad['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="top[button_text]" class="form-control" value="<?= htmlspecialchars($ad['button_text'] ?? '') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Text</label>
                                <input type="text" name="top[description]" class="form-control" value="<?= htmlspecialchars($ad['description'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Target Link (URL)</label>
                                <input type="text" name="top[button_link]" class="form-control" value="<?= htmlspecialchars($ad['button_link'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Image URL (Optional)</label>
                                <input type="text" name="top[image_url]" class="form-control" value="<?= htmlspecialchars($ad['image_url'] ?? '') ?>" placeholder="https://example.com/banner.jpg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle Banner Settings -->
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Middle Banner (Above Products)</h5>
                    </div>
                    <div class="card-body">
                        <?php $ad = $adsByLocation['middle'] ?? []; ?>
                        <input type="hidden" name="middle[banner_location]" value="middle">
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="middle[is_active]" id="middleActive" <?= (!empty($ad['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="middleActive">Enable Middle Banner</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Banner Title</label>
                                <input type="text" name="middle[title]" class="form-control" value="<?= htmlspecialchars($ad['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="middle[button_text]" class="form-control" value="<?= htmlspecialchars($ad['button_text'] ?? '') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Text</label>
                                <input type="text" name="middle[description]" class="form-control" value="<?= htmlspecialchars($ad['description'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Target Link (URL)</label>
                                <input type="text" name="middle[button_link]" class="form-control" value="<?= htmlspecialchars($ad['button_link'] ?? '') ?>">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Image URL (Optional)</label>
                                <input type="text" name="middle[image_url]" class="form-control" value="<?= htmlspecialchars($ad['image_url'] ?? '') ?>" placeholder="Leave empty to use default gradient">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>


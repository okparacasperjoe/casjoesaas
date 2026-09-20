<?php $active = 'lists'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Subscribers | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
    <style>
        .import-layout {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
        .import-main {
            flex: 2;
            min-width: 350px;
        }
        .import-side {
            flex: 1;
            min-width: 300px;
        }
        @media (max-width: 768px) {
            .import-main, .import-side {
                min-width: 100%;
            }
        }
        .drop-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 40px 30px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .drop-zone:hover, .drop-zone.active {
            border-color: #000066;
            background: #f0f4ff;
        }
        .drop-zone i {
            font-size: 3rem;
            color: #000066;
            margin-bottom: 15px;
            display: block;
        }
        .drop-zone-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .drop-zone-subtext {
            font-size: 0.9rem;
            color: #64748b;
        }
        .column-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .column-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
            font-size: 0.85rem;
            text-align: center;
            color: #475569;
            font-weight: 600;
        }
        .column-chip.required {
            border-left: 3px solid #dc2626;
        }
        .column-chip.required::after {
            content: '*';
            color: #dc2626;
            margin-left: 4px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.9rem;
            color: #334155;
        }
        .feature-list li i {
            color: #000066;
            font-size: 1.1rem;
        }
        .alert-premium {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        .alert-premium-success {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            color: #166534;
        }
        .alert-premium-danger {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
        }
        .section-title {
            color: #000066;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4ff;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar via partial -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <div>
                    <h1>Import Subscribers</h1>
                </div>
                <a href="/mail/lists/view?id=<?= $listId ?>" class="btn" style="background: transparent; border: 1px solid #cbd5e1; color: #475569;">
                    <ion-icon name="arrow-back-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Back to List
                </a>
            </div>

            <div class="import-layout">
                <!-- Main Form Column -->
                <div class="import-main">
                    <div class="card app-card-white" style="padding: 30px; border-top: 4px solid #000066;">
                        
                        <!-- Feedback Alerts -->
                        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                            <div class="alert-premium alert-premium-success">
                                <i class="bi bi-check2-circle fs-3 me-3" style="margin-right: 15px;"></i>
                                <div>
                                    <h5 style="margin: 0 0 5px 0; font-size: 1.1rem;">Success!</h5>
                                    <p style="margin: 0; font-size: 0.95rem;"><?= $_GET['count'] ?? 0 ?> subscribers have been successfully imported.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_GET['error'])): ?>
                            <div class="alert-premium alert-premium-danger">
                                <i class="bi bi-exclamation-octagon fs-3 me-3" style="margin-right: 15px;"></i>
                                <div>
                                    <h5 style="margin: 0 0 5px 0; font-size: 1.1rem;">Import Issue</h5>
                                    <p style="margin: 0; font-size: 0.95rem;"><?= htmlspecialchars($_GET['error']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form action="/mail/lists/import-process" method="POST" enctype="multipart/form-data" id="importForm">
                            <input type="hidden" name="list_id" value="<?= $listId ?>">
                            <input type="hidden" name="has_header" value="on">

                            <div class="drop-zone" id="dropZone">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <div class="drop-zone-text">Drop your CSV file here</div>
                                <div class="drop-zone-subtext">or click to browse your local files</div>
                                <input type="file" name="csv_file" id="fileInput" style="display: none;" accept=".csv" required>
                                
                                <div id="filePreview" style="display: none; margin-top: 20px;">
                                    <div style="background: #e6e6ff; padding: 10px 15px; border-radius: 8px; display: inline-flex; align-items: center; gap: 10px; border: 1px solid #000066;">
                                        <i class="bi bi-file-earmark-spreadsheet-fill" style="margin: 0; color: #000066; font-size: 1.2rem;"></i>
                                        <span id="fileNameDisplay" style="color: #000066; font-weight: 600;">filename.csv</span>
                                        <i class="bi bi-x-circle-fill" style="margin: 0; cursor: pointer; color: #dc2626; font-size: 1.2rem;" onclick="resetFile(event)"></i>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1.1rem; justify-content: center;">
                                <i class="bi bi-lightning-charge-fill" style="margin-right: 8px;"></i> 
                                Perform Bulk Import
                            </button>
                        </form>

                        <!-- Direct Link Import Section -->
                        <div style="margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 25px;">
                            <h3 class="section-title">
                                <i class="bi bi-link-45deg" style="color: #2563eb; margin-right: 8px;"></i>
                                Import via URL
                            </h3>
                            <p style="color: #64748b; margin-bottom: 20px; font-size: 0.95rem;">
                                Paste a direct link to a CSV file (like a published Google Sheet link).
                            </p>
                            <form action="/mail/lists/import-link-process" method="POST" id="importLinkForm">
                                <input type="hidden" name="list_id" value="<?= $listId ?>">
                                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <input type="url" name="csv_url" class="form-control" placeholder="https://..." required style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: #fff; color: #333;">
                                </div>
                                <button type="submit" class="btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #000066; width: 100%; padding: 12px; justify-content: center;">
                                    <i class="bi bi-cloud-download" style="margin-right: 8px;"></i> Fetch & Import URL
                                </button>
                            </form>
                        </div>
                        
                        <!-- Direct ERP Sync Section -->
                        <div style="margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 25px;">
                            <h3 class="section-title">
                                <i class="bi bi-database-fill-gear" style="color: #FFA600; margin-right: 8px;"></i>
                                Direct ERP Contact Sync
                            </h3>
                            <p style="color: #64748b; margin-bottom: 20px; font-size: 0.95rem;">
                                Instantly pull all customers and contacts from your <strong>Casjoe ERP CRM</strong> directly into this list with a single click.
                            </p>
                            <form action="/mail/lists/sync-erp" method="POST">
                                <input type="hidden" name="list_id" value="<?= $listId ?>">
                                <button type="submit" class="btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #000066;">
                                    <i class="bi bi-arrow-repeat" style="margin-right: 8px;"></i> Sync Casjoe ERP Contacts
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Side Information Column -->
                <div class="import-side">
                    <!-- Requirements Card -->
                    <div class="card app-card-white" style="padding: 25px; margin-bottom: 20px;">
                        <h3 class="section-title">CSV Requirements</h3>
                        <p style="font-size: 0.9rem; color: #475569; margin-bottom: 15px;">Your CSV file must include a header row with the following column names:</p>
                        
                        <div class="column-grid">
                            <div class="column-chip required" title="Required">email</div>
                            <div class="column-chip">first_name</div>
                            <div class="column-chip">last_name</div>
                            <div class="column-chip">phone</div>
                            <div class="column-chip">company</div>
                        </div>
                        
                        <div style="margin-top: 15px; font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 5px;">
                            <span style="color: #dc2626; font-size: 1.2rem;">*</span> Required Column
                        </div>
                    </div>

                    <!-- What happens next? Card -->
                    <div class="card app-card-white" style="padding: 25px;">
                        <h3 class="section-title">What happens next?</h3>
                        <ul class="feature-list">
                            <li><i class="bi bi-magic"></i> Automatic deduplication of existing emails</li>
                            <li><i class="bi bi-shield-check"></i> Syntax validation for invalid email addresses</li>
                            <li><i class="bi bi-person-check"></i> Contacts are immediately available for campaigns</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const importForm = document.getElementById('importForm');

        // Click to browse
        dropZone.addEventListener('click', (e) => {
            if (e.target.tagName !== 'I' || !e.target.classList.contains('bi-x-circle-fill')) {
                fileInput.click();
            }
        });

        // Drag and Drop styling
        ['dragover', 'dragenter'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('active');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('active');
            });
        });

        // Handle File Drop
        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                updateFileDisplay();
            }
        });

        // Handle File Select
        fileInput.addEventListener('change', updateFileDisplay);

        function updateFileDisplay() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (!file.name.endsWith('.csv')) {
                    alert('Please select a valid CSV file.');
                    resetFile();
                    return;
                }
                fileNameDisplay.textContent = file.name;
                filePreview.style.display = 'block';
                // Hide icon and texts
                dropZone.querySelector('.bi-cloud-arrow-up-fill').style.display = 'none';
                dropZone.querySelector('.drop-zone-text').style.display = 'none';
                dropZone.querySelector('.drop-zone-subtext').style.display = 'none';
            }
        }

        window.resetFile = function(e) {
            if (e) e.stopPropagation();
            fileInput.value = '';
            filePreview.style.display = 'none';
            // Show icon and texts
            dropZone.querySelector('.bi-cloud-arrow-up-fill').style.display = 'block';
            dropZone.querySelector('.drop-zone-text').style.display = 'block';
            dropZone.querySelector('.drop-zone-subtext').style.display = 'block';
        };

        // Loader on submit
        importForm.addEventListener('submit', function(e) {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload a CSV file first.');
                return;
            }
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite;"></i> Processing Data...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });

        document.getElementById('importLinkForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite;"></i> Fetching Data...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
    </script>
    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</body>
</html>

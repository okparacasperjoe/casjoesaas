<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Import Transactions | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .import-card {
            background: var(--surface, #ffffff);
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 760px;
            margin: 0 auto 30px;
            border: 1px solid rgba(0,0,0,0.06);
        }
        html.dark-theme .import-card {
            background: #111827;
            border-color: rgba(255,255,255,0.08);
        }
        .dropzone-container {
            border: 2px dashed #3b82f6;
            border-radius: 10px;
            padding: 36px 20px;
            text-align: center;
            background: rgba(59, 130, 246, 0.03);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 20px;
            position: relative;
        }
        .dropzone-container:hover {
            background: rgba(59, 130, 246, 0.08);
            border-color: #2563eb;
        }
        .dropzone-icon {
            font-size: 48px;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        .guide-table th {
            text-align: left;
            padding: 8px 12px;
            background: rgba(0,0,0,0.03);
            border-bottom: 2px solid rgba(0,0,0,0.06);
        }
        html.dark-theme .guide-table th {
            background: rgba(255,255,255,0.05);
            border-bottom-color: rgba(255,255,255,0.1);
        }
        .guide-table td {
            padding: 8px 12px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        html.dark-theme .guide-table td {
            border-bottom-color: rgba(255,255,255,0.05);
        }
        .badge-req {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-opt {
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Import Financial Spreadsheet</h2>
                <p style="color: #64748b; font-size: 13px; margin-top: 2px;">Bulk import cashflow, revenue, expenses, and bank transactions from Excel / CSV</p>
            </div>
            <a href="/erp/transactions" class="btn" style="background: #64748b; display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Transactions
            </a>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($errorMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($successMsg) ?></div>
            </div>
        <?php endif; ?>

        <div class="import-card">
            <!-- Instructions and Template -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 17px;">Upload Financial Spreadsheet</h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b;">
                        Upload your <strong>Excel (.xlsx, .xls)</strong> or <strong>CSV (.csv)</strong> statement directly.
                    </p>
                </div>
                <a href="/erp/transactions/sample-csv" class="btn" style="background: #0284c7; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 8px 14px;">
                    <ion-icon name="download-outline"></ion-icon> Download Sample Template
                </a>
            </div>

            <!-- Upload Form -->
            <form method="POST" action="/erp/transactions/import" enctype="multipart/form-data" id="importForm">
                <div class="dropzone-container" onclick="document.getElementById('csv_file').click()">
                    <ion-icon name="cloud-upload-outline" class="dropzone-icon"></ion-icon>
                    <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;" id="fileLabel">Click to select or drag & drop Excel / CSV file here</div>
                    <div style="font-size: 12px; color: #64748b;">Supported formats: .xlsx, .xls, .csv, .txt (Max 10MB)</div>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv, .txt, .xlsx, .xls" required style="display: none;" onchange="handleFileSelected(this)">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; user-select: none;">
                        <input type="checkbox" name="skip_duplicates" value="1" checked style="width: 16px; height: 16px;">
                        <span><strong>Skip duplicate records</strong> (prevents importing records that match existing date, amount, and description)</span>
                    </label>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; user-select: none;">
                        <input type="checkbox" name="sync_expenses" value="1" checked style="width: 16px; height: 16px;">
                        <span>Also mirror Outflow / Expense records into the <strong>ERP Expenses</strong> ledger</span>
                    </label>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <ion-icon name="checkmark-done-outline" style="font-size: 20px;"></ion-icon> Import Transactions Now
                </button>
            </form>
        </div>

        <!-- Expected Columns Guide -->
        <div class="import-card">
            <h3 style="margin: 0 0 10px 0; font-size: 16px; display: flex; align-items: center; gap: 6px;">
                <ion-icon name="information-circle-outline" style="color: #0284c7;"></ion-icon> Supported Column Headers
            </h3>
            <p style="font-size: 13px; color: #64748b; margin-top: 0;">
                The importer automatically detects column headers regardless of casing or position. The supported columns are:
            </p>

            <table class="guide-table">
                <thead>
                    <tr>
                        <th>Column Header</th>
                        <th>Status</th>
                        <th>Accepted Values / Formats</th>
                        <th>Example</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Date</strong></td>
                        <td><span class="badge-req">Required</span></td>
                        <td><code>YYYY-MM-DD</code>, <code>DD/MM/YYYY</code>, <code>MM/DD/YYYY</code>, or Excel Date serial</td>
                        <td><code>2026-08-31</code></td>
                    </tr>
                    <tr>
                        <td><strong>Description</strong></td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Details, Memo, Narration, Item, Payee, Ref, or Title</td>
                        <td><code>Client Invoice #1024</code></td>
                    </tr>
                    <tr>
                        <td><strong>Amount</strong> or <strong>Debit / Credit</strong></td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Unified Amount OR separate Debit (outflow) & Credit (inflow) columns</td>
                        <td><code>1500.00</code></td>
                    </tr>
                    <tr>
                        <td><strong>Type</strong></td>
                        <td><span class="badge-opt">Auto-detected</span></td>
                        <td><code>income</code> (revenue, credit) or <code>expense</code> (debit, cost)</td>
                        <td><code>income</code></td>
                    </tr>
                    <tr>
                        <td><strong>Category</strong></td>
                        <td><span class="badge-opt">Optional</span></td>
                        <td>Any department, tag, account, or grouping name</td>
                        <td><code>Consulting Revenue</code></td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 15px; font-size: 12px; color: #64748b; background: rgba(0,0,0,0.02); padding: 10px; border-radius: 6px; line-height: 1.6;">
                💡 <strong>Tips:</strong>
                <br>• If your file has separate <strong>Debit</strong> and <strong>Credit</strong> columns, they are automatically categorized as Expenses and Income respectively.
                <br>• If only an <strong>Amount</strong> column is present without <strong>Type</strong>, negative numbers (e.g. <code>-50.00</code>) become Expenses and positive numbers become Income.
            </div>
        </div>
    </main>
</div>

<script>
function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        document.getElementById('fileLabel').innerHTML = '<span style="color:#10b981;">✓ ' + file.name + '</span> (' + (file.size / 1024).toFixed(1) + ' KB)';
    }
}
</script>
</body>
</html>

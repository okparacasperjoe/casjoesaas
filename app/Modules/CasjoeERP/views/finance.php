<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>Finance & Accounting</h1>
                <!-- Actions -->
                <div style="display:flex; gap:10px;">
                    <button class="btn" onclick="alert('Demo: Create Journal Modal')">
                        <ion-icon name="add-circle-outline"></ion-icon> New Entry
                    </button>
                    <button class="btn" style="background: #2ecc71;" onclick="document.getElementById('posModal').style.display='flex'">
                        <ion-icon name="calculator-outline"></ion-icon> Receive via POS
                    </button>
                </div>
            </div>

            <!-- POS Modal -->
            <?php
            $posTerminals = [];
            try {
                $dbPos = \App\Core\Database::getInstance()->getConnection();
                $tId = \App\Core\TenantContext::getTenantId();
                if ($tId) {
                    $stP = $dbPos->prepare("SELECT terminal_name, terminal_serial FROM erp_pos_terminals WHERE tenant_id = ? AND is_active = 1");
                    $stP->execute([$tId]);
                    $posTerminals = $stP->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                }
            } catch (\Throwable $e) {}
            ?>
            <div id="posModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
                <div class="modal-content" style="background:var(--card-bg); padding:20px; border-radius:8px; width:420px; max-width:90%; position:relative; margin: 10% auto;">
                    <span onclick="document.getElementById('posModal').style.display='none'" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
                    <h2 style="margin-top:0;">Receive via Moniepoint POS</h2>
                    <div style="margin-top:15px;">
                        <?php if (count($posTerminals) > 1): ?>
                            <label style="font-weight:600;">Select POS Terminal</label><br>
                            <select id="posTerminalSerial" style="width:100%; padding:8px; margin-top:5px; margin-bottom:15px; border:1px solid var(--border-color); border-radius:4px; background:var(--bg-color); color:var(--text-color);">
                                <?php foreach ($posTerminals as $term): ?>
                                    <option value="<?= htmlspecialchars($term['terminal_serial']) ?>">
                                        <?= htmlspecialchars($term['terminal_name']) ?> (<?= htmlspecialchars($term['terminal_serial']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif (count($posTerminals) === 1): ?>
                            <input type="hidden" id="posTerminalSerial" value="<?= htmlspecialchars($posTerminals[0]['terminal_serial']) ?>">
                            <small style="color:#0284c7; display:block; margin-bottom:12px; font-weight:600;">Target Device: <?= htmlspecialchars($posTerminals[0]['terminal_name']) ?> (<?= htmlspecialchars($posTerminals[0]['terminal_serial']) ?>)</small>
                        <?php endif; ?>

                        <label style="font-weight:600;">Amount (₦)</label><br>
                        <input type="number" id="posAmount" style="width:100%; padding:8px; margin-top:5px; margin-bottom:15px; border:1px solid var(--border-color); border-radius:4px; background:var(--bg-color); color:var(--text-color);" placeholder="Enter amount">
                        
                        <label style="font-weight:600;">Description</label><br>
                        <input type="text" id="posDesc" style="width:100%; padding:8px; margin-top:5px; margin-bottom:15px; border:1px solid var(--border-color); border-radius:4px; background:var(--bg-color); color:var(--text-color);" placeholder="e.g., POS Sale">
                        
                        <button onclick="pushToPos()" class="btn" style="width:100%; background:#2ecc71; text-align:center; font-weight:700; padding:10px;">Push to Terminal</button>
                    </div>
                </div>
            </div>

            <script>
            async function pushToPos() {
                const amount = document.getElementById('posAmount').value;
                const desc = document.getElementById('posDesc').value;
                const termEl = document.getElementById('posTerminalSerial');
                if (!amount) return alert('Enter amount');
                
                const btn = document.querySelector('#posModal .btn');
                const origText = btn.innerHTML;
                btn.innerHTML = 'Waiting for Customer on Terminal...';
                btn.disabled = true;

                const formData = new FormData();
                formData.append('amount', amount);
                formData.append('description', desc);
                if (termEl && termEl.value) {
                    formData.append('terminal_serial', termEl.value);
                }

                try {
                    const response = await fetch('/api/erp/moniepoint/push', {
                        method: 'POST',
                        body: formData
                    });
                    const res = await response.json();
                    
                    if (res.success) {
                        alert('Payment successful and recorded! Ref: ' + res.reference);
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (e) {
                    alert('Network error pushing to POS.');
                }
                
                btn.innerHTML = origText;
                btn.disabled = false;
            }
            </script>

            <!-- GL Accounts -->
            <div class="table-container" style="margin-bottom: 30px;">
                <div class="section-title">Chart of Accounts</div>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($accounts)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 20px;">No accounts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($account['code']); ?></td>
                                    <td><?php echo htmlspecialchars($account['name']); ?></td>
                                    <td><span class="status-badge"><?php echo htmlspecialchars($account['type']); ?></span></td>
                                    <td>$<?php echo number_format($account['balance'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Journals -->
            <div class="table-container">
                <div class="section-title">Recent Journal Entries</div>
                <table>
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entries)): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 20px;">No journal entries yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($entries as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['reference']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['date']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['description']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>

</html>

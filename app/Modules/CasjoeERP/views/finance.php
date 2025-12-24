<!DOCTYPE html>
<html lang="en">

<head>
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
                <button class="btn" onclick="alert('Demo: Create Journal Modal')">
                    <ion-icon name="add-circle-outline"></ion-icon> New Entry
                </button>
            </div>

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
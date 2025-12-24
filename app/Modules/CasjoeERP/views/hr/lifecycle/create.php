<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Lifecycle Event | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script>
        function togglePositionField() {
            const type = document.querySelector('select[name="type"]').value;
            const posField = document.getElementById('newPositionField');
            if (type === 'promotion') {
                posField.style.display = 'block';
                document.querySelector('input[name="new_position"]').required = true;
            } else {
                posField.style.display = 'none';
                document.querySelector('input[name="new_position"]').required = false;
            }
        }
    </script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Record Event</h2>
        </div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form action="/erp/lifecycle/store" method="POST">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Event Type</label>
                    <select name="type" class="form-control" required onchange="togglePositionField()">
                        <option value="promotion">Promotion</option>
                        <option value="resignation">Resignation</option>
                        <option value="termination">Termination</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group" id="newPositionField">
                    <label>New Position (for Promotion)</label>
                    <input type="text" name="new_position" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Reason / Comments</label>
                    <textarea name="reason" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn">Submit Record</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>

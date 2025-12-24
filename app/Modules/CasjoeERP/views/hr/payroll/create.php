<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Run Payroll | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script>
        function updateBaseSalary() {
            const select = document.querySelector('select[name="employee_id"]');
            const salaryInput = document.querySelector('input[name="base_earning"]');
            const option = select.options[select.selectedIndex];
            if (option.dataset.salary) {
                salaryInput.value = option.dataset.salary;
            }
        }
    </script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Run Payroll</h2>
        </div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form action="/erp/payroll/store" method="POST">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required onchange="updateBaseSalary()">
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>" data-salary="<?= $e['salary'] ?>">
                                <?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Period Start</label>
                        <input type="date" name="pay_period_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Period End</label>
                        <input type="date" name="pay_period_end" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <h3>Earnings & Deductions</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Base Salary</label>
                        <input type="number" step="0.01" name="base_earning" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Bonus</label>
                        <input type="number" step="0.01" name="bonus" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label>Tax</label>
                        <input type="number" step="0.01" name="tax" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label>Other Deductions</label>
                        <input type="number" step="0.01" name="other_deductions" class="form-control" value="0">
                    </div>
                </div>

                <button type="submit" class="btn">Process Payroll</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>

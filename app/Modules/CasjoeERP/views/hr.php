<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="planet"></ion-icon>
                Casjoe<span>ERP</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <ion-icon name="apps-outline"></ion-icon>
                        Back to Apps
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/erp" class="nav-link">
                        <ion-icon name="grid-outline"></ion-icon>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/erp/hr" class="nav-link active">
                        <ion-icon name="people-outline"></ion-icon>
                        Human Resources
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/erp/finance" class="nav-link">
                        <ion-icon name="cash-outline"></ion-icon>
                        Finance
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/erp/crm" class="nav-link">
                        <ion-icon name="briefcase-outline"></ion-icon>
                        CRM
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/erp/inventory" class="nav-link">
                        <ion-icon name="cube-outline"></ion-icon>
                        Inventory
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>Human Resources</h1>
                <!-- Actions -->
                <button class="btn" onclick="document.getElementById('addEmployeeModal').style.display='block'">
                    <ion-icon name="person-add-outline"></ion-icon> Add Employee
                </button>
            </div>

            <!-- Employee List -->
            <div class="table-container">
                <div class="section-title">Employees</div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 20px;">No employees found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['position'] ?? '-'); ?></td>
                                    <td><span
                                            class="status-badge status-active"><?php echo htmlspecialchars($emp['status']); ?></span>
                                    </td>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recruitment | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Job Openings</h2>
            <button onclick="document.getElementById('newJobModal').showModal()" class="btn">Post Job</button>
        </div>

        <div class="card">
            <h3>Active Listings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['department']) ?></td>
                        <td><?= ucfirst($job['status']) ?></td>
                        <td>
                            <a href="/erp/recruitment/applications?job_id=<?= $job['id'] ?>" class="btn btn-sm">View Applications</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Quick Application Test Form (Usually public, but here for testing) -->
        <div class="card" style="margin-top: 20px;">
             <h3>Internal Application Test</h3>
             <form action="/erp/recruitment/apply" method="POST">
                 <select name="job_id" required>
                     <?php foreach ($jobs as $job): ?>
                        <option value="<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></option>
                     <?php endforeach; ?>
                 </select>
                 <input type="text" name="candidate_name" placeholder="Candidate Name" required>
                 <input type="email" name="email" placeholder="Email" required>
                 <button type="submit" class="btn btn-sm">Submit Test Application</button>
             </form>
        </div>
    </main>

    <dialog id="newJobModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/recruitment/store" method="POST">
            <h3>Post New Job</h3>
            <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label>Department</label><input type="text" name="department" class="form-control" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newJobModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Post</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>

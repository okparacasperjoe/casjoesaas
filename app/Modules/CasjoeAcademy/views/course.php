<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?> | Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="school"></ion-icon>
                Casjoe<span>Academy</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/" class="nav-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="nav-item"><a href="/academy" class="nav-link"><ion-icon
                            name="arrow-back-outline"></ion-icon>Back to Courses</a></li>
                <li class="nav-item"><a href="#" class="nav-link active"><ion-icon name="book-outline"></ion-icon>Course
                        Overview</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Course Overview</h1>
            </div>

            <div class="card" style="margin-bottom: 30px;">
                <h2 style="font-size: 2rem; margin-bottom: 10px;"><?= htmlspecialchars($course['title']) ?></h2>
                <p style="color: var(--text-muted); line-height: 1.6;"><?= htmlspecialchars($course['description']) ?>
                </p>
            </div>

            <div class="table-container">
                <div class="section-title">Course Content</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Lesson Title</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lessons)): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 20px;">No lessons yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lessons as $index => $lesson): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div style="font-weight: 500;"><?= htmlspecialchars($lesson['title']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">Video Lesson</div>
                                    </td>
                                    <td>
                                        <a href="/academy/course/<?= $course['id'] ?>/lesson/<?= $lesson['id'] ?>" class="btn"
                                            style="padding: 5px 15px; font-size: 0.9rem;">
                                            <ion-icon name="play-circle-outline"
                                                style="vertical-align: middle; margin-right: 5px;"></ion-icon> Start
                                        </a>
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
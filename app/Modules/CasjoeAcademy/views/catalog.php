<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Catalog | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .course-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .course-thumb {
            height: 180px;
            background: #eee;
            background-size: cover;
            background-position: center;
        }
        .course-content {
            padding: 20px;
        }
        .course-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        .price-tag {
            font-weight: bold;
            color: var(--secondary);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require_once __DIR__ . '/../../../core/Views/global_dashboard.php'; // Reuse Sidebar logic if possible, or just mock for now since partials are tricky ?> 
    <!-- Actually, let's just make a standalone layout for now reusing style.css -->
    
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/academy" class="nav-link active"><ion-icon name="library-outline"></ion-icon> Catalog</a></li>
            <li class="nav-item"><a href="/academy/my-courses" class="nav-link"><ion-icon name="school-outline"></ion-icon> My Learning</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Academy Catalog</h2>
            <div class="user-profile">
                <span>Welcome, User</span>
            </div>
        </div>

        <div class="course-grid">
            <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div class="course-thumb" style="background-image: url('<?= htmlspecialchars($course['thumbnail']) ?>');"></div>
                <div class="course-content">
                    <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
                    <p style="font-size: 0.9rem; color: #666; line-height: 1.4;"><?= substr(htmlspecialchars($course['description']), 0, 100) ?>...</p>
                    <div class="course-meta">
                        <span class="price-tag"><?= $course['price'] > 0 ? '₦'.number_format($course['price']) : 'Free' ?></span>
                        <a href="/academy/course/<?= $course['id'] ?>" class="btn">View Course</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Course & E-Book Catalog | Casjoe Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* Netflix-style Catalog Aesthetics */
        body, html {
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .catalog-main {
            padding-bottom: 50px;
        }
        .catalog-hero {
            background: linear-gradient(0deg, #0f172a 0%, rgba(15,23,42,0.6) 100%), url('https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=2070&auto=format&fit=crop') center/cover;
            padding: 100px 60px 60px;
            margin-bottom: 20px;
            position: relative;
        }
        .catalog-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            max-width: 800px;
        }
        .catalog-hero p {
            font-size: 1.2rem;
            color: #e2e8f0;
            max-width: 600px;
            line-height: 1.6;
            text-shadow: 0 1px 5px rgba(0,0,0,0.5);
        }
        .section-header {
            padding: 0 60px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 0 60px 50px;
        }
        .course-card-modern {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #1e293b;
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s ease, z-index 0.3s;
            text-decoration: none;
            color: white;
            display: flex;
            flex-direction: column;
            aspect-ratio: 16/9;
            z-index: 1;
        }
        .course-card-modern:hover {
            transform: scale(1.08);
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            z-index: 10;
        }
        .course-thumb-modern {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: opacity 0.3s ease;
        }
        .course-thumb-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(15,23,42,1) 0%, rgba(15,23,42,0) 80%);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        .course-card-modern:hover .course-thumb-overlay {
            opacity: 0.95;
            background: linear-gradient(0deg, rgba(15,23,42,1) 0%, rgba(15,23,42,0.6) 100%);
        }
        .thumb-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e11d48;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            z-index: 2;
            text-transform: uppercase;
        }
        .course-info-modern {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            z-index: 2;
            transform: translateY(20px);
            opacity: 0.8;
            transition: all 0.3s ease;
        }
        .course-card-modern:hover .course-info-modern {
            transform: translateY(0);
            opacity: 1;
        }
        .course-info-modern h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            line-height: 1.3;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }
        .course-desc {
            font-size: 0.8rem;
            color: #cbd5e1;
            margin-bottom: 10px;
            display: none;
            line-height: 1.4;
        }
        .course-card-modern:hover .course-desc {
            display: block;
        }
        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }
        .price-tag {
            font-weight: bold;
            color: #10b981;
        }
        .play-btn {
            background: white;
            color: black;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .course-card-modern:hover .play-btn {
            opacity: 1;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .catalog-hero {
                padding: 80px 20px 40px; /* Reduced padding */
            }
            .catalog-hero h1 {
                font-size: 2.2rem;
            }
            .catalog-hero p {
                font-size: 1rem;
            }
            .section-header {
                padding: 0 20px;
            }
            .course-grid {
                grid-template-columns: 1fr;
                padding: 0 20px 40px;
            }
            .mobile-toggle {
                display: block !important;
                position: static !important;
                background: rgba(255,255,255,0.1);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 12px;
                font-size: 1.5rem;
                cursor: pointer;
            }
            h3 {
                padding: 0 20px !important;
            }
        }
    </style>
</head>
<body class="catalog-main">
<div class="app-container" style="background: transparent; min-height: 100vh;">
    <?php include dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
    <?php $active = 'academy_market'; include __DIR__ . '/partials/sidebar_academy.php'; ?>

    <main class="main-content" style="padding: 0;">
        <div class="catalog-hero">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <button class="mobile-toggle d-md-none" onclick="toggleMobileMenu()" style="display: none;">
                    <ion-icon name="menu-outline"></ion-icon>
                </button>
                <h1 style="margin: 0;">Casjoe Business School</h1>
            </div>
            <p>Master business operating systems, leadership frameworks, and technical skills through our premium video courses and executive reading material.</p>
        </div>

        <!-- Video Courses Section -->
        <div class="section-header">
            <h3>Trending Now</h3>
        </div>
        <div class="course-grid">
            <?php if (empty($courses)): ?>
                <div style="grid-column: 1/-1; padding: 40px; text-align: center; color: #64748b;">
                    No video courses available at the moment.
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                <?php 
                    $cleanDesc = trim(strip_tags($course['description'] ?? ''));
                    $shortDesc = mb_strimwidth($cleanDesc, 0, 90, '...');
                ?>
                <a href="/academy/course/<?= $course['id'] ?>" class="course-card-modern">
                    <div class="course-thumb-modern" style="background-image: url('<?= htmlspecialchars($course['thumbnail'] ?? '') ?>');"></div>
                    <div class="course-thumb-overlay"></div>
                    <span class="thumb-badge">Original</span>
                    
                    <div class="course-info-modern">
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="course-desc"><?= htmlspecialchars($shortDesc) ?></p>
                        <div class="course-meta">
                            <span class="price-tag"><?= ($course['price'] ?? 0) > 0 ? '$'.number_format($course['price'], 2) : 'FREE' ?></span>
                            <div class="play-btn"><ion-icon name="play"></ion-icon></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Executive E-Books Section -->
        <?php if (!empty($books)): ?>
        <div class="section-header" style="margin-top: 20px;">
            <h3>Executive Reading</h3>
            <a href="/academy/library" style="font-size: 0.9rem; color: #94a3b8; text-decoration: none;">View Library →</a>
        </div>
        <div class="course-grid">
            <?php foreach ($books as $book): ?>
            <?php 
                $cleanDesc = trim(strip_tags($book['description'] ?? ''));
                $shortDesc = mb_strimwidth($cleanDesc, 0, 90, '...');
            ?>
            <a href="/academy/library/read/<?= $book['id'] ?>" class="course-card-modern" style="aspect-ratio: 3/4;">
                <div class="course-thumb-modern" style="background-image: url('<?= htmlspecialchars($book['cover_path'] ?? '/assets/casjoe_logo.png') ?>'); background-size: cover; background-position: top center;"></div>
                <div class="course-thumb-overlay"></div>
                <span class="thumb-badge" style="background: #2563eb;">E-Book</span>
                
                <div class="course-info-modern">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <?php if (!empty($book['author'])): ?>
                    <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px;"><?= htmlspecialchars($book['author']) ?></div>
                    <?php endif; ?>
                    <p class="course-desc"><?= htmlspecialchars($shortDesc) ?></p>
                    <div class="course-meta">
                        <span class="price-tag">Read Online</span>
                        <div class="play-btn" style="border-radius: 4px;"><ion-icon name="book"></ion-icon></div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>

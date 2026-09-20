<?php 
$title = "CEO Book Reader | Casjoe Business School";
$active = 'academy_library';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .book-card {
            background: var(--glass-bg, #fff);
            border: 1px solid var(--glass-border, #eee);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            cursor: pointer;
            position: relative;
        }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .book-cover {
            height: 250px; background: #e9ecef; position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .book-cover img { width: 100%; height: 100%; object-fit: cover; }
        .book-info { padding: 15px; }
        .book-title { font-weight: 700; color: var(--text-color, #eee) !important; margin-bottom: 5px; font-size: 1rem; }
        .book-author { color: var(--text-muted, #aaa) !important; font-size: 0.85rem; margin-bottom: 15px; }
        
        @media (max-width: 992px) {
            .main-content { padding: 1rem !important; }
        }
    </style>
</head>
<body>
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <!-- Load the centralized sidebar partial -->
    <?php require __DIR__ . '/../partials/sidebar_academy.php'; ?>

    <main class="main-content" style="padding: 2rem 2.5rem;">
        <div class="top-bar" style="display: flex; align-items: center; gap: 15px;">
            <button class="mobile-toggle d-md-none" onclick="toggleMobileMenu()" style="display: none; background: rgba(255,255,255,0.1); color: var(--text-color, #eee); border: none; border-radius: 8px; padding: 8px 12px; font-size: 1.5rem; cursor: pointer;">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
            <h2 style="margin: 0; flex: 1;">CEO Book Reader</h2>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/academy/library/upload" class="btn" style="background: var(--secondary); color:#000; padding: 8px 15px; border-radius: 8px; text-decoration:none; font-weight:bold;">
                <ion-icon name="cloud-upload-outline"></ion-icon> Upload Book
            </a>
            <?php endif; ?>
        </div>

        <div class="container-fluid p-0">
             <?php if(empty($books)): ?>
                <div style="text-align: center; padding: 50px; color: #666;">
                    <ion-icon name="library-outline" style="font-size: 4rem; opacity: 0.5;"></ion-icon>
                    <h3 class="mt-3 text-muted">Library is Empty</h3>
                    <p>No books have been added yet. check back soon!</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4">
                    <?php foreach($books as $book): ?>
                        <div class="col">
                            <div class="book-card" onclick="window.location.href='/academy/library/read/<?= $book['id'] ?>'">
                                <div class="book-cover">
                                    <?php if($book['cover_path']): ?>
                                        <img src="<?= htmlspecialchars($book['cover_path']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                                    <?php else: ?>
                                        <ion-icon name="book" style="font-size: 3rem; color: #ccc;"></ion-icon>
                                    <?php endif; ?>
                                </div>
                                <div class="book-info">
                                    <div class="book-title text-truncate" title="<?= htmlspecialchars($book['title']) ?>">
                                        <?= htmlspecialchars($book['title']) ?>
                                    </div>
                                    <div class="book-author text-truncate">
                                        <?= htmlspecialchars($book['author'] ?? 'Unknown Author') ?>
                                    </div>
                                    <button class="btn btn-sm w-100" style="background: #FFA600; color: #000; font-weight: 600; border:none;">
                                        Read
                                    </button>
                                </div>
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button onclick="event.stopPropagation(); window.location.href='/academy/library/edit/<?= $book['id'] ?>'" class="btn btn-sm" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                    <ion-icon name="pencil" style="font-size: 1.2rem; color: #00004D;"></ion-icon>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>
</body>
</html>

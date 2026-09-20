<?php require __DIR__ . '/../global_header.php'; ?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #000066; font-weight: 700; margin: 0; font-size: 1.75rem;">Knowledge Base</h2>
        <a href="/kb" style="color: var(--secondary); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px;"><ion-icon name="arrow-back-outline"></ion-icon> Back to Articles</a>
    </div>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-3">
            <div class="card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 20px;">
                <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h4 style="margin: 0; color: #fff; font-size: 1.1rem;">All Articles</h4>
                </div>
                <div style="padding: 15px;">
                    <?php foreach ($allArticles as $navItem): ?>
                        <a href="/kb/article/<?= htmlspecialchars($navItem['slug']) ?>" style="display: block; color: <?= $navItem['slug'] === $article['slug'] ? 'var(--secondary)' : 'var(--text-muted)' ?>; text-decoration: none; margin-bottom: 10px; padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,0.05); <?= $navItem['slug'] === $article['slug'] ? 'font-weight: bold;' : '' ?>">
                            <?= htmlspecialchars($navItem['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 20px;">
                <div style="padding: 30px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h1 style="color: #fff; margin: 0; font-size: 2rem;"><?= htmlspecialchars($article['title']) ?></h1>
                    <p style="color: var(--text-muted); margin: 10px 0 0 0; font-size: 0.9rem;">Last updated on <?= date('M j, Y', strtotime($article['created_at'])) ?></p>
                </div>
                <div style="padding: 30px; color: #eee; font-size: 1.05rem; line-height: 1.6;" class="kb-content">
                    <?= $article['content'] ?>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: var(--text-muted);">Still need help?</p>
                <a href="/support/create" class="btn btn-primary" style="background: var(--secondary); border: none; padding: 10px 25px; border-radius: 8px;">Create a Support Ticket</a>
            </div>
        </div>
    </div>
</div>

<style>
    .kb-content h3 { color: #fff; margin-top: 25px; margin-bottom: 15px; }
    .kb-content p { margin-bottom: 15px; }
    .kb-content ol, .kb-content ul { padding-left: 20px; margin-bottom: 20px; }
    .kb-content li { margin-bottom: 10px; }
</style>

<?php require __DIR__ . '/../global_footer.php'; ?>

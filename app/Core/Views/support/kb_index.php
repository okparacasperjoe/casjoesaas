<?php require __DIR__ . '/../global_header.php'; ?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #000066; font-weight: 700; margin: 0; font-size: 1.75rem;">Knowledge Base</h2>
        <a href="/support" style="color: var(--secondary); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px;"><ion-icon name="arrow-back-outline"></ion-icon> Back to Support</a>
    </div>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-8 mx-auto">
            <div class="card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 20px;">
                <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center;">
                    <h3 style="margin: 0; color: #fff;">How can we help you?</h3>
                    <p style="color: var(--text-muted); margin-top: 10px;">Browse our articles below to find answers to common questions.</p>
                </div>
                <div style="padding: 30px;">
                    <?php if (empty($articles)): ?>
                        <p style="text-align: center; color: var(--text-muted);">No articles found.</p>
                    <?php else: ?>
                        <div style="display: grid; gap: 15px;">
                            <?php foreach ($articles as $article): ?>
                                <a href="/kb/article/<?= htmlspecialchars($article['slug']) ?>" style="display: block; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 8px; text-decoration: none; transition: background 0.2s; border: 1px solid rgba(255,255,255,0.05);">
                                    <h4 style="color: #fff; margin: 0 0 5px 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                                        <ion-icon name="document-text-outline" style="color: var(--secondary);"></ion-icon> 
                                        <?= htmlspecialchars($article['title']) ?>
                                    </h4>
                                    <p style="color: var(--text-muted); margin: 0; font-size: 0.9rem;">
                                        <?= strip_tags(substr($article['content'], 0, 100)) ?>...
                                    </p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../global_footer.php'; ?>

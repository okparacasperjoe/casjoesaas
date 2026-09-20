<?php
$pageTitle = 'CMS Manager';
require __DIR__ . '/../header.php';
?>

<div class="top-bar" style="margin-bottom: 30px;">
    <h2 style="color: #FFA600; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="document-text-outline" style="font-size: 1.8rem; color: #FFA600;"></ion-icon> CMS Manager
    </h2>
</div>

<div class="settings-container" style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    <!-- Static Pages -->
    <div class="card mb-4" style="background:#13141f; padding:0; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.4); border:1px solid rgba(255, 166, 0, 0.2); overflow:hidden; margin-bottom: 30px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; gap: 8px; background: rgba(255, 166, 0, 0.05);">
            <ion-icon name="documents-outline" style="color: #FFA600; font-size: 1.3rem;"></ion-icon>
            <h4 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 1.05rem;">Static Pages</h4>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.92rem;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255, 166, 0, 0.3); background:#191b2a;">
                        <th style="padding:16px 24px; color:#FFA600; font-weight:600; width: 40%;">Title</th>
                        <th style="padding:16px 24px; color:#FFA600; font-weight:600; width: 40%;">Slug URL</th>
                        <th style="padding:16px 24px; color:#FFA600; font-weight:600; text-align:right; width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pages as $p): ?>
                    <tr style="border-bottom:1px solid rgba(255, 255, 255, 0.06); transition: background 0.2s;" onmouseover="this.style.background='rgba(255, 166, 0, 0.06)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:18px 24px; vertical-align:middle; font-weight:600; color:#ffffff;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <ion-icon name="document-outline" style="color:#FFA600; font-size:1.2rem;"></ion-icon>
                                <?= htmlspecialchars($p['title']) ?>
                            </div>
                        </td>
                        <td style="padding:18px 24px; vertical-align:middle;">
                            <a href="/<?= htmlspecialchars($p['slug']) ?>" target="_blank" style="color:#38bdf8; text-decoration:none; font-weight:500; font-family:monospace; font-size:0.85rem; background:rgba(255, 255, 255, 0.08); padding:6px 10px; border-radius:6px; border:1px solid rgba(255, 255, 255, 0.1);">
                                /<?= htmlspecialchars($p['slug']) ?> <ion-icon name="open-outline" style="vertical-align:middle; font-size:0.9rem; margin-left:2px;"></ion-icon>
                            </a>
                        </td>
                        <td style="padding:18px 24px; vertical-align:middle; text-align:right;">
                            <a href="/<?= ADMIN_PATH ?>/cms/page/<?= $p['id'] ?>" class="btn-primary" style="padding: 6px 12px; background: rgba(255, 255, 255, 0.08); color: #f1f5f9; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(255, 255, 255, 0.1); cursor: pointer; transition: all 0.2s;">
                                <ion-icon name="create-outline"></ion-icon> Edit
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Blog Posts -->
    <div class="card" style="background:#13141f; padding:0; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.4); border:1px solid rgba(255, 166, 0, 0.2); overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding: 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(255, 166, 0, 0.05);">
            <div style="display:flex; align-items:center; gap:8px;">
                <ion-icon name="newspaper-outline" style="color: #FFA600; font-size: 1.3rem;"></ion-icon>
                <h4 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 1.05rem;">Blog Posts</h4>
            </div>
            <a href="/<?= ADMIN_PATH ?>/cms/post/create" class="btn-primary" style="background-color:#FFA600; color:#090a0f; border:none; padding:8px 16px; border-radius:8px; font-weight:700; font-size:0.85rem; text-decoration:none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 166, 0, 0.25);">
                <ion-icon name="add-circle-outline" style="font-size:1.1rem;"></ion-icon> Create New Post
            </a>
        </div>
        
        <?php if(empty($posts)): ?>
            <div style="text-align:center; padding:50px 20px;">
                <ion-icon name="newspaper-outline" style="font-size:3rem; color:#64748b; margin-bottom:12px;"></ion-icon>
                <p style="color:#94a3b8; margin:0; font-size:0.9rem;">No blog posts found.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.92rem;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255, 166, 0, 0.3); background:#191b2a;">
                            <th style="padding:16px 24px; color:#FFA600; font-weight:600; width: 50%;">Title</th>
                            <th style="padding:16px 24px; color:#FFA600; font-weight:600; width: 20%;">Status</th>
                            <th style="padding:16px 24px; color:#FFA600; font-weight:600; text-align:right; width: 30%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($posts as $post): ?>
                        <tr style="border-bottom:1px solid rgba(255, 255, 255, 0.06); transition: background 0.2s;" onmouseover="this.style.background='rgba(255, 166, 0, 0.06)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:18px 24px; vertical-align:middle; font-weight:600; color:#ffffff;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <ion-icon name="book-outline" style="color:#FFA600; font-size:1.2rem; flex-shrink: 0;"></ion-icon>
                                    <span style="line-height: 1.4;"><?= htmlspecialchars(strip_tags($post['title'])) ?></span>
                                </div>
                            </td>
                            <td style="padding:18px 24px; vertical-align:middle;">
                                <?php if($post['is_published']): ?>
                                    <span style="background:rgba(16, 185, 129, 0.15); color:#10b981; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700; border: 1px solid rgba(16, 185, 129, 0.3);">Published</span>
                                <?php else: ?>
                                    <span style="background:rgba(255, 255, 255, 0.1); color:#94a3b8; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700; border: 1px solid rgba(255, 255, 255, 0.2);">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:18px 24px; vertical-align:middle; text-align:right;">
                                <div style="display:inline-flex; gap:8px; justify-content: flex-end;">
                                    <a href="/blog/<?= $post['slug'] ?>" target="_blank" class="btn-primary" style="padding: 6px 12px; background: rgba(56, 189, 248, 0.15); color: #38bdf8; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(56, 189, 248, 0.3); cursor: pointer; transition: all 0.2s;">
                                        <ion-icon name="eye-outline"></ion-icon> View
                                    </a>
                                    <a href="/<?= ADMIN_PATH ?>/cms/post/edit/<?= $post['id'] ?>" class="btn-primary" style="padding: 6px 12px; background: rgba(255, 255, 255, 0.08); color: #f1f5f9; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(255, 255, 255, 0.1); cursor: pointer; transition: all 0.2s;">
                                        <ion-icon name="create-outline"></ion-icon> Edit
                                    </a>
                                    <a href="/<?= ADMIN_PATH ?>/cms/post/delete/<?= $post['id'] ?>" onclick="return confirm('Delete this post?');" class="btn-primary" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.15); color: #ef4444; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(239, 68, 68, 0.3); cursor: pointer; transition: all 0.2s;">
                                        <ion-icon name="trash-outline"></ion-icon> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../footer.php'; ?>

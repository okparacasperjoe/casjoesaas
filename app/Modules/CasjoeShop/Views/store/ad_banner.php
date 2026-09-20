<?php
// Expects $banner_ad to be set (array from DB) 
if (!empty($banner_ad)): 
    // Defaults if not set in DB
    $bgStart = $banner_ad['bg_gradient_start'] ?? '#000066';
    $bgEnd = $banner_ad['bg_gradient_end'] ?? '#000044';
    $btnText = $banner_ad['button_text'] ?? 'Learn More';
    $btnLink = $banner_ad['button_link'] ?? '#';
?>
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(45deg, <?= htmlspecialchars($bgStart) ?> 0%, <?= htmlspecialchars($bgEnd) ?> 100%); overflow: hidden;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center position-relative">
        <div style="z-index: 2; position: relative;">
            <span class="badge bg-gold text-dark mb-2">Promoted</span>
            <h4 class="fw-bold text-white mb-1"><?= htmlspecialchars($banner_ad['title'] ?? 'Advertisement') ?></h4>
            <p class="mb-0 text-white-50">
                <?= htmlspecialchars($banner_ad['description'] ?? '') ?> 
                <a href="<?= htmlspecialchars($btnLink) ?>" class="text-gold text-decoration-none fw-bold ms-2">
                    <?= htmlspecialchars($btnText) ?> &rarr;
                </a>
            </p>
        </div>
        
        <?php if(!empty($banner_ad['image_url'])): ?>
            <div class="d-none d-md-block" style="height: 100%; position: absolute; right: 0; top: 0; width: 40%; background-image: url('<?= htmlspecialchars($banner_ad['image_url']) ?>'); background-size: cover; background-position: center; mask-image: linear-gradient(to right, transparent, black); -webkit-mask-image: linear-gradient(to right, transparent, black);"></div>
        <?php else: ?>
            <div class="d-none d-md-block">
                <i class="bi bi-megaphone-fill fs-1 text-gold"></i>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


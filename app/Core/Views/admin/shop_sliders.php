<?php
$pageTitle = 'Shop Sliders & Carousel';
require __DIR__ . '/header.php';
?>

<div class="top-bar" style="margin-bottom: 30px;">
    <h2 style="color: #FFA600; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="images-outline" style="font-size: 1.8rem; color: #FFA600;"></ion-icon> E-Commerce Hero Sliders
    </h2>
</div>

<div class="settings-container" style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    <div class="card" style="background:#13141f; padding:30px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.4); border:1px solid rgba(255, 166, 0, 0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="color:#ffffff; margin:0 0 8px 0; font-weight:700;">Marketplace Hero Sliders & Banners</h3>
                <p style="color:#94a3b8; margin:0; font-size:0.95rem;">Configure the high-impact promotional carousel slides shown on the storefront homepage and main category headers.</p>
            </div>
            <button onclick="alert('Slider uploader and animation config manager is ready for hero graphic deployment.');" class="btn-primary" style="background-color:#FFA600; color:#090a0f; border:none; padding:10px 20px; border-radius:8px; font-weight:700; font-size:0.9rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 166, 0, 0.25);">
                <ion-icon name="add-circle-outline" style="font-size:1.2rem;"></ion-icon> Upload New Slide Banner
            </button>
        </div>

        <div style="background: rgba(255, 166, 0, 0.05); border: 1px solid rgba(255, 166, 0, 0.2); border-radius: 10px; padding: 25px; text-align: center; margin: 20px 0;">
            <ion-icon name="easel-outline" style="font-size: 3.5rem; color: #FFA600; margin-bottom: 12px;"></ion-icon>
            <h4 style="color: #ffffff; font-weight: 700; margin-bottom: 8px;">Homepage Hero Carousel Configuration</h4>
            <p style="color: #94a3b8; max-width: 600px; margin: 0 auto 20px auto; line-height: 1.6;">Upload promotional slide banners with call-to-action buttons to highlight mega sales, seasonal discounts, and brand launches across Casjoe Marketplace.</p>
            <div style="display: inline-flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ High-Res Retina Support</span>
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ Custom Target Links</span>
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ Auto-Play & Transition Timing</span>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>

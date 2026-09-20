<?php
$pageTitle = 'Shop Ads & Banners';
require __DIR__ . '/header.php';
?>

<div class="top-bar" style="margin-bottom: 30px;">
    <h2 style="color: #FFA600; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="pricetag-outline" style="font-size: 1.8rem; color: #FFA600;"></ion-icon> Shop Advertising Campaigns
    </h2>
</div>

<div class="settings-container" style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    <div class="card" style="background:#13141f; padding:30px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.4); border:1px solid rgba(255, 166, 0, 0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="color:#ffffff; margin:0 0 8px 0; font-weight:700;">Active Store Promotions & Banner Ads</h3>
                <p style="color:#94a3b8; margin:0; font-size:0.95rem;">Manage featured shop banners, sponsored products, and merchant promotional spots across Casjoe E-Commerce stores.</p>
            </div>
            <button onclick="alert('Promotional campaign builder and banner allocation engine is active and ready for store placement selection.');" class="btn-primary" style="background-color:#FFA600; color:#090a0f; border:none; padding:10px 20px; border-radius:8px; font-weight:700; font-size:0.9rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(255, 166, 0, 0.25);">
                <ion-icon name="add-circle-outline" style="font-size:1.2rem;"></ion-icon> Create New Ad Campaign
            </button>
        </div>

        <div style="background: rgba(255, 166, 0, 0.05); border: 1px solid rgba(255, 166, 0, 0.2); border-radius: 10px; padding: 25px; text-align: center; margin: 20px 0;">
            <ion-icon name="megaphone-outline" style="font-size: 3.5rem; color: #FFA600; margin-bottom: 12px;"></ion-icon>
            <h4 style="color: #ffffff; font-weight: 700; margin-bottom: 8px;">No Active Sponsored Shop Campaigns</h4>
            <p style="color: #94a3b8; max-width: 600px; margin: 0 auto 20px auto; line-height: 1.6;">When stores subscribe to promotional banner spots or featured category placement, their active ad units and performance impressions will appear here.</p>
            <div style="display: inline-flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ Category Top Banners</span>
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ Featured Store Placement</span>
                <span style="background: rgba(255,255,255,0.06); color: #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; border: 1px solid rgba(255,255,255,0.1);">✓ Product Spotlight Ads</span>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>

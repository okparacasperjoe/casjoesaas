<style>
/* Expand container for wide, compact grid layout */
.onboarding-container {
    max-width: 960px !important;
    padding: 30px 35px !important;
    transition: max-width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@media (max-width: 768px) {
    .onboarding-container { padding: 20px 15px !important; }
}

.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 14px;
    margin-top: 20px;
    margin-bottom: 30px;
}
@media (max-width: 600px) {
    .modules-grid { grid-template-columns: 1fr; gap: 10px; }
}

.module-card {
    position: relative;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.module-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-accent, #000066);
    transition: height 0.25s ease;
}

.module-card:hover {
    transform: translateY(-3px);
    border-color: var(--card-accent, #000066);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
}

.module-card.is-selected {
    border-color: var(--card-accent, #000066);
    background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(248, 250, 252, 0.95) 100%);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06), 0 0 0 2px var(--card-accent-alpha, rgba(0, 0, 102, 0.15));
}

.module-card.is-selected::before {
    height: 5px;
}

.module-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.module-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--card-accent-alpha, rgba(0, 0, 102, 0.1));
    color: var(--card-accent, #000066);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.module-check {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 1.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    transition: all 0.2s ease;
    background: #fff;
    font-size: 0.75rem;
}

/* Custom ON/OFF Pill Switch matching uploaded image exactly */
.pill-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 64px;
    height: 30px;
    border-radius: 30px;
    cursor: pointer;
    user-select: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
    overflow: hidden;
}

/* When OFF (Unchecked) - Yellow/Amber color */
.pill-switch.pill-off {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: 2px solid #b45309;
}

/* When ON (Checked) - Vibrant Blue color */
.pill-switch.pill-on {
    background: linear-gradient(135deg, #000066 0%, #0052cc 100%);
    border: 2px solid #00004d;
}

/* Inner Text (ON / OFF) */
.pill-switch .pill-text {
    font-size: 0.75rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.5px;
    line-height: 1;
    z-index: 2;
    transition: all 0.3s ease;
}

/* Text positions */
.pill-switch.pill-on .pill-text {
    margin-left: 10px;
}
.pill-switch.pill-off .pill-text {
    margin-left: 26px;
}

/* White Circle Knob */
.pill-switch .pill-knob {
    position: absolute;
    top: 3px;
    width: 20px;
    height: 20px;
    background: #ffffff;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.25);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 3;
}

/* Knob positions */
.pill-switch.pill-on .pill-knob {
    left: 38px;
}
.pill-switch.pill-off .pill-knob {
    left: 3px;
}

.module-title {
    font-size: 1.02rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
    line-height: 1.3;
}

.module-desc {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.45;
}
</style>

<div class="text-center mb-2">
    <span class="badge rounded-pill bg-warning text-dark px-3 py-1 mb-2 fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        ⚡ TAILOR YOUR WORKSPACE
    </span>
    <h3 class="fw-bold mb-2" style="color: #0f172a; font-size: 1.6rem;">Select Your Ecosystem Modules</h3>
    <p class="text-muted mx-auto small" style="max-width: 620px; line-height: 1.5;">
        Choose the exact superpowers to activate for your business right now. Click any module card or toggle to switch between ON and OFF.
    </p>
</div>

<?php
// Assign vibrant color palettes & icons to each module slug
$moduleThemes = [
    'casjoe-bos' => ['accent' => '#000066', 'alpha' => 'rgba(0, 0, 102, 0.12)', 'icon' => '🏢'],
    'casjoe-erp' => ['accent' => '#000066', 'alpha' => 'rgba(0, 0, 102, 0.12)', 'icon' => '🏢'],
    'casjoe-mart' => ['accent' => '#FFA600', 'alpha' => 'rgba(255, 166, 0, 0.18)', 'icon' => '🛍️'],
    'casjoe-shop' => ['accent' => '#FFA600', 'alpha' => 'rgba(255, 166, 0, 0.18)', 'icon' => '🛍️'],
    'casjoe-pay' => ['accent' => '#10B981', 'alpha' => 'rgba(16, 185, 129, 0.15)', 'icon' => '💳'],
    'casjoe-cloud' => ['accent' => '#3B82F6', 'alpha' => 'rgba(59, 130, 246, 0.15)', 'icon' => '☁️'],
    'casjoe-academy' => ['accent' => '#8B5CF6', 'alpha' => 'rgba(139, 92, 246, 0.15)', 'icon' => '🎓'],
    'casjoe-ads' => ['accent' => '#EF4444', 'alpha' => 'rgba(239, 68, 68, 0.15)', 'icon' => '📢'],
    'casjoe-crm' => ['accent' => '#06B6D4', 'alpha' => 'rgba(6, 182, 212, 0.15)', 'icon' => '🤝'],
    'casjoe-hrm' => ['accent' => '#EC4899', 'alpha' => 'rgba(236, 72, 153, 0.15)', 'icon' => '👥']
];
?>

<form action="/onboarding/step5" method="POST" id="modulesForm">
    <div class="modules-grid">
        <?php foreach ($modules as $module): 
            $slug = $module['slug'];
            $theme = $moduleThemes[$slug] ?? ['accent' => '#000066', 'alpha' => 'rgba(0, 0, 102, 0.12)', 'icon' => '⚡'];
        ?>
        <div class="module-card is-selected" style="--card-accent: <?= $theme['accent'] ?>; --card-accent-alpha: <?= $theme['alpha'] ?>;" onclick="toggleModuleCard(this)">
            <input type="checkbox" name="modules[]" value="<?= htmlspecialchars($slug) ?>" checked class="d-none module-checkbox">
            
            <div>
                <div class="module-card-header">
                    <div class="module-icon-wrap">
                        <?= $theme['icon'] ?>
                    </div>
                    <div class="pill-switch pill-on" title="Click to toggle ON/OFF">
                        <span class="pill-text">ON</span>
                        <span class="pill-knob"></span>
                    </div>
                </div>

                <div class="module-title"><?= htmlspecialchars($module['name']) ?></div>

                <div class="module-desc">
                    <?= htmlspecialchars($module['description']) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow-lg" style="font-size: 1.15rem; background: linear-gradient(135deg, #000066 0%, #1a1a80 100%); border: none; min-width: 320px;">
            Activate Selected Modules <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
function toggleModuleCard(card) {
    const checkbox = card.querySelector('.module-checkbox');
    const pill = card.querySelector('.pill-switch');
    const pillText = pill.querySelector('.pill-text');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        card.classList.add('is-selected');
        pill.className = 'pill-switch pill-on';
        pillText.textContent = 'ON';
    } else {
        card.classList.remove('is-selected');
        pill.className = 'pill-switch pill-off';
        pillText.textContent = 'OFF';
    }
}
</script>

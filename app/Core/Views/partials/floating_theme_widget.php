<?php
if (isset($GLOBALS['floating_theme_widget_loaded'])) {
    return;
}
$GLOBALS['floating_theme_widget_loaded'] = true;
?>
<style>
    #floating-theme-widget {
        position: fixed;
        top: 50%;
        right: 24px;
        transform: translateY(-50%);
        z-index: 999999;
    }

    .ftw-switch {
        position: relative;
        display: inline-block;
        width: 104px;
        height: 42px;
    }
    
    .ftw-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .ftw-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #030414;
        border-radius: 42px;
        /* Brand color glowing border */
        box-shadow: 0 0 0 2px #000066, 0 0 12px rgba(0, 0, 102, 0.8), inset 0 0 15px rgba(0,0,0,0.8);
        transition: .4s;
        overflow: hidden;
        display: flex;
        align-items: center;
    }
    
    .ftw-label {
        position: absolute;
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 0.85rem;
        font-weight: 800;
        transition: .4s;
        pointer-events: none;
        letter-spacing: 1px;
    }
    
    /* "DARK" text is visible when unchecked (knob is on the left) */
    .ftw-label-dark {
        right: 14px;
        color: rgba(255, 166, 0, 0.65);
    }
    
    /* "LIGHT" text is visible when checked (knob is on the right) */
    .ftw-label-light {
        left: 12px;
        color: rgba(0, 0, 102, 0.75);
        opacity: 0;
    }
    
    .ftw-knob {
        position: absolute;
        height: 34px;
        width: 34px;
        left: 4px;
        bottom: 4px;
        background-color: #0a0e27;
        border-radius: 50%;
        transition: .4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        display: flex;
        align-items: center;
        justify-content: center;
        /* Knob border and glow */
        box-shadow: 0 0 0 2px #FFA600, 0 0 10px rgba(255, 166, 0, 0.5), inset 0 0 8px rgba(0,0,0,0.5);
    }
    
    .ftw-knob ion-icon {
        font-size: 1.2rem;
        transition: .4s;
        position: absolute;
    }
    
    .ftw-moon { 
        color: #FFA600; 
        opacity: 1; 
        transform: scale(1) rotate(0); 
        filter: drop-shadow(0 0 4px rgba(255, 166, 0, 0.6));
    }
    .ftw-sun { 
        color: #000066; 
        opacity: 0; 
        transform: scale(0.5) rotate(180deg); 
    }
    
    /* Checked State (Light Mode) */
    .ftw-switch input:checked + .ftw-slider {
        background-color: #ffffff;
        box-shadow: 0 0 0 2px #FFA600, 0 0 12px rgba(255, 166, 0, 0.5), inset 0 0 10px rgba(0,0,0,0.05);
    }
    
    .ftw-switch input:checked + .ftw-slider .ftw-label-dark {
        opacity: 0;
    }
    
    .ftw-switch input:checked + .ftw-slider .ftw-label-light {
        opacity: 1;
    }
    
    .ftw-switch input:checked + .ftw-slider .ftw-knob {
        transform: translateX(62px);
        background-color: #f8fafc;
        box-shadow: 0 0 0 2px #000066, 0 0 10px rgba(0, 0, 102, 0.3), inset 0 0 5px rgba(255,255,255,0.8);
    }
    
    .ftw-switch input:checked + .ftw-slider .ftw-moon {
        opacity: 0; 
        transform: scale(0.5) rotate(-180deg); 
    }
    
    .ftw-switch input:checked + .ftw-slider .ftw-sun {
        opacity: 1; 
        transform: scale(1) rotate(0); 
        filter: drop-shadow(0 0 2px rgba(0, 0, 102, 0.3));
    }

    /* Media query to ensure it doesn't overlap weirdly on mobile if left: 24px is too far */
    @media (max-width: 768px) {
        #floating-theme-widget {
            right: 15px;
        }
    }
</style>

<div id="floating-theme-widget">
    <label class="ftw-switch">
        <input type="checkbox" id="ftw-checkbox">
        <div class="ftw-slider">
            <span class="ftw-label ftw-label-dark">DARK</span>
            <span class="ftw-label ftw-label-light">LIGHT</span>
            <div class="ftw-knob">
                <ion-icon name="moon" class="ftw-moon"></ion-icon>
                <ion-icon name="sunny" class="ftw-sun"></ion-icon>
            </div>
        </div>
    </label>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ftwCheckbox = document.getElementById('ftw-checkbox');
    if(!ftwCheckbox) return;

    // Check localStorage to set initial toggle state
    const currentTheme = localStorage.getItem('casjoe_theme');
    if (currentTheme === 'light') {
        ftwCheckbox.checked = true;
    } else {
        ftwCheckbox.checked = false;
    }

    ftwCheckbox.addEventListener('change', function() {
        if (this.checked) {
            document.documentElement.classList.add('light-theme');
            document.documentElement.classList.remove('dark-theme');
            localStorage.setItem('casjoe_theme', 'light');
        } else {
            document.documentElement.classList.add('dark-theme');
            document.documentElement.classList.remove('light-theme');
            localStorage.setItem('casjoe_theme', 'dark');
        }
    });
});
</script>

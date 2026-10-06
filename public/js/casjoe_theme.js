// Casjoe Theme Loader & Accessibility Pre-Paint Engine
// This script runs immediately in the <head> to prevent Flash of Unstyled Content (FOUC).
(function() {
    // 1. Theme Loading
    const currentTheme = localStorage.getItem('casjoe_theme') || localStorage.getItem('theme');
    if (currentTheme === 'light') {
        document.documentElement.classList.add('light-theme');
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.classList.remove('dark-theme');
    } else {
        document.documentElement.classList.add('dark-theme');
        document.documentElement.setAttribute('data-theme', 'dark');
        document.documentElement.classList.remove('light-theme');
    }

    // 2. Accessibility (PWD) Pre-Paint Class Application
    try {
        const a11yRaw = localStorage.getItem('casjoe_a11y');
        if (a11yRaw) {
            const a11y = JSON.parse(a11yRaw);
            const doc = document.documentElement;
            if (a11y.fontSize === 'lg') doc.classList.add('a11y-font-lg');
            if (a11y.fontSize === 'xl') doc.classList.add('a11y-font-xl');
            if (a11y.dyslexia) doc.classList.add('a11y-dyslexia');
            if (a11y.contrast === 'dark') doc.classList.add('a11y-contrast-dark');
            if (a11y.contrast === 'light') doc.classList.add('a11y-contrast-light');
            if (a11y.contrast === 'mono') doc.classList.add('a11y-monochrome');
            if (a11y.highlightLinks) doc.classList.add('a11y-highlight-links');
            if (a11y.stopMotion) doc.classList.add('a11y-stop-motion');
            if (a11y.readingRuler) doc.classList.add('a11y-ruler-active');
            if (a11y.readingMask) doc.classList.add('a11y-mask-active');
            if (a11y.enhancedFocus) doc.classList.add('a11y-focus-ring');
            if (a11y.ttsActive) doc.classList.add('a11y-tts-active');
        }
    } catch(e) {}

    // 3. Auto-load Accessibility Suite Assets Asynchronously
    if (typeof document !== 'undefined') {
        function loadA11yAssets() {
            if (!document.getElementById('casjoe-a11y-css')) {
                const link = document.createElement('link');
                link.id = 'casjoe-a11y-css';
                link.rel = 'stylesheet';
                link.href = '/css/casjoe_a11y.css?v=2.2';
                document.head.appendChild(link);
            }
            if (!document.getElementById('casjoe-a11y-js')) {
                const script = document.createElement('script');
                script.id = 'casjoe-a11y-js';
                script.src = '/js/casjoe_a11y.js';
                script.defer = true;
                document.head.appendChild(script);
            }
        }
        if (document.head) {
            loadA11yAssets();
        } else {
            document.addEventListener('DOMContentLoaded', loadA11yAssets);
        }
    }
})();

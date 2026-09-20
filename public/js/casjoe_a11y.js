/**
 * Casjoe LLC: PWD Universal Accessibility Engine (WCAG 2.1 Level AA)
 * Lightweight, zero-dependency client-side accessibility engine with
 * Text-to-Speech (TTS), Reading Focus Mask, Reading Ruler & Keyboard Skip Links.
 */
(function() {
    'use strict';

    if (window.__casjoeA11yLoaded) return;
    window.__casjoeA11yLoaded = true;

    const STORAGE_KEY = 'casjoe_a11y';

    const defaultState = {
        fontSize: 'normal',   // 'normal' | 'lg' | 'xl'
        dyslexia: false,
        contrast: 'none',     // 'none' | 'dark' | 'light' | 'mono'
        highlightLinks: false,
        stopMotion: false,
        readingRuler: false,
        readingMask: false,
        enhancedFocus: false,
        ttsActive: false,
        ttsRate: 1.0          // 0.8 | 1.0 | 1.2
    };

    function getState() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? Object.assign({}, defaultState, JSON.parse(raw)) : defaultState;
        } catch (e) {
            return defaultState;
        }
    }

    function saveState(state) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {}
    }

    function applyState(state) {
        const doc = document.documentElement;

        // 1. Font Size
        doc.classList.remove('a11y-font-lg', 'a11y-font-xl');
        if (state.fontSize === 'lg') doc.classList.add('a11y-font-lg');
        if (state.fontSize === 'xl') doc.classList.add('a11y-font-xl');

        // 2. Dyslexia Font
        doc.classList.toggle('a11y-dyslexia', !!state.dyslexia);

        // 3. Contrast Modes
        doc.classList.remove('a11y-contrast-dark', 'a11y-contrast-light', 'a11y-monochrome');
        if (state.contrast === 'dark') doc.classList.add('a11y-contrast-dark');
        if (state.contrast === 'light') doc.classList.add('a11y-contrast-light');
        if (state.contrast === 'mono') doc.classList.add('a11y-monochrome');

        // 4. Highlight Links
        doc.classList.toggle('a11y-highlight-links', !!state.highlightLinks);

        // 5. Stop Motion
        doc.classList.toggle('a11y-stop-motion', !!state.stopMotion);

        // 6. Reading Ruler
        doc.classList.toggle('a11y-ruler-active', !!state.readingRuler);

        // 7. Reading Focus Mask
        doc.classList.toggle('a11y-mask-active', !!state.readingMask);

        // 8. Enhanced Focus Rings
        doc.classList.toggle('a11y-focus-ring', !!state.enhancedFocus);

        // 9. Text-to-Speech
        doc.classList.toggle('a11y-tts-active', !!state.ttsActive);
        if (!state.ttsActive && 'speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }

        // Update UI Controls
        syncUI(state);
    }

    function syncUI(state) {
        const panel = document.getElementById('casjoe-a11y-panel');
        if (!panel) return;

        // Font buttons
        panel.querySelectorAll('[data-a11y-font]').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-a11y-font') === state.fontSize);
        });

        // Contrast buttons
        panel.querySelectorAll('[data-a11y-contrast]').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-a11y-contrast') === state.contrast);
        });

        // TTS Speed buttons
        panel.querySelectorAll('[data-a11y-speed]').forEach(btn => {
            btn.classList.toggle('active', parseFloat(btn.getAttribute('data-a11y-speed')) === parseFloat(state.ttsRate));
        });

        // Toggles
        const dyslexiaInput = panel.querySelector('#a11y-toggle-dyslexia');
        if (dyslexiaInput) dyslexiaInput.checked = !!state.dyslexia;

        const linksInput = panel.querySelector('#a11y-toggle-links');
        if (linksInput) linksInput.checked = !!state.highlightLinks;

        const motionInput = panel.querySelector('#a11y-toggle-motion');
        if (motionInput) motionInput.checked = !!state.stopMotion;

        const rulerInput = panel.querySelector('#a11y-toggle-ruler');
        if (rulerInput) rulerInput.checked = !!state.readingRuler;

        const maskInput = panel.querySelector('#a11y-toggle-mask');
        if (maskInput) maskInput.checked = !!state.readingMask;

        const focusInput = panel.querySelector('#a11y-toggle-focus');
        if (focusInput) focusInput.checked = !!state.enhancedFocus;

        const ttsInput = panel.querySelector('#a11y-toggle-tts');
        if (ttsInput) ttsInput.checked = !!state.ttsActive;
    }

    function injectWidget() {
        if (document.getElementById('casjoe-a11y-btn')) return;

        // 1. Skip to Content Link
        if (!document.getElementById('a11y-skip-link')) {
            const skipLink = document.createElement('a');
            skipLink.id = 'a11y-skip-link';
            skipLink.className = 'a11y-skip-link';
            skipLink.href = '#main-content';
            skipLink.textContent = 'Skip to Main Content (Tab)';
            skipLink.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.getElementById('main-content') ||
                               document.querySelector('main, [role="main"], .main-content, .app-container');
                if (target) {
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
            document.body.insertBefore(skipLink, document.body.firstChild);
        }

        // 2. Reading Ruler & Mask Elements
        const ruler = document.createElement('div');
        ruler.id = 'a11y-ruler-line';
        document.body.appendChild(ruler);

        const maskTop = document.createElement('div');
        maskTop.id = 'a11y-mask-top';
        const maskBottom = document.createElement('div');
        maskBottom.id = 'a11y-mask-bottom';
        document.body.appendChild(maskTop);
        document.body.appendChild(maskBottom);

        window.addEventListener('mousemove', function(e) {
            const doc = document.documentElement;
            if (doc.classList.contains('a11y-ruler-active')) {
                ruler.style.top = e.clientY + 'px';
            }
            if (doc.classList.contains('a11y-mask-active')) {
                const bandHeight = 110;
                const topH = Math.max(0, e.clientY - (bandHeight / 2));
                const botH = Math.max(0, window.innerHeight - (e.clientY + (bandHeight / 2)));
                maskTop.style.height = topH + 'px';
                maskBottom.style.height = botH + 'px';
            }
        }, { passive: true });

        // 3. Floating Launcher Button
        const btn = document.createElement('button');
        btn.id = 'casjoe-a11y-btn';
        btn.setAttribute('type', 'button');
        btn.setAttribute('aria-label', 'Open Accessibility Settings (Alt + A)');
        btn.setAttribute('title', 'Accessibility Settings (Alt + A)');
        btn.innerHTML = `
            <svg viewBox="0 0 24 24" class="a11y-icon" aria-hidden="true">
                <circle cx="12" cy="4" r="2"/>
                <path d="M19 13v-2c-1.54.02-3.09-.75-4.07-1.83l-1.29-1.43c-.17-.19-.38-.34-.61-.45-.01 0-.01 0-.02-.01H13c-.35-.2-.75-.3-1.19-.26-.62.05-1.2.39-1.53.94l-2 3.46c-.27.47-.11 1.07.36 1.34.47.27 1.07.11 1.34-.36L11 10.37V15l-3.23 1.62c-.46.23-.65.79-.42 1.25.23.46.79.65 1.25.42L12 16.59l3.4 1.7c.18.09.37.13.56.13.34 0 .67-.14.9-.42.33-.4.27-.99-.13-1.32L15 15.22V13h4z"/>
            </svg>
        `;
        document.body.appendChild(btn);

        // 4. Floating Accessibility Panel
        const panel = document.createElement('div');
        panel.id = 'casjoe-a11y-panel';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', 'Accessibility Controls');
        panel.innerHTML = `
            <div class="a11y-header">
                <h3 class="a11y-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path d="M19 13v-2c-1.54.02-3.09-.75-4.07-1.83l-1.29-1.43c-.17-.19-.38-.34-.61-.45-.01 0-.01 0-.02-.01H13c-.35-.2-.75-.3-1.19-.26-.62.05-1.2.39-1.53.94l-2 3.46c-.27.47-.11 1.07.36 1.34.47.27 1.07.11 1.34-.36L11 10.37V15l-3.23 1.62c-.46.23-.65.79-.42 1.25.23.46.79.65 1.25.42L12 16.59l3.4 1.7c.18.09.37.13.56.13.34 0 .67-.14.9-.42.33-.4.27-.99-.13-1.32L15 15.22V13h4z"/></svg>
                    Accessibility Suite
                </h3>
                <button type="button" class="a11y-close-btn" id="a11y-close-btn" aria-label="Close accessibility settings">&times;</button>
            </div>
            <div class="a11y-body">
                <!-- Text Size -->
                <div>
                    <div class="a11y-section-title">Text Size</div>
                    <div class="a11y-btn-group">
                        <button type="button" class="a11y-btn" data-a11y-font="normal">Normal</button>
                        <button type="button" class="a11y-btn" data-a11y-font="lg">Large +15%</button>
                        <button type="button" class="a11y-btn" data-a11y-font="xl">Extra +30%</button>
                    </div>
                </div>

                <!-- Contrast Modes -->
                <div>
                    <div class="a11y-section-title">Contrast Modes</div>
                    <div class="a11y-btn-group-2">
                        <button type="button" class="a11y-btn" data-a11y-contrast="none">Default</button>
                        <button type="button" class="a11y-btn" data-a11y-contrast="dark">High Contrast Dark</button>
                        <button type="button" class="a11y-btn" data-a11y-contrast="light">High Contrast Light</button>
                        <button type="button" class="a11y-btn" data-a11y-contrast="mono">Monochrome</button>
                    </div>
                </div>

                <!-- Text-to-Speech (TTS) Voice Reader -->
                <div>
                    <div class="a11y-section-title">🔊 Text-to-Speech (Voice Reader)</div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">🎙️ Click-to-Speak Aloud</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-tts">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <div style="display:flex; gap:6px; align-items:center; margin-top:2px;">
                            <span style="font-size:0.75rem; color:#94a3b8; font-weight:600;">Speed:</span>
                            <div class="a11y-btn-group" style="flex:1;">
                                <button type="button" class="a11y-btn" data-a11y-speed="0.8">0.8x</button>
                                <button type="button" class="a11y-btn" data-a11y-speed="1.0">1.0x</button>
                                <button type="button" class="a11y-btn" data-a11y-speed="1.2">1.2x</button>
                            </div>
                        </div>
                        <button type="button" class="a11y-btn" id="a11y-stop-tts" style="background:#271818; border-color:#ef4444; color:#ef4444;">
                            ⏹️ Stop Voice Reader
                        </button>
                    </div>
                </div>

                <!-- Cognitive & Visual Aids -->
                <div>
                    <div class="a11y-section-title">Focus & Reading Helpers</div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">📖 Dyslexia-Friendly Font</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-dyslexia">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">📏 Reading Guide Ruler</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-ruler">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">🔦 Focus Mask (Dim Outside)</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-mask">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">🎯 Enhanced Focus Rings</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-focus">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">🔗 Highlight Links & Buttons</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-links">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                        <label class="a11y-toggle-row">
                            <span class="a11y-toggle-label">⏸️ Stop Motion & Animations</span>
                            <span class="a11y-switch">
                                <input type="checkbox" id="a11y-toggle-motion">
                                <span class="a11y-switch-slider"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="a11y-footer">
                <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                    <button type="button" class="a11y-reset-btn" id="a11y-reset-btn">Reset All</button>
                    <span class="a11y-hint">Shortcut: <kbd style="background:#1f2937; padding:2px 4px; border-radius:4px; font-size:0.7rem;">Alt + A</kbd></span>
                </div>
                <div style="margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.08); text-align:center; width:100%;">
                    <a href="/accessibility" target="_blank" class="a11y-doc-link" style="color:#FFA600; font-size:0.75rem; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                        <span>📜 WCAG 2.1 AA Statement &amp; Shortcuts &rarr;</span>
                    </a>
                </div>
            </div>
        `;
        document.body.appendChild(panel);

        // Bind Events & Actions
        const currentState = getState();
        syncUI(currentState);

        function togglePanel() {
            panel.classList.toggle('is-open');
            if (panel.classList.contains('is-open')) {
                const firstBtn = panel.querySelector('button');
                if (firstBtn) firstBtn.focus();
            }
        }

        btn.addEventListener('click', togglePanel);
        document.getElementById('a11y-close-btn')?.addEventListener('click', function() {
            panel.classList.remove('is-open');
        });

        // Close on Escape & Shortcut Alt + A
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && panel.classList.contains('is-open')) {
                panel.classList.remove('is-open');
                btn.focus();
            }
            if (e.altKey && (e.key === 'a' || e.key === 'A')) {
                e.preventDefault();
                togglePanel();
            }
        });

        // Click outside closes panel
        document.addEventListener('click', function(e) {
            if (panel.classList.contains('is-open') && !panel.contains(e.target) && !btn.contains(e.target)) {
                panel.classList.remove('is-open');
            }
        });

        // Font selection
        panel.querySelectorAll('[data-a11y-font]').forEach(fBtn => {
            fBtn.addEventListener('click', function() {
                const state = getState();
                state.fontSize = this.getAttribute('data-a11y-font');
                saveState(state);
                applyState(state);
            });
        });

        // Contrast selection
        panel.querySelectorAll('[data-a11y-contrast]').forEach(cBtn => {
            cBtn.addEventListener('click', function() {
                const state = getState();
                state.contrast = this.getAttribute('data-a11y-contrast');
                saveState(state);
                applyState(state);
            });
        });

        // TTS Speed selection
        panel.querySelectorAll('[data-a11y-speed]').forEach(sBtn => {
            sBtn.addEventListener('click', function() {
                const state = getState();
                state.ttsRate = parseFloat(this.getAttribute('data-a11y-speed')) || 1.0;
                saveState(state);
                applyState(state);
            });
        });

        // Toggle switches
        document.getElementById('a11y-toggle-dyslexia')?.addEventListener('change', function() {
            const state = getState();
            state.dyslexia = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-links')?.addEventListener('change', function() {
            const state = getState();
            state.highlightLinks = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-motion')?.addEventListener('change', function() {
            const state = getState();
            state.stopMotion = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-ruler')?.addEventListener('change', function() {
            const state = getState();
            state.readingRuler = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-mask')?.addEventListener('change', function() {
            const state = getState();
            state.readingMask = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-focus')?.addEventListener('change', function() {
            const state = getState();
            state.enhancedFocus = this.checked;
            saveState(state);
            applyState(state);
        });

        document.getElementById('a11y-toggle-tts')?.addEventListener('change', function() {
            const state = getState();
            state.ttsActive = this.checked;
            saveState(state);
            applyState(state);
        });

        // Stop Voice Reader button
        document.getElementById('a11y-stop-tts')?.addEventListener('click', function() {
            stopSpeaking();
        });

        // Reset button
        document.getElementById('a11y-reset-btn')?.addEventListener('click', function() {
            stopSpeaking();
            const reset = Object.assign({}, defaultState);
            saveState(reset);
            applyState(reset);
        });

        // ── Text-to-Speech (TTS) Voice Engine ──
        let currentSpeakingEl = null;

        function speakText(text) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            if (!text || !text.trim()) return;

            const utterance = new SpeechSynthesisUtterance(text.trim());
            const state = getState();
            utterance.rate = state.ttsRate || 1.0;

            utterance.onend = function() {
                if (currentSpeakingEl) {
                    currentSpeakingEl.classList.remove('a11y-speaking-target');
                    currentSpeakingEl = null;
                }
            };
            utterance.onerror = function() {
                if (currentSpeakingEl) {
                    currentSpeakingEl.classList.remove('a11y-speaking-target');
                    currentSpeakingEl = null;
                }
            };

            window.speechSynthesis.speak(utterance);
        }

        function stopSpeaking() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
            if (currentSpeakingEl) {
                currentSpeakingEl.classList.remove('a11y-speaking-target');
                currentSpeakingEl = null;
            }
        }

        // Click-to-Speak listener
        document.addEventListener('click', function(e) {
            const state = getState();
            if (!state.ttsActive) return;

            // Never speak if clicking inside panel or accessibility trigger button
            if (panel.contains(e.target) || btn.contains(e.target)) return;

            const target = e.target.closest('p, h1, h2, h3, h4, h5, h6, li, td, th, label, .card-title, .card-text, .alert, blockquote');
            if (!target) return;

            stopSpeaking();
            currentSpeakingEl = target;
            target.classList.add('a11y-speaking-target');

            const text = target.innerText || target.textContent;
            speakText(text);
        });
    }

    // Apply saved accessibility classes immediately (pre-paint)
    const initial = getState();
    applyState(initial);

    // Inject UI on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectWidget);
    } else {
        injectWidget();
    }
})();

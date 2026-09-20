<?php
$pageTitle = 'Integrations';
require __DIR__ . '/../header.php';
?>
<style>
        .settings-container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: transparent;
            padding: 0; 
            display: flex;
            flex-direction: column;
            min-height: 500px;
        }
        
        /* Tabs Container Header */
        .tabs-header-container {
            background: var(--glass-bg);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Divider */
            padding: 0 20px;
            overflow-x: auto;
        }
        
        .tab-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 20px 25px;
            cursor: pointer;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            white-space: nowrap;
        }
        
        .tab-btn:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        /* Active Indicator */
        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -1px; /* Overlap the divider */
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary); /* The purple indicator from the image */
            border-radius: 4px 4px 0 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tab-btn.active {
            color: white;
            font-weight: 600;
        }
        
        .tab-btn.active::after {
            opacity: 1;
        }
        
        .tab-btn ion-icon {
            font-size: 1.3rem;
        }
        
        /* Tab Content Area */
        .tab-content-area {
            background: var(--glass-bg);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px;
            position: relative;
            flex-grow: 1;
        }
        
        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form Styles */
        .form-section h3 { 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            margin-top: 0;
            color: white; 
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.1);
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .switch-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .switch-label {
            color: white;
            font-weight: bold;
        }

        /* Basic CSS Switch */
        .switch {
          position: relative;
          display: inline-block;
          width: 50px;
          height: 24px;
        }
        
        .switch input { 
          opacity: 0;
          width: 0;
          height: 0;
        }
        
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: rgba(255,255,255,0.2);
          transition: .4s;
          border-radius: 34px;
        }
        
        .slider:before {
          position: absolute;
          content: "";
          height: 16px;
          width: 16px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          transition: .4s;
          border-radius: 50%;
        }
        
        input:checked + .slider {
          background-color: var(--primary);
        }
        
        input:checked + .slider:before {
          transform: translateX(26px);
        }
        
        .btn-save-container {
            margin-top: 30px;
            text-align: right;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-save:hover { 
            filter: brightness(1.1); 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4); 
        }
        
    </style>
        <div class="top-bar" style="margin-bottom: 20px;">
            <h2><ion-icon name="extension-puzzle-outline" style="vertical-align: middle;"></ion-icon> Integrations</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><ion-icon name="checkmark-circle"></ion-icon> Settings updated successfully</div>
        <?php endif; ?>

        <form action="/<?= ADMIN_PATH ?>/settings/integrations/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
            
            <div class="settings-container">
                <!-- Tabs Container -->
                <div class="tabs-header-container">
                    <div class="tabs-nav">
                        <button type="button" class="tab-btn active" onclick="openTab('social', this)">
                            <ion-icon name="log-in-outline"></ion-icon> Social Login
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('analytics', this)">
                            <ion-icon name="stats-chart-outline"></ion-icon> Analytics
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('ai', this)">
                            <ion-icon name="hardware-chip-outline"></ion-icon> Artificial Intelligence
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('whatsapp', this)">
                            <ion-icon name="logo-whatsapp"></ion-icon> WhatsApp
                        </button>
                    </div>
                </div>
                
                <!-- Tabs Content -->
                <div class="tab-content-area">
                    
                    <!-- Tab: Social Login -->
                    <div id="social" class="tab-pane active">
                        <div class="grid-2">
                            <!-- Google Login -->
                            <div class="form-section">
                                <h3><ion-icon name="logo-google" style="vertical-align: middle;"></ion-icon> Google Login</h3>
                                <div class="switch-container">
                                    <label class="switch">
                                      <input type="checkbox" name="settings[google_login_enabled]" value="1" <?= ($settings['google_login_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                      <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">Enable Google Login</span>
                                </div>
                                <div class="form-group">
                                    <label>Client ID</label>
                                    <input type="text" name="settings[google_client_id]" class="form-control" value="<?= htmlspecialchars($settings['google_client_id'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Client Secret</label>
                                    <input type="password" name="settings[google_client_secret]" class="form-control" value="<?= htmlspecialchars($settings['google_client_secret'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <!-- LinkedIn Login -->
                            <div class="form-section">
                                <h3><ion-icon name="logo-linkedin" style="vertical-align: middle;"></ion-icon> LinkedIn Login</h3>
                                <div class="switch-container">
                                    <label class="switch">
                                      <input type="checkbox" name="settings[linkedin_login_enabled]" value="1" <?= ($settings['linkedin_login_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                      <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">Enable LinkedIn Login</span>
                                </div>
                                <div class="form-group">
                                    <label>Client ID</label>
                                    <input type="text" name="settings[linkedin_client_id]" class="form-control" value="<?= htmlspecialchars($settings['linkedin_client_id'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Client Secret</label>
                                    <input type="password" name="settings[linkedin_client_secret]" class="form-control" value="<?= htmlspecialchars($settings['linkedin_client_secret'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Facebook Login -->
                            <div class="form-section">
                                <h3><ion-icon name="logo-facebook" style="vertical-align: middle;"></ion-icon> Facebook Login</h3>
                                <div class="switch-container">
                                    <label class="switch">
                                      <input type="checkbox" name="settings[facebook_login_enabled]" value="1" <?= ($settings['facebook_login_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                      <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">Enable Facebook Login</span>
                                </div>
                                <div class="form-group">
                                    <label>App ID</label>
                                    <input type="text" name="settings[facebook_client_id]" class="form-control" value="<?= htmlspecialchars($settings['facebook_client_id'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <!-- Apple Login -->
                            <div class="form-section">
                                <h3><ion-icon name="logo-apple" style="vertical-align: middle;"></ion-icon> Apple Login</h3>
                                <div class="switch-container">
                                    <label class="switch">
                                      <input type="checkbox" name="settings[apple_login_enabled]" value="1" <?= ($settings['apple_login_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                      <span class="slider"></span>
                                    </label>
                                    <span class="switch-label">Enable Apple Login</span>
                                </div>
                                <div class="form-group">
                                    <label>Services ID</label>
                                    <input type="text" name="settings[apple_client_id]" class="form-control" value="<?= htmlspecialchars($settings['apple_client_id'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Analytics -->
                    <div id="analytics" class="tab-pane">
                        <div class="form-section">
                            <h3><ion-icon name="analytics-outline" style="vertical-align: middle;"></ion-icon> Google Analytics</h3>
                            <p style="color: rgba(255,255,255,0.6); margin-bottom: 20px; font-size: 0.9rem;">
                                Enter your Google Analytics Tracking ID (e.g., G-XXXXXXXXXX) to start tracking page views and user behavior.
                            </p>
                            
                            <div class="form-group">
                                <label>Tracking ID</label>
                                <input type="text" name="settings[google_analytics_id]" class="form-control" placeholder="G-XXXXXXXXXX" value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Tab: AI -->
                    <div id="ai" class="tab-pane">
                        <?php $activeProvider = $settings['ai_provider'] ?? 'openai'; ?>
                        <div class="form-section">
                            <h3><ion-icon name="hardware-chip-outline" style="vertical-align: middle;"></ion-icon> AI Engine Selection</h3>
                            <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px; font-size: 0.95rem;">
                                Choose which artificial intelligence provider powers the AI Manager, Weekly Roast, Email Drafter, and smart assistants across the ERP.
                            </p>
                            <div class="form-group" style="max-width: 400px; margin-bottom: 30px;">
                                <label style="font-weight: 600; color: #fff;">Active AI Provider</label>
                                <select name="settings[ai_provider]" id="ai_provider_select" class="form-control" style="padding: 12px; font-weight: 600;" onchange="toggleAiSections()">
                                    <option value="openai" <?= $activeProvider === 'openai' ? 'selected' : '' ?>>OpenAI (GPT-4 / GPT-4o)</option>
                                    <option value="gemini" <?= $activeProvider === 'gemini' ? 'selected' : '' ?>>Google Gemini (1.5 Pro / Flash)</option>
                                    <option value="huggingface" <?= $activeProvider === 'huggingface' ? 'selected' : '' ?>>Hugging Face (Open Source Models)</option>
                                    <option value="groq" <?= $activeProvider === 'groq' ? 'selected' : '' ?>>Groq (Fast LLM Inference)</option>
                                    <option value="muse_glimmer" <?= $activeProvider === 'muse_glimmer' ? 'selected' : '' ?>>✨ Meta Muse Glimmer 30B (via OpenRouter)</option>
                                </select>
                            </div>

                            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 25px 0;">

                            <!-- OpenAI Configuration -->
                            <div id="section_openai" class="ai-provider-card" style="margin-bottom: 30px;">
                                <h4 style="color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <span>🤖 OpenAI Configuration</span>
                                    <span class="badge-active" id="badge_openai" style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 12px; display: <?= $activeProvider === 'openai' ? 'inline-block' : 'none' ?>;">Active Provider</span>
                                </h4>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>OpenAI API Key (sk-...)</label>
                                        <input type="password" name="settings[openai_api_key]" class="form-control" placeholder="sk-..." value="<?= htmlspecialchars($settings['openai_api_key'] ?? ($settings['ai_openai_key'] ?? '')) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Model Name</label>
                                        <input type="text" name="settings[ai_openai_model]" class="form-control" placeholder="gpt-4o" value="<?= htmlspecialchars($settings['ai_openai_model'] ?? 'gpt-4') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Google Gemini Configuration -->
                            <div id="section_gemini" class="ai-provider-card" style="margin-bottom: 30px;">
                                <h4 style="color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <span>✨ Google Gemini Configuration</span>
                                    <span class="badge-active" id="badge_gemini" style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 12px; display: <?= $activeProvider === 'gemini' ? 'inline-block' : 'none' ?>;">Active Provider</span>
                                </h4>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Gemini API Key (AIzaSy...)</label>
                                        <input type="password" name="settings[ai_gemini_key]" class="form-control" placeholder="AIzaSy..." value="<?= htmlspecialchars($settings['ai_gemini_key'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Model Name</label>
                                        <input type="text" name="settings[ai_gemini_model]" class="form-control" placeholder="gemini-1.5-pro" value="<?= htmlspecialchars($settings['ai_gemini_model'] ?? 'gemini-pro') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Hugging Face Configuration -->
                            <div id="section_huggingface" class="ai-provider-card">
                                <h4 style="color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <span>🤗 Hugging Face Configuration</span>
                                    <span class="badge-active" id="badge_huggingface" style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 12px; display: <?= $activeProvider === 'huggingface' ? 'inline-block' : 'none' ?>;">Active Provider</span>
                                </h4>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Hugging Face API Token (hf_...)</label>
                                        <input type="password" name="settings[ai_huggingface_key]" class="form-control" placeholder="hf_..." value="<?= htmlspecialchars($settings['ai_huggingface_key'] ?? '') ?>">
                                    </div>
                                     <div class="form-group">
                                         <label>Model Identifier</label>
                                         <?php 
                                         $currentHfModel = $settings['ai_huggingface_model'] ?? 'Qwen/Qwen2.5-72B-Instruct';
                                         if (empty($currentHfModel) || stripos($currentHfModel, 'flan-t5') !== false) {
                                             $currentHfModel = 'Qwen/Qwen2.5-72B-Instruct';
                                         }
                                         ?>
                                         <input type="text" name="settings[ai_huggingface_model]" class="form-control" placeholder="Qwen/Qwen2.5-72B-Instruct" value="<?= htmlspecialchars($currentHfModel) ?>">
                                         <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 4px; display: block;">Supported Chat Models: <code>Qwen/Qwen2.5-72B-Instruct</code>, <code>meta-llama/Meta-Llama-3-8B-Instruct</code>, <code>mistralai/Mistral-7B-Instruct-v0.3</code></small>
                                     </div>
                                </div>
                            </div>

                            <!-- Groq Configuration -->
                            <div id="section_groq" class="ai-provider-card" style="margin-bottom: 30px;">
                                <h4 style="color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <span>⚡ Groq Configuration</span>
                                    <span class="badge-active" id="badge_groq" style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 12px; display: <?= $activeProvider === 'groq' ? 'inline-block' : 'none' ?>;">Active Provider</span>
                                </h4>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>Groq API Key (gsk_...)</label>
                                        <input type="password" name="settings[ai_groq_key]" class="form-control" placeholder="gsk_..." value="<?= htmlspecialchars($settings['ai_groq_key'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Model Name</label>
                                        <input type="text" name="settings[ai_groq_model]" class="form-control" placeholder="llama3-8b-8192" value="<?= htmlspecialchars($settings['ai_groq_model'] ?? 'llama3-8b-8192') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Meta Muse Glimmer Configuration -->
                            <div id="section_muse_glimmer" class="ai-provider-card" style="margin-bottom: 30px;">
                                <h4 style="color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <span>✨ Meta Muse Glimmer 30B Configuration</span>
                                    <span class="badge-active" id="badge_muse_glimmer" style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 12px; display: <?= $activeProvider === 'muse_glimmer' ? 'inline-block' : 'none' ?>;">Active Provider</span>
                                </h4>
                                <p style="color: rgba(255,255,255,0.6); font-size: 0.88rem; margin-bottom: 16px;">
                                    Muse Glimmer 30B is Meta's open-weight model available via <strong>OpenRouter</strong>. Get your API key at
                                    <a href="https://openrouter.ai/keys" target="_blank" style="color: #6366f1;">openrouter.ai/keys</a>.
                                </p>
                                <div class="grid-2">
                                    <div class="form-group">
                                        <label>OpenRouter API Key (sk-or-...)</label>
                                        <input type="password" name="settings[ai_muse_glimmer_key]" class="form-control" placeholder="sk-or-..." value="<?= htmlspecialchars($settings['ai_muse_glimmer_key'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Model ID</label>
                                        <input type="text" name="settings[ai_muse_glimmer_model]" class="form-control" placeholder="meta/muse-glimmer-30b" value="<?= htmlspecialchars($settings['ai_muse_glimmer_model'] ?? 'meta/muse-glimmer-30b') ?>">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Tab: WhatsApp -->
                    <div id="whatsapp" class="tab-pane">
                        <div class="form-section">
                            <h3><ion-icon name="logo-whatsapp" style="vertical-align: middle;"></ion-icon> WhatsApp Infrastructure</h3>
                            <div class="form-group">
                                <label>Active WhatsApp Provider</label>
                                <select name="settings[whatsapp_provider]" class="form-control" style="background-color: #1a1a2e;">
                                    <option value="meta" <?= ($settings['whatsapp_provider'] ?? 'meta') == 'meta' ? 'selected' : '' ?>>Official Meta Cloud API</option>
                                    <option value="unofficial" <?= ($settings['whatsapp_provider'] ?? '') == 'unofficial' ? 'selected' : '' ?>>Unofficial API (Baileys/WAPP)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-2 mt-4">
                            <div class="form-section">
                                <h3>Official Meta API</h3>
                                <div class="form-group">
                                    <label>System User Token</label>
                                    <input type="password" name="settings[whatsapp_meta_token]" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_meta_token'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number ID</label>
                                    <input type="text" name="settings[whatsapp_meta_phone_id]" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_meta_phone_id'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3>Unofficial API Provider</h3>
                                <div class="form-group">
                                    <label>Endpoint URL</label>
                                    <input type="url" name="settings[whatsapp_unofficial_url]" class="form-control" placeholder="https://your-whatsapp-api.com" value="<?= htmlspecialchars($settings['whatsapp_unofficial_url'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Instance Token / API Key</label>
                                    <input type="password" name="settings[whatsapp_unofficial_token]" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_unofficial_token'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="btn-save-container">
                        <button type="submit" class="btn-save"><ion-icon name="checkmark-circle-outline"></ion-icon> Save Integrations</button>
                    </div>
                </div>
                 </form>
<script>
    function openTab(tabId, btnElement) {
        // Hide all tabs
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => pane.classList.remove('active'));
        
        // Remove active class from buttons
        const btns = document.querySelectorAll('.tab-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        
        // Show selected tab
        const target = document.getElementById(tabId);
        if (target) target.classList.add('active');
        
        // Set active class on clicked button
        if (btnElement) btnElement.classList.add('active');

        const tabInput = document.getElementById('active_tab_input');
        if (tabInput) tabInput.value = tabId;
    }

    function toggleAiSections() {
        const select = document.getElementById('ai_provider_select');
        if (!select) return;
        const val = select.value;
        ['openai', 'gemini', 'huggingface', 'groq', 'muse_glimmer'].forEach(p => {
            const badge = document.getElementById('badge_' + p);
            const card = document.getElementById('section_' + p);
            if (badge) badge.style.display = (p === val) ? 'inline-block' : 'none';
            if (card) {
                card.style.borderLeft = (p === val) ? '4px solid #10b981' : '4px solid transparent';
                card.style.paddingLeft = (p === val) ? '15px' : '0';
            }
        });
    }
    document.addEventListener('DOMContentLoaded', toggleAiSections);
</script>
<?php require __DIR__ . '/../footer.php'; ?>

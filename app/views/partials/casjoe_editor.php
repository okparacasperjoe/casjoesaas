
<style>
        .casjoe-editor-toolbar {
            background: #fbfbfb;
            border-bottom: 1px solid #d1d1d1;
            padding: 8px 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ce-row {
            display: flex;
            gap: 2px;
            flex-wrap: wrap;
            align-items: center;
        }
        .ce-btn {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 3px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #333;
            font-size: 1.1rem;
            font-family: serif;
            transition: all 0.1s;
        }
        .ce-btn:hover {
            background: #e4e4e4;
            border-color: #ccc;
        }
        .ce-divider {
            width: 1px;
            height: 20px;
            background: #ccc;
            margin: 0 5px;
        }
        .ce-select {
            border: 1px solid #ccc;
            background: #fff;
            height: 24px;
            border-radius: 3px;
            font-size: 0.85rem;
            padding: 0 5px;
            color: #333;
            outline: none;
            cursor: pointer;
        }
        .ce-color-picker {
            position: relative;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 3px;
            cursor: pointer;
            color: #333;
        }
        .ce-color-picker:hover {
            background: #e4e4e4;
            border-color: #ccc;
        }
        .ce-color-picker input[type="color"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .casjoe-editor-workspace {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background: #ffffff;
            outline: none;
            font-family: inherit;
        }
        /* Image Modal Styles */
        .casjoe-modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,60,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .casjoe-modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-family: 'Inter', sans-serif;
        }
        .casjoe-modal-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 25px 0;
        }
        .modal-btn {
            background: #f8f9fc;
            border: 2px solid #e2e8f0;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            color: #000066;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .modal-btn:hover {
            border-color: var(--erp-gold);
            background: #fffdf5;
        }
        .modal-close {
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-weight: 600;
        }
    
</style>

<div class="casjoe-visual-editor">
<div class="casjoe-editor-toolbar" id="editorToolbar">
                            <!-- Row 1 -->
                            <div class="ce-row">
                                <button class="ce-btn" onclick="execCmd('removeFormat')" title="Remove Format" style="color: #d9534f; font-style: italic;">Tx</button>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="execCmd('bold')" title="Bold"><b>B</b></button>
                                <button class="ce-btn" onclick="execCmd('italic')" title="Italic"><i>I</i></button>
                                <button class="ce-btn" onclick="execCmd('underline')" title="Underline"><u>U</u></button>
                                <button class="ce-btn" onclick="execCmd('strikethrough')" title="Strikethrough"><s>S</s></button>
                                <button class="ce-btn" onclick="execCmd('subscript')" title="Subscript" style="font-size: 0.9rem;">X<sub>2</sub></button>
                                <button class="ce-btn" onclick="execCmd('superscript')" title="Superscript" style="font-size: 0.9rem;">X<sup>2</sup></button>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="insertLink()" title="Link"><ion-icon name="link-outline"></ion-icon></button>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="execCmd('insertOrderedList')" title="Numbered List"><ion-icon name="list-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="execCmd('insertUnorderedList')" title="Bullet List"><ion-icon name="list"></ion-icon></button>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="execCmd('outdent')" title="Decrease Indent"><ion-icon name="arrow-back-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="execCmd('indent')" title="Increase Indent"><ion-icon name="arrow-forward-outline"></ion-icon></button>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="execCmd('justifyLeft')" title="Align Left"><ion-icon name="menu-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="execCmd('justifyCenter')" title="Align Center"><ion-icon name="reorder-two-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="execCmd('justifyRight')" title="Align Right" style="transform: scaleX(-1);"><ion-icon name="menu-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="execCmd('justifyFull')" title="Justify"><ion-icon name="reorder-four-outline"></ion-icon></button>
                            </div>
                            <!-- Row 2 -->
                            <div class="ce-row">
                                <button class="ce-btn" onclick="openImageModal()" title="Insert Image"><ion-icon name="image-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="openVideoModal()" title="Insert Video"><ion-icon name="videocam-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="insertTable()" title="Insert Table"><ion-icon name="grid-outline"></ion-icon></button>
                                <button class="ce-btn" onclick="openIntegrationPicker()" title="Casjoe Integrations" style="width: auto; padding: 0 8px; font-size: 0.8rem; font-weight: bold; background: #000066; color: #fff; border-radius: 4px; margin-left: 5px;"><ion-icon name="apps-outline" style="margin-right: 4px; vertical-align: middle;"></ion-icon>Apps</button>
                                <div class="ce-divider"></div>
                                <select class="ce-select" onchange="if(this.value) execCmd('insertText', this.value); this.selectedIndex=0;" title="Personalization Tags" style="width: 100px; font-weight: bold;">
                                    <option value="">Merge Tags</option>
                                    <option value="{{first_name}}">First Name</option>
                                    <option value="{{last_name}}">Last Name</option>
                                    <option value="{{email}}">Email Address</option>
                                    <option value="{{company}}">Company</option>
                                    <option value="{{unsubscribe_link}}">Unsubscribe Link</option>
                                </select>
                                <div class="ce-divider"></div>
                                <select class="ce-select" onchange="execCmd('fontName', this.value); this.selectedIndex=0;" title="Font Family">
                                    <option value="">Font</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Courier New">Courier</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Inter">Inter</option>
                                    <option value="Lato">Lato</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Times New Roman">Times</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                                <select class="ce-select" onchange="execCmd('fontSize', this.value); this.selectedIndex=0;" title="Font Size">
                                    <option value="">Size</option>
                                    <option value="1">Small</option>
                                    <option value="3">Normal</option>
                                    <option value="5">Large</option>
                                    <option value="7">Huge</option>
                                </select>
                                <div class="ce-divider"></div>
                                <div class="ce-color-picker" title="Text Color">
                                    <span style="font-weight: bold; font-family: sans-serif; font-size: 1rem; border-bottom: 3px solid #000;">A</span>
                                    <input type="color" onchange="execCmd('foreColor', this.value); this.previousElementSibling.style.borderBottomColor = this.value;">
                                </div>
                                <div class="ce-color-picker" title="Background Color">
                                    <ion-icon name="color-fill-outline"></ion-icon>
                                    <input type="color" onchange="execCmd('hiliteColor', this.value)">
                                </div>
                                <div class="ce-divider"></div>
                                <button class="ce-btn" onclick="toggleSource()" id="sourceToggleBtn2" style="width: auto; padding: 0 10px; font-family: 'Inter', sans-serif; font-size: 0.85rem;">
                                    <ion-icon name="code-slash-outline" style="margin-right: 5px;"></ion-icon> Source
                                </button>
                            </div>
                        </div>

                        <!-- Editable Workspace -->
                        <div id="casjoe-workspace" class="casjoe-editor-workspace" contenteditable="true" spellcheck="false" placeholder="Start typing or load a template..."></div>
                        
                        <!-- Raw Source Workspace (Hidden by default) -->
                        <textarea id="casjoe-source" style="display: none; flex: 1; padding: 25px; border: none; outline: none; resize: none; font-family: monospace; font-size: 0.9rem; background: #f8f9fc;"></textarea>
                    </div>
</div>

<!-- Image Modal -->
    <div id="imageModal" class="casjoe-modal" style="display:none;">
        <div class="casjoe-modal-content" style="width: 450px;">
            <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem;" id="imgModalTitle">Choose Image</h3>
            
            <div id="imgSrcControls">
                <div class="casjoe-modal-options">
                    <button class="modal-btn" onclick="document.getElementById('deviceUpload').click()">
                        <ion-icon name="laptop-outline"></ion-icon> Upload from Device
                    </button>
                    <input type="file" id="deviceUpload" accept="image/*" style="display:none;" onchange="handleDeviceUpload(this)">
                    
                    <button class="modal-btn" onclick="handleLinkUpload()">
                        <ion-icon name="link-outline"></ion-icon> Image URL Link
                    </button>
                </div>
            </div>

            <!-- Image Resize Controls (Only visible when editing an existing image) -->
            <div id="imgPropControls" style="display:none; margin-top: 20px; text-align: left; background: #f8f9fc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <p style="margin: 0 0 10px 0; font-size: 0.95rem; font-weight: bold; color: #000066;">Resize Image</p>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Scale Slider</label>
                    <input type="range" min="10" max="200" value="100" id="imgScaleSlider" oninput="scaleImageBySlider(this.value)" style="width: 100%; cursor: pointer;">
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Width</label>
                        <input type="text" id="imgWidth" class="form-control" placeholder="e.g. 100% or 300px" style="padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Height</label>
                        <input type="text" id="imgHeight" class="form-control" placeholder="e.g. auto" style="padding: 8px;">
                    </div>
                </div>
                <button onclick="updateImageProperties()" style="width: 100%; background: var(--erp-gold); color: #000066; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: opacity 0.2s;">Apply Size</button>
            </div>

            <button class="modal-close" onclick="closeImageModal()" style="margin-top: 15px;">Cancel</button>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="casjoe-modal" style="display:none;">
        <div class="casjoe-modal-content" style="width: 450px;">
            <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem;">Insert Video</h3>
            
            <div class="casjoe-modal-options">
                <button class="modal-btn" onclick="document.getElementById('videoDeviceUpload').click()">
                    <ion-icon name="laptop-outline"></ion-icon> Upload Video
                </button>
                <input type="file" id="videoDeviceUpload" accept="video/*" style="display:none;" onchange="handleVideoDeviceUpload(this)">
                
                <button class="modal-btn" onclick="handleVideoIframe()">
                    <ion-icon name="logo-youtube"></ion-icon> YouTube/Iframe Link
                </button>
            </div>
            
            <button class="modal-close" onclick="closeVideoModal()" style="margin-top: 15px;">Cancel</button>
        </div>
    </div>

    <!-- Casjoe Cloud Picker Modal -->
    <div id="casjoeCloudModal" class="casjoe-modal" style="display:none; z-index: 2000;">
        <div class="casjoe-modal-content" style="width: 650px; max-height: 80vh; overflow-y: auto; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="cloud-outline"></ion-icon> Casjoe Cloud
                </h3>
                <button class="modal-close" onclick="closeCasjoeCloudPicker()" style="margin: 0;">Close</button>
            </div>
            
            <div id="cloudGrid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>

    <!-- Integration Picker Modal -->
    <div id="integrationModal" class="casjoe-modal" style="display:none; z-index: 2000;">
        <div class="casjoe-modal-content" style="width: 550px; max-height: 80vh; overflow-y: auto; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="apps-outline"></ion-icon> Casjoe Integrations
                </h3>
                <button class="modal-close" onclick="closeIntegrationPicker()" style="margin: 0;">Close</button>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('smart_forms')"><ion-icon name="document-text-outline"></ion-icon> Smart Forms</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('cloud')"><ion-icon name="cloud-outline"></ion-icon> Casjoe Cloud</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('academy')"><ion-icon name="school-outline"></ion-icon> Courses</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('shop')"><ion-icon name="cart-outline"></ion-icon> Store Products</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('links')"><ion-icon name="link-outline"></ion-icon> Links</button>
            </div>

            <div id="integrationGrid" style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                <!-- Populated by JS -->
                <div style="text-align: center; color: #64748b; padding: 20px;">Select an app above to view your links.</div>
            </div>
        </div>
    </div>

    


<!-- Image Modal -->
    <div id="imageModal" class="casjoe-modal" style="display:none;">
        <div class="casjoe-modal-content" style="width: 450px;">
            <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem;" id="imgModalTitle">Choose Image</h3>
            
            <div id="imgSrcControls">
                <div class="casjoe-modal-options">
                    <button class="modal-btn" onclick="document.getElementById('deviceUpload').click()">
                        <ion-icon name="laptop-outline"></ion-icon> Upload from Device
                    </button>
                    <input type="file" id="deviceUpload" accept="image/*" style="display:none;" onchange="handleDeviceUpload(this)">
                    
                    <button class="modal-btn" onclick="handleLinkUpload()">
                        <ion-icon name="link-outline"></ion-icon> Image URL Link
                    </button>
                </div>
            </div>

            <!-- Image Resize Controls (Only visible when editing an existing image) -->
            <div id="imgPropControls" style="display:none; margin-top: 20px; text-align: left; background: #f8f9fc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <p style="margin: 0 0 10px 0; font-size: 0.95rem; font-weight: bold; color: #000066;">Resize Image</p>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Scale Slider</label>
                    <input type="range" min="10" max="200" value="100" id="imgScaleSlider" oninput="scaleImageBySlider(this.value)" style="width: 100%; cursor: pointer;">
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Width</label>
                        <input type="text" id="imgWidth" class="form-control" placeholder="e.g. 100% or 300px" style="padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 5px;">Height</label>
                        <input type="text" id="imgHeight" class="form-control" placeholder="e.g. auto" style="padding: 8px;">
                    </div>
                </div>
                <button onclick="updateImageProperties()" style="width: 100%; background: var(--erp-gold); color: #000066; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: opacity 0.2s;">Apply Size</button>
            </div>

            <button class="modal-close" onclick="closeImageModal()" style="margin-top: 15px;">Cancel</button>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="casjoe-modal" style="display:none;">
        <div class="casjoe-modal-content" style="width: 450px;">
            <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem;">Insert Video</h3>
            
            <div class="casjoe-modal-options">
                <button class="modal-btn" onclick="document.getElementById('videoDeviceUpload').click()">
                    <ion-icon name="laptop-outline"></ion-icon> Upload Video
                </button>
                <input type="file" id="videoDeviceUpload" accept="video/*" style="display:none;" onchange="handleVideoDeviceUpload(this)">
                
                <button class="modal-btn" onclick="handleVideoIframe()">
                    <ion-icon name="logo-youtube"></ion-icon> YouTube/Iframe Link
                </button>
            </div>
            
            <button class="modal-close" onclick="closeVideoModal()" style="margin-top: 15px;">Cancel</button>
        </div>
    </div>

    <!-- Casjoe Cloud Picker Modal -->
    <div id="casjoeCloudModal" class="casjoe-modal" style="display:none; z-index: 2000;">
        <div class="casjoe-modal-content" style="width: 650px; max-height: 80vh; overflow-y: auto; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="cloud-outline"></ion-icon> Casjoe Cloud
                </h3>
                <button class="modal-close" onclick="closeCasjoeCloudPicker()" style="margin: 0;">Close</button>
            </div>
            
            <div id="cloudGrid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>

    <!-- Integration Picker Modal -->
    <div id="integrationModal" class="casjoe-modal" style="display:none; z-index: 2000;">
        <div class="casjoe-modal-content" style="width: 550px; max-height: 80vh; overflow-y: auto; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                <h3 style="margin:0; color:#000066; font-weight:800; font-size:1.2rem; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="apps-outline"></ion-icon> Casjoe Integrations
                </h3>
                <button class="modal-close" onclick="closeIntegrationPicker()" style="margin: 0;">Close</button>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('smart_forms')"><ion-icon name="document-text-outline"></ion-icon> Smart Forms</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('cloud')"><ion-icon name="cloud-outline"></ion-icon> Casjoe Cloud</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('academy')"><ion-icon name="school-outline"></ion-icon> Courses</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('shop')"><ion-icon name="cart-outline"></ion-icon> Store Products</button>
                <button class="modal-btn" style="flex:1; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="loadIntegrations('links')"><ion-icon name="link-outline"></ion-icon> Links</button>
            </div>

            <div id="integrationGrid" style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                <!-- Populated by JS -->
                <div style="text-align: center; color: #64748b; padding: 20px;">Select an app above to view your links.</div>
            </div>
        </div>
    </div>

    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sourceTextarea = document.getElementById('casjoe-source');
        if (sourceTextarea) {
            sourceTextarea.setAttribute('name', '<?= $editorName ?? "content" ?>');
            sourceTextarea.value = `<?= $editorValue ?? "" ?>`;
        }
        const canvas = document.getElementById('casjoe-workspace');
        if (canvas) {
            canvas.innerHTML = `<?= $editorValue ?? "" ?>`;
            
            // Add Selection Saving logic
            canvas.addEventListener('blur', saveCasjoeSelection);
            canvas.addEventListener('keyup', saveCasjoeSelection);
            canvas.addEventListener('mouseup', saveCasjoeSelection);
        }
    });

    let casjoeSavedRange = null;

    function saveCasjoeSelection() {
        if (window.getSelection) {
            const sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                casjoeSavedRange = sel.getRangeAt(0);
            }
        }
    }

    function restoreCasjoeSelection() {
        if (casjoeSavedRange) {
            if (window.getSelection) {
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(casjoeSavedRange);
            }
        }
    }

    // Override execCmd
    function execCmd(command, value = null) {
        if (isSourceMode) return;
        restoreCasjoeSelection();
        document.execCommand(command, false, value);
        document.getElementById('casjoe-workspace').focus();
    }







        // --- Casjoe Custom Editor Logic ---
        if (typeof isSourceMode === "undefined") { var isSourceMode = false; }
        const workspace = document.getElementById('casjoe-workspace');
        const sourceArea = document.getElementById('casjoe-source');
        const toolbar = document.getElementById('editorToolbar');
        const sourceToggleBtn = document.getElementById('sourceToggleBtn2');

        function old_execCmd(command, value = null) {
            document.execCommand(command, false, value);
            workspace.focus();
        }

        function insertLink() {
            const url = prompt("Enter the link URL:");
            if (url) {
                execCmd('createLink', url);
            }
        }

        function toggleSource() {
            if (isSourceMode) {
                // Switching to Visual Mode
                workspace.innerHTML = sourceArea.value;
                sourceArea.style.display = 'none';
                workspace.style.display = 'block';
                toolbar.style.opacity = '1';
                toolbar.style.pointerEvents = 'auto';
                sourceToggleBtn.innerText = 'View Source';
                sourceToggleBtn.style.background = 'rgba(255,255,255,0.1)';
                sourceToggleBtn.style.color = '#ffffff';
                sourceToggleBtn.style.borderColor = 'rgba(255,255,255,0.3)';
            } else {
                // Switching to Source Mode
                sourceArea.value = workspace.innerHTML;
                workspace.style.display = 'none';
                sourceArea.style.display = 'block';
                toolbar.style.opacity = '0.5';
                toolbar.style.pointerEvents = 'none';
                sourceToggleBtn.innerText = 'View Visual';
                sourceToggleBtn.style.background = '#ffa600';
                sourceToggleBtn.style.color = '#000066';
                sourceToggleBtn.style.borderColor = '#ffa600';
                // keep the source button active so they can switch back
                document.getElementById('sourceToggleBtn2').style.pointerEvents = 'auto'; 
            }
            isSourceMode = !isSourceMode;
        }

        // Make images clickable inside the workspace so they can be edited
        workspace.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                openImageModal(e.target);
            }
        });

        // --- Image Modal Logic ---
        let currentEditingImage = null;

        function openImageModal(imgElement = null) {
            currentEditingImage = imgElement; 
            document.getElementById('imageModal').style.display = 'flex';
            
            if (imgElement) {
                document.getElementById('imgModalTitle').innerText = "Edit Image";
                document.getElementById('imgPropControls').style.display = 'block';
                
                // Populate current sizes
                document.getElementById('imgWidth').value = imgElement.style.width || imgElement.getAttribute('width') || '';
                document.getElementById('imgHeight').value = imgElement.style.height || imgElement.getAttribute('height') || '';
                
                // Store natural width for slider scaling if not already stored
                if (!imgElement.dataset.origW) {
                    imgElement.dataset.origW = imgElement.naturalWidth || imgElement.clientWidth || 500;
                }
                document.getElementById('imgScaleSlider').value = 100; // Reset slider
            } else {
                document.getElementById('imgModalTitle').innerText = "Choose Image";
                document.getElementById('imgPropControls').style.display = 'none';
            }
        }

        function scaleImageBySlider(percentage) {
            if (currentEditingImage) {
                const origW = parseFloat(currentEditingImage.dataset.origW);
                const newW = (origW * (percentage / 100)).toFixed(0);
                
                // Apply visually immediately
                currentEditingImage.style.width = newW + 'px';
                currentEditingImage.setAttribute('width', newW);
                currentEditingImage.style.height = 'auto';
                currentEditingImage.setAttribute('height', 'auto');
                
                // Update inputs to match
                document.getElementById('imgWidth').value = newW + 'px';
                document.getElementById('imgHeight').value = 'auto';
            }
        }

        function updateImageProperties() {
            if (currentEditingImage) {
                const w = document.getElementById('imgWidth').value;
                const h = document.getElementById('imgHeight').value;
                
                if (w) {
                    currentEditingImage.style.width = w;
                    // Email clients prefer raw attributes alongside styles
                    currentEditingImage.setAttribute('width', w.replace('px', '').replace('%', ''));
                } else {
                    currentEditingImage.style.width = '';
                    currentEditingImage.removeAttribute('width');
                }
                
                if (h) {
                    currentEditingImage.style.height = h;
                    currentEditingImage.setAttribute('height', h.replace('px', '').replace('%', ''));
                } else {
                    currentEditingImage.style.height = '';
                    currentEditingImage.removeAttribute('height');
                }
                
                closeImageModal();
            }
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            currentEditingImage = null;
        }

        function processImageSelection(url) {
            if(!url) return;
            if (currentEditingImage) {
                // Edit existing image
                currentEditingImage.src = url;
            } else {
                // Insert new image
                restoreCasjoeSelection(); execCmd('insertImage', url);
            }
            closeImageModal();
        }

        function handleDeviceUpload(input) {
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    processImageSelection(e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleLinkUpload() {
            const url = prompt("Enter Image URL:");
            if(url) processImageSelection(url);
        }

        // --- Video Modal & Logic ---
        function openVideoModal() {
            document.getElementById('videoModal').style.display = 'flex';
        }

        function closeVideoModal() {
            document.getElementById('videoModal').style.display = 'none';
        }

        function handleVideoLinkUpload() {
            const embedCode = prompt("Enter a YouTube Link, MP4 Link, or raw Iframe embed code:");
            if(embedCode) processVideoInsertion(embedCode);
            closeVideoModal();
        }

        function handleDeviceVideoUpload(input) {
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    processVideoInsertion(e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
            closeVideoModal();
        }

        function processVideoInsertion(embedCode) {
            let html = embedCode;
            
            // Auto-convert standard YouTube links to iframes
            if (embedCode.includes('youtube.com/watch?v=')) {
                const videoId = embedCode.split('v=')[1].split('&')[0];
                html = `<div style="text-align: center;"><iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="max-width: 100%;"></iframe></div><br/>`;
            } else if (embedCode.includes('youtu.be/')) {
                const videoId = embedCode.split('youtu.be/')[1].split('?')[0];
                html = `<div style="text-align: center;"><iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="max-width: 100%;"></iframe></div><br/>`;
            } else if (!embedCode.includes('<iframe')) {
                // If it's a raw video file link or base64
                html = `<div style="text-align: center;"><video width="100%" style="max-width: 560px;" controls src="${embedCode}"></video></div><br/>`;
            }
            
            restoreCasjoeSelection(); execCmd('insertHTML', html);
        }

        // --- Casjoe Cloud Picker ---
        let currentCloudType = 'image';

        function openCasjoeCloudPicker(type) {
            currentCloudType = type;
            document.getElementById('imageModal').style.display = 'none';
            document.getElementById('videoModal').style.display = 'none';
            document.getElementById('casjoeCloudModal').style.display = 'flex';
            
            const grid = document.getElementById('cloudGrid');
            grid.innerHTML = '<div style="text-align: center; grid-column: span 3; padding: 20px; font-weight: bold; color: #000066;">Connecting to Casjoe Cloud...</div>';
            
            fetch('/cloud/api/list?type=' + type)
                .then(res => res.json())
                .then(data => {
                    grid.innerHTML = '';
                    if (data.files && data.files.length > 0) {
                        data.files.forEach(file => {
                            if (type === 'image') {
                                grid.innerHTML += `<div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onclick="selectFromCloud('${file.url}')">
                                    <img src="${file.url}" style="width: 100%; height: 120px; object-fit: cover; display: block;">
                                    <div style="padding: 8px; font-size: 0.8rem; background: #f8f9fc; text-align: center; color: #000066; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</div>
                                </div>`;
                            } else {
                                grid.innerHTML += `<div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 140px; background: #eef2ff; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onclick="selectFromCloud('${file.url}')">
                                    <ion-icon name="videocam-outline" style="font-size: 3rem; color: #000066; margin-top: 20px;"></ion-icon>
                                    <div style="padding: 8px; font-size: 0.8rem; width: 100%; background: #f8f9fc; text-align: center; color: #000066; font-weight: bold; margin-top: auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</div>
                                </div>`;
                            }
                        });
                    } else {
                        grid.innerHTML = `<div style="text-align: center; grid-column: span 3; padding: 20px; color: #64748b;">No ${type}s found in your Casjoe Cloud.<br><br><a href="/cloud" target="_blank" style="color: #000066; font-weight: bold;">Go to Casjoe Cloud to upload files</a></div>`;
                    }
                })
                .catch(err => {
                    grid.innerHTML = '<div style="text-align: center; grid-column: span 3; padding: 20px; color: red;">Error loading from Casjoe Cloud.</div>';
                });
        }

        function closeCasjoeCloudPicker() {
            document.getElementById('casjoeCloudModal').style.display = 'none';
        }

        function selectFromCloud(url) {
            if(currentCloudType === 'image') {
                processImageSelection(url);
            } else {
                processVideoInsertion(url);
            }
            closeCasjoeCloudPicker();
        }

        // --- Casjoe Integration Picker ---
        function openIntegrationPicker() {
            document.getElementById('integrationModal').style.display = 'flex';
            document.getElementById('integrationGrid').innerHTML = '<div style="text-align: center; color: #64748b; padding: 20px;">Select an app above to view your links.</div>';
        }

        function closeIntegrationPicker() {
            document.getElementById('integrationModal').style.display = 'none';
        }

        function loadIntegrations(type) {
            const grid = document.getElementById('integrationGrid');
            grid.innerHTML = '<div style="text-align: center; padding: 20px; font-weight: bold; color: #000066;">Loading Integrations...</div>';
            
            fetch('/api/integrations?type=' + type)
                .then(res => res.json())
                .then(data => {
                    grid.innerHTML = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            let iconName = 'link';
                            if (type === 'smart_forms') iconName = 'document-text';
                            if (type === 'cloud') iconName = 'cloud';
                            if (type === 'academy') iconName = 'school';
                            if (type === 'shop') iconName = 'cart';

                            grid.innerHTML += `<div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #f8f9fc; transition: all 0.2s;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='#f8f9fc'" onclick="insertIntegrationLink('${item.name}', '${item.url}', '${type}', '${item.meta_type || ''}')">
                                <div style="font-weight: bold; color: #000066; display: flex; align-items: center; gap: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <ion-icon name="${iconName}" style="font-size: 1.5rem;"></ion-icon>
                                    ${item.name}
                                </div>
                                <div style="font-size: 0.8rem; color: #fff; font-weight: bold; background: #000066; padding: 6px 12px; border-radius: 4px;">Insert</div>
                            </div>`;
                        });
                    } else {
                        grid.innerHTML = `<div style="text-align: center; padding: 20px; color: #64748b;">No items found in this module.<br><br>Head to the module dashboard to create some!</div>`;
                    }
                })
                .catch(err => {
                    grid.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Error loading integrations.</div>';
                });
        }

        function insertIntegrationLink(name, url, type, metaType) {
            // Check if it's a cloud image/video insert instead of a button
            if (type === 'cloud') {
                if (metaType && metaType.startsWith('image/')) {
                    const html = `<div style="text-align: center;"><img src="${url}" style="max-width: 100%; border-radius: 8px;" alt="${name}" onclick="makeImageResizable(this)"/></div><br/>`;
                    restoreCasjoeSelection(); execCmd('insertHTML', html);
                    closeIntegrationPicker();
                    return;
                } else if (metaType && metaType.startsWith('video/')) {
                    const html = `<div style="text-align: center;"><video controls style="max-width: 100%; border-radius: 8px;"><source src="${url}" type="${metaType}"></video></div><br/>`;
                    restoreCasjoeSelection(); execCmd('insertHTML', html);
                    closeIntegrationPicker();
                    return;
                }
            }

            let defaultText = "View " + name;
            if (type === 'smart_forms') defaultText = "Fill Form: " + name;
            if (type === 'academy') defaultText = "Start Course: " + name;
            if (type === 'shop') defaultText = "Buy: " + name;
            if (type === 'cloud') defaultText = "Download File: " + name;
            
            const text = prompt("Enter button text:", defaultText);
            if(!text) return;
            
            const html = `<div style="text-align: center; margin: 20px 0;"><a href="${url}" style="background-color: var(--erp-gold, #ffa600); color: #000066; display: inline-block; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-family: sans-serif; font-weight: bold; font-size: 16px;">${text}</a></div><br/>`;
            
            restoreCasjoeSelection(); execCmd('insertHTML', html);
            closeIntegrationPicker();
        }

        // --- Advanced Insertions ---
        function insertCTAButton() {
            const text = prompt("Enter button text (e.g. Click Here):", "Click Here");
            if(!text) return;
            const link = prompt("Enter button link URL:", "https://");
            if(!link) return;
            
            const html = `<div style="text-align: center; margin: 20px 0;"><a href="${link}" style="background-color: var(--erp-gold, #ffa600); color: #000066; display: inline-block; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-family: sans-serif; font-weight: bold; font-size: 16px;">${text}</a></div><br/>`;
            
            restoreCasjoeSelection(); execCmd('insertHTML', html);
        }

        function insertTable() {
            const rows = prompt("Number of rows:", "2");
            if(!rows) return;
            const cols = prompt("Number of columns:", "2");
            if(!cols) return;
            
            let html = '<table style="width:100%; border-collapse: collapse; border: 1px solid #ccc;">';
            for(let i=0; i<rows; i++) {
                html += '<tr>';
                for(let j=0; j<cols; j++) {
                    html += '<td style="padding: 8px; border: 1px solid #ccc;">Data</td>';
                }
                html += '</tr>';
            }
            html += '</table><br/>';
            restoreCasjoeSelection(); execCmd('insertHTML', html);
        }


</script>

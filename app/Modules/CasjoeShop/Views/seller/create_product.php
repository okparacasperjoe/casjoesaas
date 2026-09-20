<?php 
$title = "Add Product";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Add New Product</h5>
                    <a href="/shop/vendor/products" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
                <div class="card-body p-4">
                    <form action="/shop/vendor/products/store" method="POST" enctype="multipart/form-data">
                        
                        <!-- Type Selection -->
                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-dark fw-bold">Product Type</label>
                            <div class="btn-group" role="group" aria-label="Product Type">
                                <input type="radio" class="btn-check" name="type" id="typePhysical" value="physical" checked onchange="toggleType()">
                                <label class="btn btn-outline-primary" for="typePhysical"><i class="bi bi-box-seam"></i> Physical</label>

                                <input type="radio" class="btn-check" name="type" id="typeDigital" value="digital" onchange="toggleType()">
                                <label class="btn btn-outline-info" for="typeDigital"><i class="bi bi-cloud-arrow-down"></i> Digital</label>

                                <input type="radio" class="btn-check" name="type" id="typeService" value="service" onchange="toggleType()">
                                <label class="btn btn-outline-success" for="typeService"><i class="bi bi-person-workspace"></i> Service</label>
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label text-dark fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Vintage T-Shirt or E-Book" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold">Category</label>
                                <input type="text" name="category" class="form-control" list="categories" placeholder="e.g. Clothing">
                                <datalist id="categories">
                                    <option value="Clothing">
                                    <option value="Electronics">
                                    <option value="Digital">
                                    <option value="Services">
                                    <option value="Home & Garden">
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold">Price (₦)</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                               <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Product Image (Main)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Gallery Images (Optional)</label>
                                <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                                <div class="form-text">You can select multiple images.</div>
                            </div>                </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Short Description <small class="text-muted">(for product listings and previews)</small></label>
                            <textarea name="description" class="form-control" rows="2" maxlength="200" placeholder="Brief summary that appears in product listings..."></textarea>
                            <div class="form-text">Maximum 200 characters</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Long Description <small class="text-muted">(supports images, videos, and formatted text)</small></label>
                            
                            <!-- Quill Editor Container -->
                            <div id="quill-editor" style="height: 300px; background: white; color: black !important;"></div>
                            <input type="hidden" name="long_description" id="hiddenDescription">
                            
                            <div class="form-text mt-2">Full product details with rich formatting.</div>
                        </div>

                        <!-- Physical Options -->
                        <div id="physicalOptions" class="p-3 bg-light rounded mb-3 border">
                            <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-truck"></i> Shipping & Stock</h6>
                            <div class="row g-3">
                                <style>
                                    /* Force Quill Content Black */
                                    .ql-editor { color: black !important; }
                                    .ql-editor p { color: black !important; }
                                    .ql-snow .ql-stroke { stroke: #333 !important; }
                                    .ql-snow .ql-fill { fill: #333 !important; }
                                    .ql-snow .ql-picker { color: #333 !important; }
                                </style>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" class="form-control" placeholder="Automatic if empty">
                                </div>
                            </div>
                        </div>

                        <!-- Digital Options -->
                        <div id="digitalOptions" class="p-3 bg-light rounded mb-3 border" style="display:none;">
                            <h6 class="fw-bold mb-2 text-info"><i class="bi bi-file-earmark-lock"></i> Digital File Delivery</h6>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Upload File (PDF, ZIP, Audio)</label>
                                <input type="file" name="digital_file" class="form-control">
                                <div class="form-text">Customer will receive a secure download link after purchase.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">OR External Download Link</label>
                                <input type="url" name="file_url" class="form-control" placeholder="https://dropbox.com/...">
                                <div class="form-text">If provided, this link will be used instead of the uploaded file.</div>
                            </div>
                        </div>

                        <!-- Service Options -->
                        <div id="serviceOptions" class="p-3 bg-light rounded mb-3 border" style="display:none;">
                            <h6 class="fw-bold mb-2 text-success"><i class="bi bi-calendar-check"></i> Service Details</h6>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Delivery Time</label>
                                <input type="text" name="delivery_time" class="form-control" placeholder="e.g. 3-5 business days, Upon booking, etc.">
                                <div class="form-text">Expected timeframe for service delivery or completion.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Service Notes</label>
                                <textarea name="service_notes" class="form-control" rows="2" placeholder="Any special instructions or requirements"></textarea>
                                <div class="form-text">Any special instructions for the customer.</div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Create Product</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Sync content to hidden input on form submit
    document.querySelector('form').onsubmit = function() {
        var content = document.querySelector('#hiddenDescription');
        content.value = quill.root.innerHTML;
        // Check if empty
        if (quill.getText().trim().length === 0 && content.value.trim() === '<p><br></p>') {
             content.value = '';
        }
    };

    function toggleType() {
        const isDigital = document.getElementById('typeDigital').checked;
        const isService = document.getElementById('typeService').checked;
        
        document.getElementById('physicalOptions').style.display = (isDigital || isService) ? 'none' : 'block';
        document.getElementById('digitalOptions').style.display = isDigital ? 'block' : 'none';
        document.getElementById('serviceOptions').style.display = isService ? 'block' : 'none';
    }
    // Init on load
    toggleType();
</script>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>


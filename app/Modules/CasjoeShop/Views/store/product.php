<?php 
$title = htmlspecialchars($product['name']);
require __DIR__ . '/../layout/store_header.php';
require __DIR__ . '/../helpers/price_helper.php';
?>


<style>
    .product-long-description img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .product-long-description iframe {
        max-width: 100%;
    }
</style>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/shop" class="text-dark text-decoration-none opacity-75">Shop</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Image -->
        <!-- Image -->
        <div class="col-md-6">
            <div class="main-image-container mb-3 position-relative overflow-hidden rounded shadow-sm" style="cursor: zoom-in; background: rgba(255,255,255,0.7); border: 1px solid var(--glass-border); backdrop-filter: blur(10px);">
                <?php if($product['image_path']): ?>
                    <img id="mainImage" src="<?= htmlspecialchars($product['image_path']) ?>" class="img-fluid w-100" style="transition: transform 0.2s ease-out; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded p-5 text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.5);">
                        <i class="bi bi-box fs-1 text-muted" style="font-size: 5rem; opacity: 0.5;"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if(!empty($product['gallery'])): ?>
                <div class="d-flex gap-2 overflow-auto pb-2">
                    <!-- Main Image Thumb -->
                    <img src="<?= htmlspecialchars($product['image_path']) ?>" class="img-thumbnail gallery-thumb border-primary" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="swapImage(this.src, this)">
                    
                    <?php foreach($product['gallery'] as $img): ?>
                        <img src="<?= htmlspecialchars($img['image_path']) ?>" class="img-thumbnail gallery-thumb" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="swapImage(this.src, this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <script>
                // Image Swap
                function swapImage(src, el) {
                    const mainImg = document.getElementById('mainImage');
                    if(mainImg) mainImg.src = src;
                    
                    // Highlight active thumb
                    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('border-primary'));
                    if(el) el.classList.add('border-primary');
                }

                // Zoom Logic
                const container = document.querySelector('.main-image-container');
                const img = document.getElementById('mainImage');
                
                if(container && img) {
                    container.addEventListener('mousemove', function(e) {
                         const { left, top, width, height } = container.getBoundingClientRect();
                         const x = e.clientX - left;
                         const y = e.clientY - top;
                         
                         const xPercent = (x / width) * 100;
                         const yPercent = (y / height) * 100;
                         
                         img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
                         img.style.transform = 'scale(2.5)'; // Zoom level
                    });
                    
                    container.addEventListener('mouseleave', function() {
                        img.style.transform = 'scale(1)';
                        img.style.transformOrigin = 'center center';
                    });
                }
            </script>
        </div>

        <!-- Details -->
        <div class="col-md-6">
            <div class="mb-3">
                <?php if($product['type'] == 'digital'): ?>
                    <span class="badge bg-info text-dark p-2"><i class="bi bi-cloud-arrow-down"></i> Digital Download</span>
                <?php elseif($product['type'] == 'service'): ?>
                    <span class="badge bg-success text-white p-2"><i class="bi bi-person-workspace"></i> Service</span>
                <?php else: ?>
                    <span class="badge bg-secondary p-2"><i class="bi bi-box-seam"></i> Physical Product</span>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center mb-2">
                <div class="text-warning small me-2">
                    <?php 
                    for($i=1; $i<=5; $i++) {
                        if($i <= floor($avgRating)) echo '<i class="bi bi-star-fill"></i>';
                        elseif($i == ceil($avgRating) && $avgRating - floor($avgRating) > 0) echo '<i class="bi bi-star-half"></i>';
                        else echo '<i class="bi bi-star"></i>';
                    }
                    ?>
                </div>
                <span class="text-muted small">(<?= count($reviews) ?> reviews)</span>
            </div>

            <h1 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="text-muted mb-4 small opacity-75">Sold by <strong><?= htmlspecialchars($vendor['store_name']) ?></strong></div>

            <h2 class="text-primary fw-bold mb-3"><?= formatPrice($product['price'], $product['vendor_currency']) ?></h2>
            
            <!-- Temu/Sales Conversion Features -->
            <div class="p-3 rounded mb-4" style="background: rgba(255,0,0,0.1); border: 1px solid rgba(255,0,0,0.3); backdrop-filter: blur(5px);">
                <!-- Urgency Timer -->
                <div class="d-flex align-items-center text-danger fw-bold mb-2">
                    <i class="bi bi-stopwatch me-2 animate__animated animate__flash animate__infinite animate__slower"></i>
                    <span>Limited Time Deal! Ends in: <span id="deal-countdown" class="font-monospace">02:14:30</span></span>
                </div>
                
                <!-- Stock Scarcity (Randomized for demo if no stock field) -->
                <?php $fakeStock = rand(3, 12); ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-bold text-dark">Almost Sold Out!</span>
                        <span class="text-danger fw-bold">Only <?= $fakeStock ?> left</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= rand(70, 95) ?>%"></div>
                    </div>
                </div>

                <!-- Social Proof -->
                <div class="small text-dark opacity-75">
                    <i class="bi bi-fire text-warning"></i> <strong><?= rand(10, 50) ?></strong> people ordered this in the last 24 hours
                </div>
            </div>

            <script>
                // Simple countdown logic
                let time = 7200 + Math.floor(Math.random() * 3600); // Start between 2-3 hours
                setInterval(() => {
                    time--;
                    if(time < 0) time = 7200; // Reset
                    
                    const hours = Math.floor(time / 3600);
                    const minutes = Math.floor((time % 3600) / 60);
                    const seconds = time % 60;
                    
                    document.getElementById('deal-countdown').innerText = 
                        `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                }, 1000);
            </script>

            <!-- Short Description (if exists) -->
            <?php if(!empty($product['description'])): ?>
                <p class="lead text-muted mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <!-- Long Description Moved Below -->

            <!-- Service Details -->
            <?php if($product['type'] == 'service'): ?>
                <div class="alert alert-success mb-4">
                    <h6 class="fw-bold"><i class="bi bi-calendar-check"></i> Service Details</h6>
                    <?php if(!empty($product['delivery_time'])): ?>
                        <p class="mb-1"><strong>Delivery Time:</strong> <?= htmlspecialchars($product['delivery_time']) ?></p>
                    <?php endif; ?>
                    <?php if(!empty($product['service_notes'])): ?>
                        <p class="mb-0"><strong>Notes:</strong> <?= nl2br(htmlspecialchars($product['service_notes'])) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <form action="/shop/cart/add" method="POST" class="flex-grow-1" id="mainCartForm">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="d-flex gap-3 align-items-center">
                        <input type="number" name="quantity" class="form-control" value="1" min="1" style="width: 80px;">
                        <button type="submit" class="btn btn-primary btn-lg px-5 w-100">Add to Cart</button>
                    </div>
                </form>

                <form action="/shop/wishlist/toggle" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-lg btn-outline-danger" title="<?= isset($isWishlisted) && $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' ?>">
                        <?php if(isset($isWishlisted) && $isWishlisted): ?>
                            <i class="bi bi-heart-fill"></i>
                        <?php else: ?>
                            <i class="bi bi-heart"></i>
                        <?php endif; ?>
                    </button>
                </form>
            </div>

            <!-- Share Buttons -->
            <div class="mt-4 border-top pt-3">
                <span class="fw-bold text-muted small text-uppercase me-2">Share:</span>
                <?php $shareUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/shop/product/" . $product['id']; ?>
                
                <a href="https://wa.me/?text=Check+out+<?= urlencode($product['name']) ?>+<?= urlencode($shareUrl) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill me-1 mb-1">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill me-1 mb-1">
                    <i class="bi bi-facebook"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($product['name']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill me-1 mb-1">
                    <i class="bi bi-twitter"></i> Twitter
                </a>
                <button onclick="copyLink('<?= $shareUrl ?>')" class="btn btn-sm btn-light border rounded-pill me-1 mb-1">
                    <i class="bi bi-link-45deg"></i> Copy Link
                </button>
            </div>

            <?php if($product['type'] == 'digital'): ?>
            <div class="mt-4 p-3 bg-light rounded small">
                <i class="bi bi-info-circle-fill text-info"></i> You will receive a secure download link immediately after payment.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Long Description Section -->
    <?php if(!empty($product['long_description'])): ?>
    <div class="row mt-5">
        <div class="col-lg-10 mx-auto">
            <h3 class="fw-bold mb-3 text-center text-dark">Product Details</h3>
            <div class="product-long-description p-4 rounded shadow-sm text-dark" style="background: rgba(255,255,255,0.7); border: 1px solid var(--glass-border); backdrop-filter: blur(10px);">
                <?= $product['long_description'] ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reviews Section -->
    <div class="row mt-5 pt-5 border-top">
        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Customer Reviews</h3>
            
            <?php if(isset($_GET['review_submitted'])): ?>
                <div class="alert alert-success">Thank you! Your review has been submitted.</div>
            <?php endif; ?>

            <?php if(empty($reviews)): ?>
                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <?php foreach($reviews as $r): ?>
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex align-items-center mb-2">
                        <div class="text-warning small me-2">
                            <?php for($i=1; $i<=5; $i++) echo ($i <= $r['rating']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                        </div>
                        <span class="fw-bold me-2"><?= htmlspecialchars($r['user_name']) ?></span>
                        <span class="text-muted small"><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
                    </div>
                    <?php if($r['comment']): ?>
                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.7) !important; border: 1px solid var(--glass-border) !important; backdrop-filter: blur(10px);">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-dark">Write a Review</h5>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="/shop/review" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">★★★★★ (5 Stars)</option>
                                <option value="4">★★★★☆ (4 Stars)</option>
                                <option value="3">★★★☆☆ (3 Stars)</option>
                                <option value="2">★★☆☆☆ (2 Stars)</option>
                                <option value="1">★☆☆☆☆ (1 Star)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Review</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Share your thoughts..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                    </form>
                    <?php else: ?>
                        <p class="text-muted mb-3">Please login to write a review.</p>
                        <a href="/shop/login?redirect=/shop/product/<?= $product['id'] ?>" class="btn btn-outline-primary w-100">Login to Review</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

<!-- Sticky Add to Cart Bar -->
<div id="sticky-cart-bar" class="fixed-bottom shadow-lg border-top p-3 d-none" style="z-index: 1050; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); border-color: rgba(0,0,0,0.1) !important;">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-none d-md-block">
            <h6 class="m-0 text-truncate" style="max-width: 300px;"><?= htmlspecialchars($product['name']) ?></h6>
            <small class="text-primary fw-bold"><?= formatPrice($product['price'], $product['vendor_currency']) ?></small>
        </div>
        <div class="d-flex gap-2 align-items-center ms-auto">
            <div class="d-md-none me-3">
                 <span class="text-primary fw-bold"><?= formatPrice($product['price'], $product['vendor_currency']) ?></span>
            </div>
            <button onclick="document.querySelector('#mainCartForm').submit()" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                Add to Cart
            </button>
        </div>
    </div>
</div>

<script>
    function copyLink(url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
                prompt("Copy this link:", url);
            });
        } else {
            prompt("Copy this link:", url);
        }
    }

    // Show sticky bar when main button scrolls out of view
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#mainCartForm');
        const stickyBar = document.querySelector('#sticky-cart-bar');
        
        if(form && stickyBar) {
            const observer = new IntersectionObserver((entries) => {
                if (!entries[0].isIntersecting) {
                    // Button is out of view (scrolled past)
                    // Check if we are below it (getBoundingClientRect().top < 0)
                    if (entries[0].boundingClientRect.top < 0) {
                        stickyBar.classList.remove('d-none');
                        stickyBar.classList.add('d-block', 'animate__animated', 'animate__slideInUp');
                    } else {
                         // Above it (shouldn't happen often but valid)
                        stickyBar.classList.add('d-none');
                        stickyBar.classList.remove('d-block');
                    }
                } else {
                    // Button is in view
                    stickyBar.classList.add('d-none');
                    stickyBar.classList.remove('d-block');
                }
            }, { threshold: 0 });
            
            observer.observe(form);
        }
    });
</script>


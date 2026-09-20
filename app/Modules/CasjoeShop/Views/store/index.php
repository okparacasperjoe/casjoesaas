<?php 
$title = "Shop Home";
require __DIR__ . '/../layout/store_header.php';
require __DIR__ . '/../helpers/price_helper.php';
?>

<style>
    .glass-banner {
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow: var(--glass-shadow);
        position: relative;
        overflow: hidden;
    }
    .glass-banner::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        animation: rotate 20s linear infinite;
        pointer-events: none;
    }
    @keyframes rotate { 100% { transform: rotate(360deg); } }

    .product-card {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid var(--glass-border) !important;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        backdrop-filter: blur(10px);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--glass-shadow);
        border-color: rgba(255, 255, 255, 0.8) !important;
    }
    .product-img-wrapper {
        position: relative;
        overflow: hidden;
    }
    .product-img-wrapper img {
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-img-wrapper img {
        transform: scale(1.05);
    }
    .glass-badge {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(5px);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
    }
    .product-card:hover .quick-add-btn {
        bottom: 1rem !important;
        opacity: 1 !important;
        z-index: 10;
    }
    .product-card {
        padding-bottom: 0;
        transition: padding 0.3s ease, transform 0.3s ease;
    }
    .product-card:hover {
        padding-bottom: 3.5rem;
    }
</style>

<div class="container mt-4 mb-5">
    <!-- Top Banner -->
    <?php if(isset($adsByLocation['top']) && $adsByLocation['top']['is_active']): 
        $banner_ad = $adsByLocation['top'];
        include __DIR__ . '/ad_banner.php';
    endif; ?>
    
    <!-- Dynamic Hero Carousel -->
    <div id="heroCarousel" class="carousel slide mb-4 glass-banner overflow-hidden" data-bs-ride="carousel" style="border-radius: 20px;">
        <div class="carousel-inner">
            <?php if(!empty($sliders)): foreach($sliders as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> p-5 text-center" 
                     style="background: <?= $slide['image_url'] ? 'url('.htmlspecialchars($slide['image_url']).') center/cover' : htmlspecialchars($slide['background_overlay'] ?? 'rgba(255,255,255,0.2)') ?>;">
                    <?php if($slide['image_url']): ?>
                        <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:<?= htmlspecialchars($slide['background_overlay'] ?? 'rgba(255,255,255,0.7)') ?>;"></div>
                    <?php endif; ?>
                    <div class="container-fluid py-4 position-relative" style="z-index: 1;">
                        <?php if($slide['badge_text']): ?>
                            <span class="badge bg-<?= htmlspecialchars($slide['badge_color'] ?? 'primary') ?> rounded-pill mb-3 px-3 py-2"><?= htmlspecialchars($slide['badge_text']) ?></span>
                        <?php endif; ?>
                        <h1 class="display-4 fw-bold text-dark mb-3"><?= htmlspecialchars($slide['title']) ?></h1>
                        <?php if($slide['subtitle']): ?>
                            <p class="col-md-8 fs-5 mx-auto text-dark opacity-75"><?= htmlspecialchars($slide['subtitle']) ?></p>
                        <?php endif; ?>
                        <?php if($slide['button_text']): ?>
                            <div class="mt-4">
                                <a href="<?= htmlspecialchars($slide['button_link'] ?? '#') ?>" class="btn btn-<?= htmlspecialchars($slide['button_color'] ?? 'primary') ?> btn-lg rounded-pill px-5 shadow"><?= htmlspecialchars($slide['button_text']) ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <!-- Fallback if no sliders exist -->
                <div class="carousel-item active p-5 text-center" style="background: rgba(255,255,255,0.2);">
                    <div class="container-fluid py-4 position-relative" style="z-index: 1;">
                        <h1 class="display-4 fw-bold text-dark mb-3">Welcome to Mart</h1>
                        <p class="col-md-8 fs-5 mx-auto text-dark opacity-75">Discover premium products crafted for excellence.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if(count($sliders) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true" style="opacity:0.5;"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true" style="opacity:0.5;"></span>
        </button>
        <?php endif; ?>
    </div>

    <!-- Category Quick-Links Bar -->
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .cat-icon-hover:hover { transform: scale(1.1); background: rgba(255,255,255,1) !important; }
    </style>
    <div class="d-flex overflow-auto gap-3 py-3 mb-5 no-scrollbar align-items-center" style="scrollbar-width: none; -ms-overflow-style: none;">
        <?php 
        $quickIcons = ['Software' => 'bi-laptop', 'E-Books' => 'bi-book', 'Graphics' => 'bi-palette', 'Music' => 'bi-music-note', 'Video' => 'bi-camera-video', 'Services' => 'bi-person-workspace', 'Physical' => 'bi-box-seam'];
        foreach($categories as $cat): 
            $icon = $quickIcons[$cat] ?? 'bi-tag';
        ?>
            <a href="/shop?category=<?= urlencode($cat) ?>" class="text-decoration-none text-center flex-shrink-0" style="width: 100px;">
                <div class="cat-icon-hover d-flex justify-content-center align-items-center rounded-circle mx-auto mb-2 glass-banner" style="width: 60px; height: 60px; background: rgba(255,255,255,0.7); transition: all 0.2s;">
                    <i class="bi <?= $icon ?> fs-4 text-primary"></i>
                </div>
                <span class="small fw-bold text-dark text-truncate d-block w-100"><?= htmlspecialchars($cat) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Flash Deals Section -->
    <?php if(!empty($deals)): ?>
    <div id="flash-deals" class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Flash Deals</h3>
            <a href="/shop?deals=1" class="text-primary text-decoration-none fw-bold small">See All <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="d-flex overflow-auto gap-4 pb-3 no-scrollbar" style="scrollbar-width: none;">
            <?php foreach($deals as $deal): ?>
                <div class="card product-card flex-shrink-0" style="width: 250px;">
                    <div class="position-absolute top-0 start-0 m-2" style="z-index: 5;">
                        <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm">-<?= round((($deal['compare_price'] - $deal['price']) / $deal['compare_price']) * 100) ?>%</span>
                    </div>
                    <a href="/shop/product/<?= $deal['id'] ?>" class="text-decoration-none">
                        <div class="product-img-wrapper" style="height: 180px; background: rgba(0,0,0,0.05);">
                            <?php if($deal['image_path']): ?>
                                <img src="<?= htmlspecialchars($deal['image_path']) ?>" class="card-img-top h-100 w-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="h-100 w-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image fs-1 text-muted opacity-25"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="text-dark text-truncate mb-1 fw-bold"><?= htmlspecialchars($deal['name']) ?></h6>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fw-bold text-primary"><?= formatPrice($deal['price'], $deal['vendor_currency']) ?></span>
                                <span class="text-muted text-decoration-line-through small"><?= formatPrice($deal['compare_price'], $deal['vendor_currency']) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content Layout -->
    <div class="row" id="products">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-header bg-transparent py-3 border-bottom">
                    <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2 text-gold"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <form action="/shop" method="GET">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Search</label>
                            <input type="text" name="q" class="form-control rounded-pill" placeholder="Keywords..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Category</label>
                            <select name="category" class="form-select rounded-pill">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control rounded-pill text-center" placeholder="Min" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control rounded-pill text-center" placeholder="Max" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3 mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow">Apply Filters</button>
                            <a href="/shop" class="btn btn-outline-light rounded-pill">Clear All</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Middle Banner (Advertisement) -->
            <?php if(isset($adsByLocation['middle']) && $adsByLocation['middle']['is_active']): 
                $banner_ad = $adsByLocation['middle'];
                include __DIR__ . '/ad_banner.php';
            endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: rgba(0,0,0,0.1) !important;">
                <h3 class="fw-bold mb-0 text-dark">Latest Arrivals</h3>
                <span class="badge glass-badge rounded-pill px-3 py-2"><?= count($products) ?> items found</span>
            </div>

            <?php if(empty($products)): ?>
                <div class="text-center py-5 glass-banner mt-4">
                    <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block opacity-50"></i>
                    <h4 class="text-dark">Nothing found</h4>
                    <p class="text-muted">No products found matching your current criteria.</p>
                    <a href="/shop" class="btn btn-primary mt-2 rounded-pill px-4">Browse All</a>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
                    <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card">
                            <!-- Share Button -->
                            <div class="position-absolute top-0 end-0 p-2" style="z-index: 5;">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-dark bg-opacity-50 rounded-circle shadow-sm dropdown-toggle hide-arrow border-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 35px; height: 35px; padding: 0; backdrop-filter: blur(5px);">
                                        <i class="bi bi-share-fill small"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                                        <?php $shareUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/shop/product/" . $product['id']; ?>
                                        <li><a class="dropdown-item text-dark small" href="https://wa.me/?text=Check+out+<?= urlencode($product['name']) ?>+<?= urlencode($shareUrl) ?>" target="_blank"><i class="bi bi-whatsapp text-success me-2"></i> WhatsApp</a></li>
                                        <li><a class="dropdown-item text-dark small" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank"><i class="bi bi-facebook text-primary me-2"></i> Facebook</a></li>
                                        <li><a class="dropdown-item text-dark small" href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($product['name']) ?>" target="_blank"><i class="bi bi-twitter text-info me-2"></i> Twitter</a></li>
                                        <li><hr class="dropdown-divider border-secondary"></li>
                                        <li><button class="dropdown-item text-dark small" onclick="copyLink('<?= $shareUrl ?>')"><i class="bi bi-link-45deg me-2"></i> Copy Link</button></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <a href="/shop/product/<?= $product['id'] ?>" class="text-decoration-none">
                                <div class="product-img-wrapper" style="height: 220px; background: rgba(0,0,0,0.2);">
                                    <?php if($product['image_path']): ?>
                                        <img src="<?= htmlspecialchars($product['image_path']) ?>" class="card-img-top h-100 w-100" alt="<?= htmlspecialchars($product['name']) ?>" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="h-100 w-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image fs-1 text-muted opacity-25"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badges over image -->
                                    <div class="position-absolute bottom-0 start-0 p-2 w-100 d-flex gap-2" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                                        <?php if($product['type'] == 'digital'): ?>
                                            <span class="badge bg-primary bg-opacity-75 rounded-pill border border-primary"><i class="bi bi-cloud-download me-1"></i> Digital</span>
                                        <?php elseif($product['type'] == 'service'): ?>
                                            <span class="badge bg-warning bg-opacity-75 text-dark rounded-pill border border-warning"><i class="bi bi-person-workspace me-1"></i> Service</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-75 rounded-pill border border-secondary"><i class="bi bi-box-seam me-1"></i> Physical</span>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($product['category'])): ?>
                                            <span class="badge glass-badge rounded-pill"><?= htmlspecialchars($product['category']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column" style="background: transparent;">
                                    <h5 class="card-title fw-bold text-dark mb-2" style="line-height: 1.4;">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </h5>
                                    
                                    <p class="card-text text-muted small text-truncate-2 mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal;">
                                        <?= htmlspecialchars($product['description'] ?? '') ?>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top position-relative" style="border-color: rgba(255,255,255,0.05) !important;">
                                        <h5 class="text-gold fw-bold mb-0"><?= formatPrice($product['price'], $product['vendor_currency']) ?></h5>
                                        <?php if(isset($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                                            <span class="text-muted text-decoration-line-through small"><?= formatPrice($product['compare_price'], $product['vendor_currency']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Hover Add to Cart Button -->
                                    <div class="quick-add-btn position-absolute w-100 start-0 text-center" style="bottom: -50px; transition: all 0.3s ease; opacity: 0; padding: 0 1rem;">
                                        <form action="/shop/cart/add" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow">
                                                <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Spin to Win Popup -->
<div class="modal fade" id="spinWinModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 text-center glass-banner">
            <div class="modal-body p-5 position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <h2 class="fw-bold text-primary mb-3">🎉 CONGRATULATIONS!</h2>
                <p class="lead fw-bold mb-4 text-dark">You've been selected for a special welcoming gift!</p>
                
                <div id="wheel-container" class="mb-4 d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div id="spinner" style="font-size: 5rem; transition: transform 3s ease-out;">🎡</div>
                </div>

                <div id="pre-spin">
                    <button class="btn btn-primary btn-lg px-5 shadow-lg rounded-pill fw-bold" onclick="spinWheel()">SPIN TO WIN</button>
                    <p class="small mt-2 text-muted">Limited time offer. Ends soon.</p>
                </div>

                <div id="post-spin" class="d-none animate__animated animate__zoomIn">
                    <h3 class="fw-bold text-success mb-2">YOU WON 10% OFF!</h3>
                    <p class="mb-3 text-dark">Use code at checkout:</p>
                    <div class="bg-light bg-opacity-50 p-3 rounded-3 border border-2 border-dashed border-dark mb-3">
                        <h4 class="m-0 fw-bold font-monospace text-primary tracking-widest">WELCOME10</h4>
                    </div>
                    <button class="btn btn-success btn-lg px-5 shadow fw-bold rounded-pill" data-bs-dismiss="modal">SHOP NOW</button>
                </div>
            </div>
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

document.addEventListener('DOMContentLoaded', function() {
    // Check if user has seen popup
    if (!sessionStorage.getItem('casjoe_popup_seen')) {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                var spinModal = new bootstrap.Modal(document.getElementById('spinWinModal'));
                spinModal.show();
                sessionStorage.setItem('casjoe_popup_seen', 'true');
            }
        }, 5000); // Show after 5 seconds
    }
});

function spinWheel() {
    const spinner = document.getElementById('spinner');
    const preSpin = document.getElementById('pre-spin');
    const postSpin = document.getElementById('post-spin');
    
    // Spin animation (3 full rotations + random)
    const deg = 1080 + Math.floor(Math.random() * 360);
    spinner.style.transform = `rotate(${deg}deg)`;
    
    // Disable button
    preSpin.querySelector('button').disabled = true;
    
    // Show result after animation
    setTimeout(() => {
        preSpin.classList.add('d-none');
        postSpin.classList.remove('d-none');
    }, 3000);
}
</script>

<!-- Newsletter Signup -->
<div class="container mb-5">
    <div class="glass-banner p-5 text-center">
        <h3 class="fw-bold text-dark mb-2">Join Our Newsletter</h3>
        <p class="text-dark opacity-75 mb-4">Get the latest updates on new products and upcoming sales.</p>
        <form class="d-flex justify-content-center max-w-md mx-auto" style="max-width: 500px;" onsubmit="event.preventDefault(); alert('Subscribed successfully!');">
            <input type="email" class="form-control form-control-lg rounded-start-pill border-0" placeholder="Enter your email address" required style="background: rgba(255,255,255,0.8);">
            <button type="submit" class="btn btn-primary btn-lg rounded-end-pill px-4 fw-bold">Subscribe</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

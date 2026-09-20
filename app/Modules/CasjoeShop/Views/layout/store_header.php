<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <title><?= $title ?? 'Casjoe Mart' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root { 
            --primary-color: #f4f6f9; 
            --secondary-color: #f68b1e;
            --brand-blue: #0052cc;
            --text-main: #333333;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        body.casjoe-mart-body { 
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%) !important; 
            color: var(--text-main) !important; 
            min-height: 100vh;
        }
        .text-primary { color: var(--secondary-color) !important; }
        .text-gold { color: var(--secondary-color) !important; }
        .bg-gold { background-color: var(--secondary-color) !important; }
        .btn-primary { background: var(--secondary-color); border: none; color: #fff; }
        .btn-primary:hover { background: #e07b1a; color: #fff; }
        .btn-outline-primary { color: var(--secondary-color); border-color: var(--secondary-color); }
        .btn-outline-primary:hover { background-color: var(--secondary-color); color: white; }
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(12px); border-bottom: 1px solid var(--glass-border); box-shadow: var(--glass-shadow); }
        .navbar-brand { font-weight: bold; color: var(--secondary-color) !important; display: flex; align-items: center; gap: 10px; }
        .nav-link { color: var(--text-main) !important; font-weight: 500; }
        .nav-link:hover { color: var(--secondary-color) !important; }
        .badge-count { position: absolute; top: 0; right: 0; transform: translate(50%, -50%); font-size: 0.75rem; }
        .card { background: var(--glass-bg) !important; border: 1px solid var(--glass-border) !important; backdrop-filter: blur(10px); color: var(--text-main); box-shadow: var(--glass-shadow); }
        .card-header { background: rgba(255, 255, 255, 0.4) !important; border-bottom: 1px solid var(--glass-border) !important; color: var(--text-main); }
        .form-control, .form-select { background: rgba(255, 255, 255, 0.5) !important; border: 1px solid var(--glass-border) !important; color: var(--text-main) !important; }
        .form-control:focus, .form-select:focus { background: rgba(255, 255, 255, 0.8) !important; border-color: var(--secondary-color) !important; box-shadow: 0 0 0 0.25rem rgba(246, 139, 30, 0.25) !important; }
        .form-control::placeholder { color: #888 !important; }
    </style>
    </style>
    <?php if(isset($vendor['facebook_pixel_id']) && !empty($vendor['facebook_pixel_id'])): ?>
    <!-- Meta Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?= htmlspecialchars($vendor['facebook_pixel_id']) ?>');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=<?= htmlspecialchars($vendor['facebook_pixel_id']) ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <?php endif; ?>
</head>
<body class="casjoe-mart-body">

<nav class="navbar navbar-expand-lg navbar-light bg-transparent sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/shop">
            <img src="/assets/casjoe_logo.webp" alt="Logo" style="height: 40px;">
            Mart
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#shopNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="shopNav">
            <ul class="navbar-nav me-3 mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/shop">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
                <li class="nav-item"><a class="nav-link text-gold fw-bold" href="/shop/vendor/register">Sell with us</a></li>
            </ul>

            <!-- Global Search -->
            <form class="d-flex mx-auto mb-2 mb-lg-0" action="/shop" method="GET" style="flex-grow: 1; max-width: 500px;">
                <div class="input-group">
                    <input class="form-control border-end-0 border" type="search" name="q" placeholder="Search for products, brands and more" aria-label="Search" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn btn-outline-secondary border-start-0 border" type="submit" style="background: white;">
                        <i class="bi bi-search text-muted"></i>
                    </button>
                </div>
            </form>
            <div class="d-flex align-items-center gap-3">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="text-decoration-none text-dark dropdown-toggle" id="userMenu" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="/shop/account"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="/shop/account/orders"><i class="bi bi-box-seam"></i> My Orders</a></li>
                            <li><a class="dropdown-item" href="/shop/account/wishlist"><i class="bi bi-heart"></i> My Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/shop/vendor/dashboard">Vendor Portal</a></li>
                            <li><a class="dropdown-item text-danger" href="/shop/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/shop/login" class="btn btn-sm btn-outline-primary">Login</a>
                <?php endif; ?>

                <?php require __DIR__ . '/../components/currency_switcher.php'; ?>

                <a href="/shop/cart" class="position-relative text-dark">
                    <i class="bi bi-cart3 fs-4"></i>
                    <?php if(!empty($_SESSION['cart'])): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= count($_SESSION['cart']) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>


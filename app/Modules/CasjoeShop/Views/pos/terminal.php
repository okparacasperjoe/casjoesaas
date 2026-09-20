<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>POS Terminal | Casjoe Mart</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --primary: #000066;
            --primary-dark: #000044;
            --secondary: #FFA600;
            --secondary-hover: #ffb733;
            --text-color: #eeeeee;
            --bg-gradient-start: #111111;
            --bg-gradient-end: #000000;
            --glass-bg: rgba(30, 30, 30, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        body { 
            margin: 0; padding: 0; overflow: hidden; height: 100vh; display: flex; flex-direction: column; 
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            color: var(--text-color);
        }
        
        .pos-container { display: flex; flex: 1; height: 100%; backdrop-filter: blur(5px); }
        
        /* Left: Products */
        .pos-products { flex: 1; padding: 20px; overflow-y: auto; background: transparent; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
        
        .product-card { 
            background: var(--glass-bg); 
            border: 1px solid var(--glass-border);
            border-radius: 16px; 
            padding: 15px; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            box-shadow: var(--glass-shadow);
            text-align: center;
            color: #fff;
        }
        
        .product-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.4); 
            border-color: var(--secondary);
        }
        
        .product-img { 
            width: 100%; height: 120px; object-fit: cover; border-radius: 12px; margin-bottom: 12px; 
            background: #222; 
        }
        .product-name { font-weight: 600; font-size: 1rem; margin-bottom: 5px; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .product-price { font-weight: 700; color: var(--secondary); font-size: 1.1rem; }
        .product-stock { font-size: 0.8rem; color: #aaa; margin-top: 5px; }

        /* Right: Cart */
        .pos-cart { 
            width: 420px; 
            background: rgba(10, 10, 10, 0.95); 
            border-left: 1px solid var(--glass-border); 
            display: flex; flex-direction: column; 
            box-shadow: -5px 0 20px rgba(0,0,0,0.5);
        }
        
        .cart-header { 
            padding: 25px; 
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%); 
            color: #fff; 
            font-weight: 700; 
            font-size: 1.2rem;
            display: flex; justify-content: space-between; align-items: center;
             border-bottom: 1px solid var(--glass-border);
        }
        
        .cart-items { flex: 1; overflow-y: auto; padding: 15px; }
        
        .cart-item { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            animation: fadeIn 0.3s ease;
        }
        
        .cart-item-info { flex: 1; }
        .cart-item-info strong { color: #fff; display: block; margin-bottom: 4px; }
        .cart-item-info small { color: #888; }
        
        .cart-item-qty { display: flex; align-items: center; gap: 10px; margin: 0 15px; background: #222; padding: 5px 10px; border-radius: 20px; border: 1px solid #333; }
        .qty-btn { background: transparent; border: none; width: 20px; height: 20px; color: #fff; cursor: pointer; font-weight: bold; }
        .qty-btn:hover { color: var(--secondary); }
        
        .cart-footer { padding: 25px; background: #050505; border-top: 1px solid var(--glass-border); }
        .cart-total { font-size: 1.8rem; font-weight: 800; text-align: right; margin-bottom: 25px; color: #fff; }
        .cart-total span { color: var(--secondary); }
        
        .checkout-btn { 
            width: 100%; 
            padding: 18px; 
            border: none; 
            font-size: 1.1rem; 
            font-weight: 700; 
            border-radius: 12px; 
            cursor: pointer; 
            transition: all 0.3s; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-cash { background: #28a745; color: #fff; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); }
        .btn-cash:hover { background: #218838; transform: translateY(-2px); }
        
        .btn-card { background: #007bff; color: #fff; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); }
        .btn-card:hover { background: #0069d9; transform: translateY(-2px); }

        .search-bar { margin-bottom: 25px; }
        .search-input { 
            width: 100%; padding: 15px 20px; 
            background: rgba(255, 255, 255, 0.08); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 12px; 
            font-size: 1.1rem; 
            color: #fff;
            outline: none;
            transition: all 0.3s;
        }
        .search-input:focus { border-color: var(--secondary); background: rgba(255, 255, 255, 0.12); box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.15); }
        
        /* Form controls (Select/Inputs) */
        .form-control { 
            background: #1a1a1a; border: 1px solid #333; color: #fff; 
            padding: 12px; border-radius: 8px; outline: none; 
        }
        .form-control:focus { border-color: var(--secondary); }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div style="background: rgba(0,0,0,0.8); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px 25px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: 800; font-size: 1.2rem; color: #fff;">CASJOE <span style="color: var(--secondary);">POS</span></span>
        <a href="/shop/vendor/dashboard" style="color: #888; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; transition: color 0.3s;">
            <ion-icon name="log-out-outline" style="font-size: 1.2rem;"></ion-icon> Exit Terminal
        </a>
    </div>

    <div class="pos-container">
        <!-- Products Area -->
        <main class="pos-products">
            <div class="search-bar" style="display: flex; gap: 15px;">
                <input type="text" id="search" class="search-input" placeholder="Scan Barcode or Search Product..." autofocus autocomplete="off">
                <button onclick="handleManualSearch()" class="btn" style="background: var(--secondary); color: #000; font-weight: bold; border: none; padding: 0 30px; border-radius: 12px; cursor: pointer;">ADD</button>
            </div>
            <div id="scanStatus" style="display:none; padding: 15px; margin-bottom: 20px; border-radius: 12px; text-align: center; font-weight: 500;"></div>
            
            <div class="product-grid" id="grid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card" onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)">
                        <img src="<?= $p['image_path'] ?? '/assets/placeholder.png' ?>" class="product-img">
                        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="product-price">₦<?= number_format($p['price'], 2) ?></div>
                        <div class="product-stock"><?= $p['stock_quantity'] ?> in stock</div>
                        <div style="display:none;" class="product-sku"><?= $p['sku'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <aside class="pos-cart">
            <div class="cart-header">
                <span>Current Sale</span>
                <ion-icon name="cart-outline"></ion-icon>
            </div>
            
            <!-- Customer Select -->
            <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <select id="customer_id" class="form-control" style="width: 100%;">
                    <option value="">Walk-in Customer</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="cart-items" id="cartItems">
                <div style="text-align: center; color: #555; margin-top: 80px; display: flex; flex-direction: column; align-items: center;">
                    <ion-icon name="basket-outline" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></ion-icon>
                    <div>Cart is empty</div>
                    <small>Scan items or click to add</small>
                </div>
            </div>

            <div class="cart-footer">
                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <input type="number" id="discount" class="form-control" placeholder="Discount (₦)" style="width: 100%;" onchange="renderCart()">
                    <button onclick="holdCart()" style="background: #333; color: #aaa; border: 1px solid #444; padding: 12px; border-radius: 8px; cursor: pointer; flex: 1;"><ion-icon name="pause-outline"></ion-icon></button>
                    <button onclick="restoreCart()" style="background: #333; color: #aaa; border: 1px solid #444; padding: 12px; border-radius: 8px; cursor: pointer; flex: 1;"><ion-icon name="refresh-outline"></ion-icon></button>
                </div>

                <div class="cart-total">
                    <small style="font-size: 0.9rem; color: #888; font-weight: normal; display: block;">Total Amount</small>
                    ₦<span id="cartTotal">0.00</span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <button class="checkout-btn btn-cash" onclick="checkout('cash')"> <ion-icon name="cash-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> CASH</button>
                    <button class="checkout-btn btn-card" onclick="checkout('card')"> <ion-icon name="card-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> CARD</button>
                </div>
            </div>
        </aside>
    </div>

    <script>
        // Inject filtered products from PHP for effective searching
        const allProducts = <?= json_encode($products) ?>;
        let cart = [];

        // Search & Barcode Logic
        const searchInput = document.getElementById('search');
        const grid = document.getElementById('grid');
        const statusEl = document.getElementById('scanStatus');

        function showStatus(msg, type) {
            statusEl.innerText = msg;
            statusEl.style.display = 'block';
            statusEl.style.background = type === 'error' ? 'rgba(220, 53, 69, 0.2)' : 'rgba(40, 167, 69, 0.2)';
            statusEl.style.color = type === 'error' ? '#ff6b6b' : '#5ddc79';
            statusEl.style.border = type === 'error' ? '1px solid rgba(220, 53, 69, 0.3)' : '1px solid rgba(40, 167, 69, 0.3)';
            setTimeout(() => { statusEl.style.display = 'none'; }, 3000);
        }

        // Search Listener
        searchInput.addEventListener('input', (e) => {
             const query = searchInput.value.toLowerCase().trim();
             // 2. Filter Grid (Real-time)
            const filtered = allProducts.filter(p => {
                return p.name.toLowerCase().includes(query) || 
                       (p.sku && p.sku.toLowerCase().includes(query));
            });
            renderGrid(filtered);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                handleScan();
            }
        });

        function handleManualSearch() {
            handleScan();
        }

        function handleScan() {
            const query = searchInput.value.toLowerCase().trim();
            if (query.length === 0) return;

             // 1. Barcode Scan Handing (Exact SKU Match)
            const exactMatch = allProducts.find(p => 
                (p.sku && p.sku.toLowerCase() === query) || 
                (p.bar_code && p.bar_code.toLowerCase() === query)
            );
            
            if (exactMatch) {
                addToCart(exactMatch);
                searchInput.value = ''; // Reset for next scan
                renderGrid(allProducts); // Reset view
                playBeep();
                showStatus(`Added: ${exactMatch.name}`, 'success');
                return;
            }

            showStatus(`Product not found: ${searchInput.value}`, 'error');
            searchInput.select(); // Select text for easy retry
        }

        function renderGrid(productsToRender) {
            grid.innerHTML = '';
            productsToRender.forEach(p => {
                const div = document.createElement('div');
                div.className = 'product-card';
                // Encode object for onclick
                const pJson = JSON.stringify(p).replace(/"/g, '&quot;');
                div.setAttribute('onclick', `addToCart(${pJson})`);
                
                div.innerHTML = `
                    <img src="${p.image_path || '/assets/placeholder.png'}" class="product-img">
                    <div class="product-name">${p.name}</div>
                    <div class="product-price">₦${parseFloat(p.price).toFixed(2)}</div>
                    <div class="product-stock">${p.stock_quantity} in stock</div>
                    <div style="display:none;" class="product-sku">${p.sku}</div>
                `;
                grid.appendChild(div);
            });
        }

        function addToCart(product) {
            const existing = cart.find(i => i.id === product.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ ...product, qty: 1 });
            }
            renderCart();
            searchInput.focus(); // Keep focus for next scan
        }

        function updateQty(index, change) {
            cart[index].qty += change;
            if (cart[index].qty <= 0) cart.splice(index, 1);
            renderCart();
        }

        function playBeep() {
            // Optional: beep sound for confirmation
            // const audio = new Audio('/assets/beep.mp3'); audio.play().catch(e=>{});
        }
        
        async function checkout(method) {
            if (cart.length === 0) return alert('Cart is empty!');
            
            const discount = parseFloat(document.getElementById('discount').value || 0);
            const total = parseFloat(document.getElementById('cartTotal').innerText);
            const customerId = document.getElementById('customer_id').value;

            if (!confirm(`Process ₦${total} via ${method.toUpperCase()}?`)) return;

            // Simple loading state
            document.querySelectorAll('.checkout-btn').forEach(b => b.disabled = true);

            try {
                const response = await fetch('/shop/pos/checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart, total, discount, payment_method: method, customer_id: customerId })
                });

                const result = await response.json();
                
                if (result.success) {
                    printReceipt(result.order_id, method, total, discount);
                    alert('Order Completed! ID: ' + result.order_id);
                    cart = [];
                    document.getElementById('discount').value = '';
                    renderCart();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (err) {
                alert('Network Error');
            }
            
            document.querySelectorAll('.checkout-btn').forEach(b => b.disabled = false);
        }

        // Hold Cart Logic
        function holdCart() {
            if (cart.length === 0) return;
            localStorage.setItem('pos_hold_cart', JSON.stringify(cart));
            cart = [];
            renderCart();
            alert('Cart Held!');
        }

        function restoreCart() {
            const held = localStorage.getItem('pos_hold_cart');
            if (!held) return alert('No held cart found');
            cart = JSON.parse(held);
            localStorage.removeItem('pos_hold_cart');
            renderCart();
        }

        function printReceipt(orderId, method, total, discount) {
            const win = window.open('', '', 'width=300,height=600');
            let itemsHtml = '';
            cart.forEach(i => itemsHtml += `<div>${i.name} x${i.qty} - ₦${(i.price * i.qty).toFixed(2)}</div>`);
            
            win.document.write(`
                <html>
                <body style="font-family: monospace; font-size: 12px; width: 280px;">
                    <div style="text-align:center;">
                        <h3>CASJOE MART</h3>
                        <p>Order #${orderId}</p>
                    </div>
                    <hr>
                    ${itemsHtml}
                    <hr>
                    <div style="text-align:right;">
                        Discount: -₦${discount.toFixed(2)}<br>
                        <strong>TOTAL: ₦${total.toFixed(2)}</strong><br>
                        Paid via: ${method.toUpperCase()}
                    </div>
                    <p style="text-align:center; margin-top:20px;">Thank You!</p>
                </body>
                </html>
            `);
            win.print();
            // win.close(); // Optional, keep open to confirm
        }

        // Render override to include discount calc
        function renderCart() {
            const container = document.getElementById('cartItems');
            const totalEl = document.getElementById('cartTotal');
            
            container.innerHTML = '';
            let subtotal = 0;

            if (cart.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: #999; margin-top: 50px;">Cart is empty</div>';
                totalEl.innerText = '0.00';
                return;
            }

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                
                const div = document.createElement('div');
                div.className = 'cart-item';
                div.innerHTML = `
                    <div class="cart-item-info">
                        <strong>${item.name}</strong><br>
                        <small>₦${item.price} x ${item.qty}</small>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                        <span>${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                    <div style="font-weight: bold;">₦${itemTotal.toFixed(2)}</div>
                `;
                container.appendChild(div);
            });

            const discount = parseFloat(document.getElementById('discount').value || 0);
            const total = Math.max(0, subtotal - discount);
            totalEl.innerText = total.toFixed(2);
        }
    </script>
</body>
</html>


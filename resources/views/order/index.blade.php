<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - {{ $meja->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); margin: 0; padding: 0; padding-bottom: 80px; }
        
        .header { background: white; padding: 20px; text-align: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 10; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; }
        .header p { margin: 4px 0 0 0; font-size: 13px; color: var(--text-muted); }

        .container { max-width: 600px; margin: 0 auto; padding: 16px; }

        .customer-card { background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .input-group { margin-bottom: 12px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .input-control { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none; font-family: 'Inter', sans-serif; }
        .input-control:focus { border-color: var(--primary); }
        .input-readonly { background: #f1f5f9; color: var(--text-muted); cursor: not-allowed; }

        .categories { display: flex; gap: 10px; overflow-x: auto; margin-bottom: 20px; scrollbar-width: none; -ms-overflow-style: none; }
        .categories::-webkit-scrollbar { display: none; }
        .category-pill { padding: 8px 16px; background: white; border-radius: 20px; font-size: 13px; font-weight: 600; color: var(--text-muted); border: 1px solid var(--border); cursor: pointer; white-space: nowrap; transition: all 0.2s; }
        .category-pill.active { background: var(--primary); color: white; border-color: var(--primary); }

        .product-list { display: flex; flex-direction: column; gap: 12px; }
        .product-card { display: flex; background: white; padding: 12px; border-radius: 12px; border: 1px solid var(--border); align-items: center; justify-content: space-between; }
        .product-info { flex: 1; }
        .product-name { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .product-price { font-size: 14px; color: var(--primary); font-weight: 600; }
        .btn-add { background: var(--bg); color: var(--text-main); border: 1px solid var(--border); border-radius: 8px; padding: 6px 12px; font-size: 13px; font-weight: 600; cursor: pointer; height: 32px; }
        .qty-controls { display: flex; align-items: center; gap: 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 4px; height: 32px; box-sizing: border-box; }
        .qty-btn { background: none; border: none; font-size: 16px; font-weight: 600; cursor: pointer; width: 24px; display: flex; align-items: center; justify-content: center; }
        .qty-number { font-size: 14px; font-weight: 600; width: 20px; text-align: center; }

        .floating-cart { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 16px; border-top: 1px solid var(--border); box-shadow: 0 -4px 12px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; z-index: 20; }
        .cart-total { display: flex; flex-direction: column; }
        .cart-total-label { font-size: 12px; color: var(--text-muted); }
        .cart-total-value { font-size: 18px; font-weight: 800; color: var(--text-main); }
        .btn-checkout { background: var(--primary); color: white; border: none; border-radius: 8px; padding: 12px 24px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:disabled { background: #94a3b8; cursor: not-allowed; }

        /* Modal Checkout */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
        .modal-container { background: white; width: 100%; max-width: 400px; border-radius: 16px; padding: 24px; box-sizing: border-box; }
        .modal-title { font-size: 18px; font-weight: 800; margin: 0 0 16px 0; }
        .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: var(--text-muted); }
        .summary-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: var(--text-main); margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--border); margin-bottom: 24px; }
        
        .toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; z-index: 1000; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .success-screen { display: none; position: fixed; inset: 0; background: white; z-index: 200; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px; }
        .success-icon { width: 80px; height: 80px; background: #dcfce7; color: #166534; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
        .success-icon svg { width: 40px; height: 40px; }
    </style>
</head>
<body x-data="customerOrder()">

    <div class="header">
        <h1>Pesan dari Meja</h1>
        <p>Anda berada di <b>{{ $meja->name }}</b></p>
    </div>

    <div class="container">
        <div class="customer-card">
            <div class="input-group">
                <label>Nomor / Nama Meja</label>
                <input type="text" class="input-control input-readonly" value="{{ $meja->name }}" readonly>
            </div>
            <div class="input-group">
                <label>Nama Pemesan (Wajib)</label>
                <input type="text" x-model="customerName" class="input-control" placeholder="Masukkan nama Anda..." required>
            </div>
            <div class="input-group" style="margin-bottom: 0;">
                <label>No. HP / WhatsApp (Opsional)</label>
                <input type="text" x-model="customerPhone" class="input-control" placeholder="Contoh: 08123456789">
            </div>
        </div>

        <div class="categories">
            <div class="category-pill" :class="activeCategory === 'Semua' ? 'active' : ''" @click="activeCategory = 'Semua'">Semua</div>
            <div class="category-pill" :class="activeCategory === 'Minuman' ? 'active' : ''" @click="activeCategory = 'Minuman'">Minuman</div>
            <div class="category-pill" :class="activeCategory === 'Makanan' ? 'active' : ''" @click="activeCategory = 'Makanan'">Makanan</div>
            <div class="category-pill" :class="activeCategory === 'Snack' ? 'active' : ''" @click="activeCategory = 'Snack'">Snack</div>
        </div>

        <div class="product-list">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="product-card">
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                        <template x-if="product.image">
                            <img :src="`/storage/` + product.image" alt="Product Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                        </template>
                        <template x-if="!product.image">
                            <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 10px; text-align: center; border: 1px solid var(--border);">No Image</div>
                        </template>
                        <div class="product-info">
                            <div class="product-name" x-text="product.name"></div>
                            <div class="product-price" x-text="formatMoney(product.price)"></div>
                        </div>
                    </div>
                    
                    <template x-if="getCartItem(product.id) === null">
                        <button class="btn-add" @click="addToCart(product)">Tambah</button>
                    </template>
                    
                    <template x-if="getCartItem(product.id) !== null">
                        <div class="qty-controls">
                            <button class="qty-btn" @click="updateQty(product.id, -1)">-</button>
                            <div class="qty-number" x-text="getCartItem(product.id).qty"></div>
                            <button class="qty-btn" @click="updateQty(product.id, 1)">+</button>
                        </div>
                    </template>
                </div>
            </template>
            
            <div x-show="filteredProducts.length === 0" style="text-align: center; color: var(--text-muted); padding: 40px;">
                Belum ada produk di kategori ini.
            </div>
        </div>
    </div>

    <!-- Floating Cart -->
    <div class="floating-cart" x-show="cart.length > 0" x-transition>
        <div class="cart-total">
            <span class="cart-total-label"><span x-text="totalItems"></span> Produk</span>
            <span class="cart-total-value" x-text="formatMoney(total)"></span>
        </div>
        <button class="btn-checkout" @click="openCheckout()" :disabled="!customerName.trim()">Pesan Sekarang</button>
    </div>

    <!-- Modal Checkout -->
    <div id="checkoutModal" class="modal-overlay" :style="checkoutModal ? 'display:flex' : 'display:none'">
        <div class="modal-container">
            <h3 class="modal-title">Konfirmasi Pesanan</h3>
            
            <div style="max-height: 200px; overflow-y: auto; margin-bottom: 16px;">
                <template x-for="item in cart" :key="item.id">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <div>
                            <span x-text="item.qty + 'x '"></span>
                            <span font-weight="600" x-text="item.name"></span>
                        </div>
                        <div x-text="formatMoney(item.price * item.qty)"></div>
                    </div>
                </template>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <span x-text="formatMoney(subtotal)"></span>
            </div>
            <div class="summary-row">
                <span>Pajak (11%)</span>
                <span x-text="formatMoney(tax)"></span>
            </div>
            <div class="summary-total">
                <span>Total Bayar</span>
                <span x-text="formatMoney(total)"></span>
            </div>
            
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px; text-align: center;">
                Pesanan akan langsung dikirim ke kasir & dapur. Pembayaran dilakukan di kasir.
            </p>

            <div style="display: flex; gap: 12px;">
                <button @click="checkoutModal = false" style="flex: 1; padding: 12px; background: white; border: 1px solid var(--border); border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                <button @click="submitOrder()" style="flex: 1; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;" :disabled="isSubmitting" x-text="isSubmitting ? 'Memproses...' : 'Kirim Pesanan'"></button>
            </div>
        </div>
    </div>

    <!-- Success Screen -->
    <div id="successScreen" class="success-screen">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 8px; color: var(--text-main);">Pesanan Berhasil!</h2>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 8px;">No. Invoice: <strong style="color: var(--text-main);" x-text="finalInvoice"></strong></p>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 32px; max-width: 300px;">Makanan sedang disiapkan. Silakan nikmati waktu Anda dan lakukan pembayaran di kasir nanti.</p>
        <div style="display: flex; gap: 12px; flex-direction: column; width: 100%; max-width: 300px;">
            <a :href="'/order/track/' + finalInvoice" style="display: block; text-align: center; background: var(--primary); color: white; border: none; border-radius: 8px; padding: 14px 32px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none;">Lacak Pesanan</a>
            <button onclick="window.location.reload()" style="background: white; color: var(--text-main); border: 1px solid var(--border); border-radius: 8px; padding: 14px 32px; font-size: 15px; font-weight: 700; cursor: pointer;">Pesan Lagi</button>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerOrder', () => ({
                products: @json($products),
                hash: '{{ $hash }}',
                cart: [],
                activeCategory: 'Semua',
                customerName: '',
                customerPhone: '',
                checkoutModal: false,
                isSubmitting: false,
                finalInvoice: '',
                
                get filteredProducts() {
                    if (this.activeCategory === 'Semua') return this.products;
                    return this.products.filter(p => p.category === this.activeCategory);
                },

                getCartItem(id) {
                    return this.cart.find(item => item.id === id) || null;
                },
                
                addToCart(product) {
                    this.cart.push({ ...product, qty: 1 });
                },
                
                updateQty(id, change) {
                    const index = this.cart.findIndex(item => item.id === id);
                    if (index > -1) {
                        const newQty = this.cart[index].qty + change;
                        if (newQty <= 0) {
                            this.cart.splice(index, 1);
                        } else {
                            this.cart[index].qty = newQty;
                        }
                    }
                },
                
                get totalItems() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },
                
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                
                get tax() {
                    return this.subtotal * 0.11;
                },
                
                get total() {
                    return this.subtotal + this.tax;
                },
                
                formatMoney(amount) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
                },
                
                openCheckout() {
                    if (!this.customerName.trim()) {
                        this.showToast('Mohon isi Nama Pemesan terlebih dahulu!', '#ef4444');
                        return;
                    }
                    this.checkoutModal = true;
                },
                
                showToast(message, color = '#10b981') {
                    const t = document.getElementById('toast');
                    t.innerText = message;
                    t.style.background = color;
                    t.style.display = 'block';
                    setTimeout(() => t.style.display = 'none', 3000);
                },

                async submitOrder() {
                    if(this.isSubmitting) return;
                    this.isSubmitting = true;
                    
                    const payload = {
                        hash: this.hash,
                        customerName: this.customerName,
                        customerPhone: this.customerPhone,
                        cart: this.cart,
                        subtotal: this.subtotal,
                        tax: this.tax,
                        total: this.total
                    };
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    try {
                        const response = await fetch('{{ route("order.submit") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(payload)
                        });
                        
                        const data = await response.json();
                        
                        if(data.success) {
                            this.finalInvoice = data.invoice;
                            this.checkoutModal = false;
                            document.getElementById('successScreen').style.display = 'flex';
                        } else {
                            this.showToast(data.message || 'Terjadi kesalahan.', '#ef4444');
                            this.isSubmitting = false;
                        }
                    } catch (e) {
                        this.showToast('Gagal terhubung ke server.', '#ef4444');
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>

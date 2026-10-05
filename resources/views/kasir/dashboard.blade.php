@extends('kasir.layouts.app', $data)

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    .pos-container { display: flex; height: calc(100vh - 80px); background: #f3f4f6; }
    
    /* Left Panel: Products */
    .pos-products { flex: 1; padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 24px; }
    
    .category-pills { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; }
    .pill { padding: 8px 16px; background: white; border-radius: 20px; font-size: 14px; font-weight: 500; cursor: pointer; border: 1px solid var(--border-color); color: var(--text-muted); transition: all 0.2s; white-space: nowrap; }
    .pill.active { background: var(--text-main); color: white; border-color: var(--text-main); }
    
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
    .product-card { background: white; border-radius: 12px; overflow: hidden; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid var(--border-color); display: flex; flex-direction: column; }
    .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.05); border-color: var(--text-main); }
    .product-img-placeholder { height: 120px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display: flex; align-items: center; justify-content: center; font-size: 32px; color: #cbd5e1; }
    .product-info { padding: 16px; display: flex; flex-direction: column; flex: 1; justify-content: space-between; }
    .product-name { font-weight: 600; font-size: 14px; color: var(--text-main); margin-bottom: 8px; line-height: 1.4; }
    .product-price { font-weight: 700; color: var(--text-main); font-size: 15px; }
    
    /* Right Panel: Cart */
    .pos-cart { width: 350px; min-width: 300px; background: white; border-left: 1px solid var(--border-color); display: flex; flex-direction: column; box-shadow: -4px 0 16px rgba(0,0,0,0.02); z-index: 10; flex-shrink: 0; }
    .cart-header { padding: 24px; border-bottom: 1px solid var(--border-color); }
    .cart-header h2 { font-size: 20px; font-weight: 700; margin: 0; }
    
    .cart-items { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
    .cart-item { display: flex; gap: 12px; align-items: flex-start; }
    .cart-item-info { flex: 1; }
    .cart-item-name { font-weight: 600; font-size: 14px; color: var(--text-main); margin-bottom: 4px; }
    .cart-item-price { font-size: 13px; color: var(--text-muted); }
    .cart-item-controls { display: flex; align-items: center; gap: 12px; background: var(--bg-color); border-radius: 6px; padding: 4px 8px; }
    .qty-btn { background: none; border: none; font-size: 16px; font-weight: 600; cursor: pointer; color: var(--text-main); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
    .qty-btn:hover { background: #e2e8f0; }
    .item-qty { font-size: 14px; font-weight: 600; min-width: 20px; text-align: center; }
    
    .cart-footer { padding: 24px; border-top: 1px solid var(--border-color); background: #fafafa; }
    .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted); }
    .summary-total { display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: var(--text-main); margin-top: 16px; padding-top: 16px; border-top: 1px dashed #cbd5e1; margin-bottom: 24px; }
    
    .btn-pay { width: 100%; background: var(--text-main); color: white; border: none; border-radius: 8px; padding: 16px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.1s, background 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-pay:hover { background: var(--primary-hover); transform: translateY(-2px); }
    .btn-pay:active { transform: translateY(0); }
    
    .empty-cart { text-align: center; color: #94a3b8; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
    .empty-cart svg { width: 48px; height: 48px; opacity: 0.5; }
    
    .type-btn { flex: 1; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; text-align: center; background: transparent; color: var(--text-muted); }
    .type-btn.active { background: white; color: var(--text-main); box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-color: var(--border-color); }
    
    .promo-section { background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 16px; display: flex; flex-direction: column; gap: 8px; }
    .promo-input-group { display: flex; gap: 8px; }
    .promo-input { flex: 1; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; outline: none; }
    .promo-btn { background: var(--text-main); color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }

    @media (max-width: 1100px) {
        .pos-container { flex-direction: column; height: auto; }
        .pos-products { height: auto; flex: none; }
        .pos-cart { width: 100%; min-width: 100%; border-left: none; border-top: 2px solid var(--border-color); }
    }
    
    .payment-option { flex: 1; padding: 16px; border-radius: 8px; border: 2px solid var(--border-color); text-align: center; cursor: pointer; transition: all 0.2s; color: var(--text-muted); }
    .payment-option.active { border-color: var(--text-main); background: #f3f4f6; color: var(--text-main); }
    
    .status-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; display: block; }
    .status-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; display: block; }
</style>

<div class="pos-container" x-data="posSystem()">
    
    <!-- LEFT PANEL: PRODUCTS -->
    <div class="pos-products">
        <div style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: space-between; align-items: center;">
            <div class="category-pills" style="flex: 1; min-width: 300px; display: flex; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
                <style>.category-pills::-webkit-scrollbar { display: none; }</style>
                <div class="pill" :class="{'active': activeCategory === 'Semua'}" @click="activeCategory = 'Semua'">Semua</div>
                <div class="pill" :class="{'active': activeCategory === 'Minuman'}" @click="activeCategory = 'Minuman'">Minuman</div>
                <div class="pill" :class="{'active': activeCategory === 'Makanan'}" @click="activeCategory = 'Makanan'">Makanan</div>
                <div class="pill" :class="{'active': activeCategory === 'Snack'}" @click="activeCategory = 'Snack'">Snack</div>
                <div class="pill" :class="{'active': activeCategory === 'Lainnya'}" @click="activeCategory = 'Lainnya'">Lainnya</div>
            </div>
            
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                @if(isset($data['kedai']) && $data['kedai']->is_qr_absen_enabled)
                <button @click="showAbsenModal = true; startQrScanner()" style="font-size: 13px; font-weight: 600; color: white; background: var(--text-main); padding: 10px 16px; border-radius: 20px; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Absen QR
                </button>
                @endif
                <div>
                    <button @click="openPendingOrdersModal()" style="font-size: 13px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 10px 16px; border-radius: 20px; border: 1.5px solid #fcd34d; white-space: nowrap; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Belum Bayar (<span x-text="totalPendingCount"></span>)
                    </button>
                </div>
                <div style="position: relative; flex: 1; min-width: 200px;">
                    <input type="text" x-model="searchQuery" placeholder="Cari menu..." style="width: 100%; padding: 10px 16px 10px 36px; border-radius: 20px; border: 1px solid var(--border-color); font-size: 14px; outline: none; box-sizing: border-box;">
                    <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="product-grid">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="product-card" @click="addToCart(product)" style="padding: 20px 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; min-height: 100px;">
                    <div class="product-name" x-text="product.name" style="font-size: 14px; margin-bottom: 8px; line-height: 1.3;"></div>
                    <div class="product-price" x-text="formatMoney(product.price)" style="color: var(--text-muted); font-size: 14px; font-weight: 600;"></div>
                </div>
            </template>
            
            <div x-show="filteredProducts.length === 0" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                Tidak ada produk yang ditemukan.
            </div>
        </div>
    </div>
    
    <!-- RIGHT PANEL: CART -->
    <div class="pos-cart">
        <div class="cart-header" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 18px; margin: 0; font-weight: 700;">Info Pesanan</h2>
                <div style="font-size: 13px; color: var(--text-muted); font-family: monospace; font-weight: 600;">#INV-<span x-text="invoiceNumber"></span></div>
            </div>
            
            <!-- Order Type -->
            <div style="display: flex; background: #f1f5f9; border-radius: 8px; padding: 4px; gap: 4px;">
                <div class="type-btn" :class="{'active': orderType === 'dine_in'}" @click="orderType = 'dine_in'">Makan Sini</div>
                <div class="type-btn" :class="{'active': orderType === 'take_away'}" @click="orderType = 'take_away'">Bungkus (Take Away)</div>
            </div>
            
            <!-- Customer Info -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <input type="text" x-model="customerName" placeholder="Nama Pelanggan (Wajib)" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box;">
                <input type="text" x-model="customerPhone" placeholder="No. HP (Opsional)" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box;">
            </div>
        </div>
        
        <div class="cart-items">
            <template x-if="cart.length === 0">
                <div class="empty-cart">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <div>Keranjang belanja kosong</div>
                </div>
            </template>
            
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name" x-text="item.name"></div>
                        <div class="cart-item-price" x-text="formatMoney(item.price)"></div>
                    </div>
                    <div class="cart-item-controls">
                        <button class="qty-btn" @click="updateQty(index, -1)">-</button>
                        <div class="item-qty" x-text="item.qty"></div>
                        <button class="qty-btn" @click="updateQty(index, 1)">+</button>
                    </div>
                    <div style="font-weight: 600; font-size: 14px; margin-left: 8px; width: 80px; text-align: right;" x-text="formatMoney(item.price * item.qty)"></div>
                </div>
            </template>
        </div>
        
        <div class="cart-footer">
            <!-- Promo / Voucher Section -->
            <div class="promo-section" style="margin-bottom: 12px;" x-show="cart.length > 0">
                <!-- When Voucher Applied -->
                <div x-show="discountAmount > 0" style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 16px;">🎟️</span>
                        <div>
                            <div style="font-weight: 700; font-size: 13px; color: #065f46;" x-text="promoInput || 'Voucher Aktif'"></div>
                            <div style="font-size: 11px; color: #047857;" x-text="'Potongan ' + formatMoney(discountAmount)"></div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" @click="voucherPickerModal = true" style="background: none; border: none; font-size: 11px; font-weight: 700; color: #059669; cursor: pointer; padding: 2px 4px;">Ganti</button>
                        <button type="button" @click="removePromo()" style="background: #fee2e2; border: none; font-size: 11px; font-weight: 700; color: #dc2626; border-radius: 4px; padding: 2px 6px; cursor: pointer;">&times;</button>
                    </div>
                </div>

                <!-- When No Voucher Applied (1-Click Picker) -->
                <div x-show="discountAmount === 0">
                    <button type="button" @click="voucherPickerModal = true" style="width: 100%; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 9px 12px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                        <div style="display: flex; align-items: center; gap: 8px; text-align: left;">
                            <span style="font-size: 16px;">🎟️</span>
                            <div>
                                <div style="font-size: 12px; font-weight: 700; color: #92400e;">Pilih Voucher Promo / Diskon</div>
                                <div style="font-size: 10px; color: #b45309;" x-text="availableVouchers.length + ' voucher tersedia - Klik untuk memilih' "></div>
                            </div>
                        </div>
                        <span style="font-size: 11px; font-weight: 700; background: #d97706; color: white; padding: 3px 8px; border-radius: 6px;">Pilih &gt;</span>
                    </button>
                </div>
            </div>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span style="font-weight: 500; color: var(--text-main);" x-text="formatMoney(subtotal)"></span>
            </div>
            
            <div class="summary-row" x-show="discountAmount > 0">
                <span style="color: #ef4444;">Diskon</span>
                <span style="font-weight: 600; color: #ef4444;" x-text="'-' + formatMoney(discountAmount)"></span>
            </div>
            
            <div class="summary-row" style="align-items: center;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" x-model="useTax" style="width: 16px; height: 16px; accent-color: var(--text-main);">
                    <span>Pajak (<span x-text="taxPercentage"></span>%)</span>
                </label>
                <span style="font-weight: 500; color: var(--text-main);" x-text="formatMoney(tax)"></span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span x-text="formatMoney(total)"></span>
            </div>
            
            <div style="display: flex; gap: 6px;">
                <button type="button" @click="saveDraft()" class="btn-pay" style="flex: 1; background: white; color: #475569; border: 1px solid var(--border-color); font-size: 11px; padding: 10px 2px;" :style="cart.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''" :disabled="cart.length === 0" title="Tahan Pesanan (Draft Lokal Kasir)">
                    📝 Draft
                </button>
                <button type="button" @click="saveBayarNanti()" class="btn-pay" style="flex: 1.2; background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; font-size: 11px; padding: 10px 2px;" :style="cart.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''" :disabled="cart.length === 0" title="Pesanan Bayar Nanti (Open Tab) - Tanpa Cetak Struk">
                    🍽️ Bayar Nanti
                </button>
                <button type="button" class="btn-pay" style="flex: 1.5; font-size: 13px;" :style="cart.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''" :disabled="cart.length === 0" @click="openCheckout()">
                    💳 Bayar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Draft button is now at the top search bar area -->
    
    <!-- Checkout Modal -->
    <div x-show="checkoutModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center;" x-cloak>
        <div style="background: white; width: 500px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 20px;">Selesaikan Pembayaran</h3>
                <button @click="checkoutModal = false" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>
            
            <div style="padding: 24px; background: #f8fafc; border-bottom: 1px solid var(--border-color); text-align: center;">
                <div style="font-size: 14px; color: var(--text-muted);">Total Tagihan</div>
                <div style="font-size: 32px; font-weight: 800; color: var(--text-main); margin-top: 4px;" x-text="formatMoney(total)"></div>
            </div>
            
            <div style="padding: 24px;">
                <div style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">Metode Pembayaran</div>
                <div style="display: flex; gap: 12px; margin-bottom: 24px;">
                    <div @click="paymentMethod = 'cash'" class="payment-option" :class="paymentMethod === 'cash' ? 'active' : ''">
                        <div style="font-weight: 600;">Cash / Tunai</div>
                    </div>
                    <div @click="paymentMethod = 'qris'" class="payment-option" :class="paymentMethod === 'qris' ? 'active' : ''">
                        <div style="font-weight: 600;">QRIS / E-Wallet</div>
                    </div>
                    <div @click="paymentMethod = 'transfer'" class="payment-option" :class="paymentMethod === 'transfer' ? 'active' : ''">
                        <div style="font-weight: 600;">Transfer Bank</div>
                    </div>
                </div>
                
                <div x-show="paymentMethod === 'cash'">
                    <div style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Uang Diterima (Rp)</div>
                    <input type="number" x-model="amountPaid" style="width: 100%; padding: 16px; font-size: 18px; font-weight: 600; border: 1px solid var(--border-color); border-radius: 8px; outline: none; box-sizing: border-box;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding: 16px; background: #dcfce7; border-radius: 8px;" x-show="amountPaid >= total">
                        <span style="color: #166534; font-weight: 600;">Kembalian:</span>
                        <span style="color: #166534; font-weight: 800; font-size: 18px;" x-text="formatMoney(amountPaid - total)"></span>
                    </div>
                </div>
                
                <div x-show="paymentMethod !== 'cash'" style="padding: 16px; background: #eff6ff; border-radius: 8px; color: #1e3a8a; text-align: center; font-size: 14px;">
                    Pastikan pembayaran melalui QRIS/Transfer sudah berhasil diterima sebelum mencetak struk. (Integrasi Payment Gateway siap disambungkan)
                </div>
            </div>
            
            <div style="padding: 24px; border-top: 1px solid var(--border-color); display: flex; gap: 12px;">
                <button @click="processPayment()" class="btn-pay" :disabled="paymentMethod === 'cash' && amountPaid < total" :style="(paymentMethod === 'cash' && amountPaid < total) ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                    Proses & Cetak Struk
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal Pesanan Belum Bayar (Tagihan Meja & Draft Terpadu) -->
    <div x-show="pendingOrdersModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 150; display: flex; align-items: center; justify-content: center;" x-cloak>
        <div style="background: white; width: 680px; max-width: 95vw; max-height: 85vh; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);">
            <!-- Header Modal -->
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                <div>
                    <div style="font-size: 18px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>📋 Pesanan Belum Bayar</span>
                        <span style="font-size: 11px; background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; font-weight: 700;" x-text="totalPendingCount + ' Menunggu'"></span>
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Tagihan aktif: Pesanan QR Meja, Bayar di Akhir (Open Tab), & Draft Kasir</div>
                </div>
                <button @click="pendingOrdersModal = false" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
            </div>

            <!-- Filter & Search Bar (Terpisah: Pesan Meja, Bayar Nanti, & Draft) -->
            <div style="padding: 14px 24px; border-bottom: 1px solid var(--border-color); display: flex; gap: 12px; flex-wrap: wrap; align-items: center; background: white;">
                <div style="display: flex; gap: 6px;">
                    <button class="pill" :class="{'active': pendingFilter === 'meja_qr'}" @click="pendingFilter = 'meja_qr'" style="font-size: 12px; padding: 6px 12px;" :style="pendingFilter === 'meja_qr' ? 'background: #ede9fe; color: #6d28d9; border: 1px solid #c4b5fd;' : ''">
                        📱 Pesan Meja (<span x-text="mejaQrOrdersCount"></span>)
                    </button>
                    <button class="pill" :class="{'active': pendingFilter === 'bayar_nanti'}" @click="pendingFilter = 'bayar_nanti'" style="font-size: 12px; padding: 6px 12px;" :style="pendingFilter === 'bayar_nanti' ? 'background: #fef3c7; color: #b45309; border: 1px solid #fcd34d;' : ''">
                        🍽️ Bayar Nanti (<span x-text="bayarNantiOrdersCount"></span>)
                    </button>
                    <button class="pill" :class="{'active': pendingFilter === 'draft'}" @click="pendingFilter = 'draft'" style="font-size: 12px; padding: 6px 12px;" :style="pendingFilter === 'draft' ? 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;' : ''">
                        📝 Draft Kasir (<span x-text="drafts.length"></span>)
                    </button>
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <input type="text" x-model="pendingSearch" placeholder="Cari Nama / No Meja..." style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; outline: none;">
                </div>
                <button @click="fetchPendingTableOrders()" style="background: #f1f5f9; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 4px; font-weight: 500;">
                    🔄 Refresh
                </button>
            </div>

            <!-- List Orders -->
            <div style="flex: 1; overflow-y: auto; padding: 16px 24px; display: flex; flex-direction: column; gap: 12px;">
                <template x-if="filteredPendingList.length === 0">
                    <div style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                        <div style="font-size: 32px; margin-bottom: 8px;">✅</div>
                        <div style="font-size: 15px; font-weight: 600; color: #334155;">Tidak Ada Pesanan Menunggu</div>
                        <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Semua pesanan meja telah selesai dibayar atau belum ada draft baru.</div>
                    </div>
                </template>

                <template x-for="item in filteredPendingList" :key="item.uniqueId">
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 10px; background: white; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'" onmouseout="this.style.boxShadow='none'">
                        <!-- Header Bar Item -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 700; font-size: 12px; padding: 3px 8px; border-radius: 6px;"
                                      :style="item.isTableOrder ? 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;'"
                                      x-text="item.tableNumber"></span>
                                <span style="font-size: 11px; padding: 2px 6px; border-radius: 4px; font-weight: 600; background: #f8fafc; color: #64748b;" x-text="item.typeLabel"></span>
                            </div>
                            <span style="font-size: 12px; color: #94a3b8;" x-text="item.time"></span>
                        </div>

                        <!-- Info Pelanggan & Invoice -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 700; font-size: 15px; color: #1e293b;" x-text="item.customerName"></div>
                            <div style="font-size: 12px; color: #64748b; font-family: monospace;" x-text="'#' + item.invoiceNumber"></div>
                        </div>

                        <!-- Ringkasan Items -->
                        <div style="background: #f8fafc; border-radius: 8px; padding: 8px 12px; font-size: 12px; color: #475569;">
                            <template x-for="ci in item.cart" :key="ci.name">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span x-text="ci.qty + 'x ' + ci.name"></span>
                                    <span style="font-weight: 600;" x-text="formatMoney(ci.price * ci.qty)"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Tagihan & Action Buttons -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                            <div>
                                <div style="font-size: 11px; color: #94a3b8;">Total Tagihan</div>
                                <div style="font-size: 17px; font-weight: 800; color: #0f172a;" x-text="formatMoney(item.total)"></div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" @click="loadPendingToCart(item)" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: white; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    ✏️ Buka di Kasir
                                </button>
                                <button type="button" @click="directPayPending(item)" style="padding: 8px 16px; border-radius: 6px; border: none; background: #10b981; color: white; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                                    💳 Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
    <!-- Modal Pilih Voucher Promo (1-Klik Tanpa Ketik) -->
    <div x-show="voucherPickerModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 160; display: flex; align-items: center; justify-content: center;" x-cloak>
        <div style="background: white; width: 560px; max-width: 95vw; max-height: 85vh; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);">
            <!-- Header Modal -->
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="background: #fef3c7; color: #d97706; padding: 8px; border-radius: 10px; font-size: 20px;">🎟️</div>
                    <div>
                        <div style="font-size: 17px; font-weight: 700; color: #1e293b;">Pilih Voucher Promo</div>
                        <div style="font-size: 12px; color: #64748b;" x-text="availableVouchers.length + ' voucher promo aktif tersedia'"></div>
                    </div>
                </div>
                <button @click="voucherPickerModal = false" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
            </div>

            <!-- Manual Search / Input fallback -->
            <div style="padding: 14px 24px; border-bottom: 1px solid var(--border-color); background: #f8fafc;">
                <div style="display: flex; gap: 8px;">
                    <input type="text" x-model="promoInput" placeholder="Punya kode lain? Masukkan di sini..." style="flex: 1; padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-transform: uppercase; font-weight: 600; outline: none;" @keydown.enter="applyPromo(); voucherPickerModal = false;">
                    <button type="button" @click="applyPromo(); voucherPickerModal = false;" style="background: #212121; color: white; border: none; padding: 0 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">Terapkan</button>
                </div>
            </div>

            <!-- Voucher List -->
            <div style="flex: 1; overflow-y: auto; padding: 16px 24px; display: flex; flex-direction: column; gap: 10px;">
                <template x-if="availableVouchers.length === 0">
                    <div style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                        <div style="font-size: 32px; margin-bottom: 8px;">🎟️</div>
                        <div style="font-size: 15px; font-weight: 600; color: #334155;">Belum Ada Voucher Aktif</div>
                    </div>
                </template>

                <template x-for="v in availableVouchers" :key="v.id">
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; display: flex; overflow: hidden; background: white; transition: all 0.2s;" :style="promoInput === v.kode ? 'border-color: #22c55e; background: #f0fdf4;' : ''">
                        <!-- Left Badge -->
                        <div style="width: 90px; background: #d97706; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 6px; text-align: center;" :style="promoInput === v.kode ? 'background: #16a34a;' : ''">
                            <span style="font-size: 16px; font-weight: 900;" x-text="v.tipe_diskon === 'persen' ? v.nilai_diskon + '%' : 'Rp ' + (v.nilai_diskon >= 1000 ? (v.nilai_diskon/1000) + 'k' : v.nilai_diskon)"></span>
                            <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9;">DISKON</span>
                        </div>

                        <!-- Right Info -->
                        <div style="flex: 1; padding: 12px 16px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-weight: 700; font-size: 14px; color: #0f172a; letter-spacing: 0.5px;" x-text="v.kode"></span>
                                    <span x-show="promoInput === v.kode" style="font-size: 10px; font-weight: 700; color: #166534; background: #dcfce7; padding: 2px 8px; border-radius: 10px;">✓ Terpasang</span>
                                </div>
                                <div style="font-size: 12px; color: #475569; margin-top: 2px;" x-text="v.nama"></div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 4px;" x-text="'Min. Belanja: ' + formatMoney(v.minimal_belanja || 0)"></div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div style="display: flex; align-items: center; padding-right: 16px;">
                            <button type="button" @click="selectVoucher(v)" style="padding: 8px 14px; border-radius: 8px; border: none; font-size: 12px; font-weight: 700; cursor: pointer; color: white;" :style="promoInput === v.kode ? 'background: #16a34a;' : 'background: #212121;'" x-text="promoInput === v.kode ? 'Ganti' : 'Pakai'"></button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    <!-- Absen QR Modal -->
    <div x-show="showAbsenModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center;" x-cloak>
        <div style="background: white; width: 400px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px;">Absensi QR (Karyawan)</h3>
                <button @click="closeAbsenModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>
            <div style="padding: 24px; text-align: center;">
                <div id="pos-reader" style="width: 100%; max-width: 300px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px solid var(--border-color);"></div>
                <div x-show="absenStatusText" :class="absenStatusClass" style="margin-top: 16px; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600;" x-text="absenStatusText"></div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); color: white; min-width: 300px; font-weight: 500;"
         :style="toastType === 'success' ? 'background-color: #10b981;' : 'background-color: #ef4444;'"
         x-cloak>
        
        <svg x-show="toastType === 'success'" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <svg x-show="toastType === 'error'" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        
        <div x-text="toastMessage" style="flex: 1; font-size: 14px;"></div>
        
        <button @click="showToast = false" style="background: none; border: none; color: white; opacity: 0.7; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posSystem', () => ({
            products: @json($data['products'] ?? []),
            cart: [],
            activeCategory: 'Semua',
            searchQuery: '',
            
            // Customer Info
            orderType: 'dine_in',
            customerName: '',
            customerPhone: '',
            invoiceNumber: (function() {
                const d = new Date();
                const ymd = String(d.getFullYear()).slice(-2) + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
                const rand = Math.floor(Math.random() * 9000) + 1000;
                return ymd + rand;
            })(),
            
            // Pesanan Belum Bayar (Meja QR, Bayar Nanti, & Draft)
            drafts: [],
            tableOrders: [],
            pendingOrdersModal: false,
            pendingFilter: 'meja_qr',
            pendingSearch: '',
            checkoutModal: false,
            voucherPickerModal: false,
            availableVouchers: @json($data['vouchers'] ?? []),
            paymentMethod: 'cash',
            amountPaid: 0,
            
            // Tax & Kedai Config
            taxPercentage: {{ isset($data['kedai']) && $data['kedai']->tax_percentage ? (float)$data['kedai']->tax_percentage : 11 }},
            useTax: {{ isset($data['kedai']) && $data['kedai']->is_tax_enabled ? 'true' : 'false' }},
            promoInput: '',
            discountAmount: 0,
            appliedVoucherId: null,

            get mejaQrOrders() {
                return this.tableOrders.filter(o => o.paymentMethod !== 'bayar_nanti');
            },
            get bayarNantiOrders() {
                return this.tableOrders.filter(o => o.paymentMethod === 'bayar_nanti');
            },
            get mejaQrOrdersCount() {
                return this.mejaQrOrders.length;
            },
            get bayarNantiOrdersCount() {
                return this.bayarNantiOrders.length;
            },
            get totalPendingCount() {
                return this.tableOrders.length + this.drafts.length;
            },
            get allPendingItems() {
                const list = [];
                // 1. Pesan dari Meja (QR Meja Online)
                this.mejaQrOrders.forEach(o => {
                    const tbl = o.orderType === 'dine_in' ? (o.tableNumber || 'Meja') : 'Takeaway';
                    list.push({
                        uniqueId: 'meja-' + o.invoiceNumber,
                        invoiceNumber: o.invoiceNumber,
                        customerName: o.customerName || 'Pelanggan Meja',
                        customerPhone: o.customerPhone || '',
                        orderType: o.orderType || 'dine_in',
                        tableNumber: tbl,
                        typeLabel: '📱 QR Meja',
                        category: 'meja_qr',
                        isTableOrder: true,
                        cart: (o.items || []).map(i => ({
                            id: i.productId || i.id,
                            name: i.name,
                            price: i.price,
                            qty: i.quantity || i.qty || 1
                        })),
                        total: o.total || 0,
                        time: o.createdAt ? new Date(o.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
                    });
                });
                // 2. Bayar Nanti (Open Tab Kasir)
                this.bayarNantiOrders.forEach(o => {
                    const tbl = o.orderType === 'dine_in' ? (o.tableNumber || 'Meja') : 'Takeaway';
                    list.push({
                        uniqueId: 'bn-' + o.invoiceNumber,
                        invoiceNumber: o.invoiceNumber,
                        customerName: o.customerName || 'Pelanggan Kasir',
                        customerPhone: o.customerPhone || '',
                        orderType: o.orderType || 'dine_in',
                        tableNumber: tbl,
                        typeLabel: '🍽️ Bayar Nanti',
                        category: 'bayar_nanti',
                        isTableOrder: true,
                        cart: (o.items || []).map(i => ({
                            id: i.productId || i.id,
                            name: i.name,
                            price: i.price,
                            qty: i.quantity || i.qty || 1
                        })),
                        total: o.total || 0,
                        time: o.createdAt ? new Date(o.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'
                    });
                });
                // 3. Drafts Kasir Lokal
                this.drafts.forEach((d, idx) => {
                    list.push({
                        uniqueId: 'draft-' + (d.id || idx),
                        draftIndex: idx,
                        invoiceNumber: d.invoiceNumber,
                        customerName: d.customerName,
                        customerPhone: d.customerPhone || '',
                        orderType: d.orderType,
                        tableNumber: d.tableNumber || 'Draft Kasir',
                        typeLabel: '📝 Draft Kasir',
                        category: 'draft',
                        isTableOrder: false,
                        cart: d.cart,
                        total: d.total,
                        time: d.time || '-'
                    });
                });
                return list;
            },
            get filteredPendingList() {
                return this.allPendingItems.filter(item => {
                    if (this.pendingFilter !== item.category) return false;
                    if (this.pendingSearch.trim()) {
                        const q = this.pendingSearch.toLowerCase();
                        const matchName = (item.customerName || '').toLowerCase().includes(q);
                        const matchTable = (item.tableNumber || '').toLowerCase().includes(q);
                        const matchInv = (item.invoiceNumber || '').toLowerCase().includes(q);
                        return matchName || matchTable || matchInv;
                    }
                    return true;
                });
            },
            
            init() {
                this.fetchPendingTableOrders();
                setInterval(() => {
                    this.fetchPendingTableOrders();
                }, 10000);
            },

            openPendingOrdersModal() {
                this.pendingOrdersModal = true;
                this.fetchPendingTableOrders();
            },

            async fetchPendingTableOrders() {
                try {
                    const res = await fetch('{{ route("api.orders.pending") }}');
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.tableOrders = json.data || [];
                    }
                } catch(e) {
                    console.log('Error fetching pending table orders:', e);
                }
            },

            loadPendingToCart(item) {
                if (this.cart.length > 0) {
                    if (!confirm('Ada item di keranjang yang belum selesai. Ganti dengan pesanan ini?')) return;
                }
                this.customerName = item.customerName;
                this.customerPhone = item.customerPhone;
                this.orderType = item.orderType;
                this.cart = JSON.parse(JSON.stringify(item.cart));
                this.invoiceNumber = item.invoiceNumber;
                if (!item.isTableOrder && item.draftIndex !== undefined) {
                    this.drafts.splice(item.draftIndex, 1);
                }
                this.pendingOrdersModal = false;
                this.showNotification('Pesanan berhasil dimuat ke keranjang kasir.', 'success');
            },

            directPayPending(item) {
                this.customerName = item.customerName;
                this.customerPhone = item.customerPhone;
                this.orderType = item.orderType;
                this.cart = JSON.parse(JSON.stringify(item.cart));
                this.invoiceNumber = item.invoiceNumber;
                if (!item.isTableOrder && item.draftIndex !== undefined) {
                    this.drafts.splice(item.draftIndex, 1);
                }
                this.pendingOrdersModal = false;
                this.openCheckout();
            },
            
            // Absen QR
            showAbsenModal: false,
            absenStatusText: '',
            absenStatusClass: '',
            posScanner: null,
            isScanningAbsen: false,
            
            startQrScanner() {
                if (this.posScanner) return; // already started
                setTimeout(() => {
                    this.posScanner = new Html5QrcodeScanner("pos-reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
                    this.posScanner.render((decodedText) => this.handleScanSuccess(decodedText), (err) => {});
                }, 200);
            },
            
            closeAbsenModal() {
                this.showAbsenModal = false;
                if (this.posScanner) {
                    this.posScanner.clear();
                    this.posScanner = null;
                }
                this.absenStatusText = '';
            },
            
            async handleScanSuccess(decodedText) {
                if (this.isScanningAbsen) return;
                this.isScanningAbsen = true;
                this.absenStatusText = 'Memproses...';
                this.absenStatusClass = '';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                try {
                    const res = await fetch('{{ route("admin.staff.absensi.scan") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ qr_code: decodedText })
                    });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        this.absenStatusText = data.message;
                        this.absenStatusClass = 'status-success';
                        this.showNotification(data.message, 'success');
                    } else {
                        this.absenStatusText = data.message;
                        this.absenStatusClass = 'status-error';
                    }
                } catch(e) {
                    this.absenStatusText = 'Terjadi kesalahan jaringan!';
                    this.absenStatusClass = 'status-error';
                }
                
                setTimeout(() => {
                    this.isScanningAbsen = false;
                    this.absenStatusText = '';
                }, 3000);
            },
            
            // Notification System
            toastMessage: '',
            toastType: 'success',
            showToast: false,
            
            showNotification(message, type = 'success') {
                this.toastMessage = message;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                }, 3000);
            },
            
            get filteredProducts() {
                return this.products.filter(p => {
                    const matchesCategory = this.activeCategory === 'Semua' || p.category === this.activeCategory;
                    const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchesCategory && matchesSearch;
                });
            },
            
            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },
            
            get tax() {
                if (!this.useTax) return 0;
                const afterDiscount = Math.max(0, this.subtotal - this.discountAmount);
                return afterDiscount * (this.taxPercentage / 100);
            },
            
            get total() {
                return Math.max(0, this.subtotal - this.discountAmount) + this.tax;
            },
            
            async applyPromo() {
                let input = this.promoInput.trim();
                if (!input) return;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                try {
                    const response = await fetch('{{ route("kasir.cek_voucher") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ kode: input, subtotal: this.subtotal })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.discountAmount = data.discount_amount;
                        this.appliedVoucherId = data.voucher_id;
                        this.showNotification(data.message, 'success');
                    } else {
                        this.showNotification(data.message, 'error');
                        this.discountAmount = 0;
                        this.appliedVoucherId = null;
                    }
                } catch (error) {
                    this.showNotification('Terjadi kesalahan saat mengecek voucher.', 'error');
                }
            },
            
            removePromo() {
                this.discountAmount = 0;
                this.promoInput = '';
                this.appliedVoucherId = null;
                this.showNotification('Voucher dihapus', 'success');
            },

            selectVoucher(v) {
                this.promoInput = v.kode;
                this.appliedVoucherId = v.id;
                this.voucherPickerModal = false;
                this.applyPromo();
            },

            async saveBayarNanti() {
                if (this.cart.length === 0) {
                    this.showNotification('Keranjang belanja masih kosong.', 'error');
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                const orderData = {
                    invoiceNumber: this.invoiceNumber,
                    customerName: this.customerName || 'Pelanggan Walk-In',
                    customerPhone: this.customerPhone || '',
                    orderType: this.orderType,
                    tableNumber: this.selectedTable || '-',
                    cart: this.cart,
                    subtotal: this.subtotal,
                    discountAmount: this.discountAmount,
                    tax: this.tax,
                    total: this.total,
                    paymentMethod: 'bayar_nanti',
                    amountPaid: 0,
                    voucherId: this.appliedVoucherId,
                };
                
                try {
                    const res = await fetch('{{ route("kasir.simpan_open_tab") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(orderData)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showNotification('Pesanan berhasil disimpan ke Bayar Nanti (tanpa cetak struk).', 'success');
                        this.resetCart();
                        this.fetchPendingTableOrders();
                    } else {
                        this.showNotification(data.message || 'Gagal menyimpan pesanan.', 'error');
                    }
                } catch (e) {
                    this.showNotification('Terjadi kesalahan koneksi server.', 'error');
                }
            },
            
            addToCart(product) {
                const existing = this.cart.find(item => item.id === product.id);
                if (existing) {
                    existing.qty++;
                } else {
                    this.cart.push({ ...product, qty: 1 });
                }
            },
            
            updateQty(index, change) {
                const newQty = this.cart[index].qty + change;
                if (newQty <= 0) {
                    this.cart.splice(index, 1);
                } else {
                    this.cart[index].qty = newQty;
                }
            },
            
            formatMoney(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
            },
            
            saveDraft() {
                if(!this.customerName.trim()) {
                    this.showNotification('Mohon masukkan Nama Pelanggan sebelum menahan pesanan.', 'error');
                    return;
                }
                
                this.drafts.push({
                    id: Date.now(),
                    invoiceNumber: this.invoiceNumber,
                    customerName: this.customerName,
                    customerPhone: this.customerPhone,
                    orderType: this.orderType,
                    cart: JSON.parse(JSON.stringify(this.cart)), // clone array
                    total: this.total,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                });
                
                this.resetCart();
                this.showNotification('Pesanan berhasil di-hold (Draft).', 'success');
            },
            
            loadDraft(index) {
                if(this.cart.length > 0) {
                    if(!confirm('Ada pesanan yang belum diselesaikan. Yakin ingin mengganti dengan draft ini?')) return;
                }
                
                const draft = this.drafts[index];
                this.customerName = draft.customerName;
                this.customerPhone = draft.customerPhone;
                this.orderType = draft.orderType;
                this.cart = draft.cart;
                this.invoiceNumber = draft.invoiceNumber;
                
                this.drafts.splice(index, 1); // remove from draft
                this.showNotification('Draft berhasil dimuat.', 'success');
            },
            
            resetCart() {
                this.cart = [];
                this.customerName = '';
                this.customerPhone = '';
                this.orderType = 'dine_in';
                const d = new Date();
                const ymd = String(d.getFullYear()).slice(-2) + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
                const rand = Math.floor(Math.random() * 9000) + 1000;
                this.invoiceNumber = ymd + rand;
                this.amountPaid = 0;
                this.discountAmount = 0;
                this.promoInput = '';
                this.useTax = true;
                this.appliedVoucherId = null;
            },
            
            openCheckout() {
                if(!this.customerName.trim()) {
                    this.showNotification('Mohon masukkan Nama Pelanggan terlebih dahulu.', 'error');
                    return;
                }
                this.amountPaid = this.total; // Default to exact amount
                this.checkoutModal = true;
            },
            
            processPayment() {
                if(this.paymentMethod === 'cash' && this.amountPaid < this.total) return;
                
                this.checkoutModal = false;
                
                const payload = {
                    invoiceNumber: this.invoiceNumber,
                    customerName: this.customerName,
                    customerPhone: this.customerPhone,
                    orderType: this.orderType,
                    cart: this.cart,
                    subtotal: this.subtotal,
                    discountAmount: this.discountAmount,
                    voucherId: this.appliedVoucherId,
                    tax: this.tax,
                    total: this.total,
                    paymentMethod: this.paymentMethod,
                    amountPaid: this.amountPaid
                };
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                // Simpan ke database via AJAX
                fetch('{{ route("kasir.cetak_struk") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ data: JSON.stringify(payload) })
                })
                .then(res => res.text())
                .then(html => {
                    if (window.thermalPrinter && window.thermalPrinter.isConnected()) {
                        // Print directly to connected Web Bluetooth thermal printer
                        window.thermalPrinter.printReceipt(payload)
                            .then(() => console.log("Printed via Web Bluetooth"))
                            .catch(err => console.error("WebBT Print error:", err));
                    } else {
                        // Fallback: Cetak menggunakan iframe tersembunyi (RawBT / System Print)
                        const oldIframe = document.getElementById('print-iframe');
                        if (oldIframe) oldIframe.remove();
                        
                        const iframe = document.createElement('iframe');
                        iframe.id = 'print-iframe';
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        
                        iframe.contentDocument.open();
                        iframe.contentDocument.write(html);
                        iframe.contentDocument.close();
                        
                        setTimeout(() => {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        }, 500);
                    }
                });
                
                this.resetCart();
                this.showNotification('Pembayaran berhasil!', 'success');
            }
        }));
    });
</script>
@endsection

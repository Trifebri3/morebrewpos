@extends(request()->routeIs('kasir.*') ? 'kasir.layouts.app' : 'admin.layouts.app', $data ?? [])

@section('content')
<!-- Leaflet CSS & JS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .settings-container {
        padding: 32px 40px 60px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-banner h1 {
        font-size: 26px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .header-banner p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 28px;
    }

    .card-header {
        padding: 20px 28px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header span {
        font-size: 13px;
        color: #64748b;
        font-weight: normal;
    }

    .card-body {
        padding: 28px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group .helper-text {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
        line-height: 1.4;
    }

    .input-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .input-control:focus {
        border-color: #212121;
        box-shadow: 0 0 0 3px rgba(33, 33, 33, 0.1);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .grid-2, .grid-3 {
            grid-template-columns: 1fr;
        }
        .settings-container {
            padding: 20px 16px 40px;
        }
    }

    /* Logo Uploader & Preview */
    .logo-uploader-box {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 18px 24px;
        background: #fafafa;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }

    .logo-preview-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        flex-shrink: 0;
    }

    .logo-preview-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Interactive Map Box */
    #map-preview {
        width: 100%;
        height: 380px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        z-index: 10;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
    }

    .map-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .search-map-box {
        display: flex;
        gap: 8px;
        flex: 1;
        min-width: 260px;
    }

    .btn-action-map {
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .btn-gps {
        background: #10b981;
        color: white;
    }

    .btn-gps:hover {
        background: #059669;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-search {
        background: #334155;
        color: white;
    }

    .btn-search:hover {
        background: #1e293b;
    }

    .badge-coords {
        background: #f1f5f9;
        color: #334155;
        padding: 6px 12px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-submit {
        background: #212121;
        color: #ffffff;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background: #000000;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 24px;
        border: 1px solid #a7f3d0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Tax Preset Buttons */
    .tax-preset-btn {
        padding: 6px 12px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s;
    }
    .tax-preset-btn:hover {
        background: #e2e8f0;
        border-color: #94a3b8;
    }

    /* Simulation Box */
    .tax-simulation-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 18px 24px;
        margin-top: 18px;
    }
</style>

<div class="settings-container">
    <div class="header-banner">
        <div>
            <h1>Pengaturan Kedai, Pajak (PB1) & Struk</h1>
            <p>Kelola tarif dan aktivasi Pajak Restoran (PB1), logo struk thermal, anggaran harian, serta zona koordinat GPS.</p>
        </div>
        <div>
            <button type="button" onclick="document.getElementById('form-pengaturan').submit();" class="btn-submit" style="padding: 10px 20px; font-size: 13.5px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="alert-success">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div style="background: #fef2f2; color: #991b1b; padding: 16px 20px; border-radius: 12px; font-size: 14px; margin-bottom: 24px; border: 1px solid #fecaca;">
        <div style="font-weight: 700; margin-bottom: 6px;">Terdapat kesalahan pada input pengaturan:</div>
        <ul style="padding-left: 20px; margin: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="form-pengaturan" action="{{ request()->routeIs('kasir.*') ? route('kasir.pengaturan.update') : route('admin.kedai.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- CARD 1: PENGATURAN PAJAK RESTORAN (PB1 / TAX) & FINANSIAL -->
        <div class="card" style="border: 2px solid #e0e7ff; box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.08);">
            <div class="card-header" style="background: #eef2ff;">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" x2="5" y1="5" y2="19"/>
                        <circle cx="6.5" cy="6.5" r="2.5"/>
                        <circle cx="17.5" cy="17.5" r="2.5"/>
                    </svg>
                    <span style="color: #312e81; font-weight: 700; font-size: 16px;">Pengaturan Pajak Restoran (PB1 / Tax) & Finansial</span>
                </h2>
                <span style="color: #4f46e5; font-weight: 600; background: #e0e7ff; padding: 4px 12px; border-radius: 20px; font-size: 12px;">Sistem Kalkulasi Otomatis POS</span>
            </div>
            <div class="card-body">
                <!-- Toggle Aktifkan Pajak -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <input type="checkbox" name="is_tax_enabled" id="is_tax_enabled" value="1" {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'checked' : '' }} style="width: 22px; height: 22px; accent-color: #4f46e5; cursor: pointer;">
                        <div>
                            <label for="is_tax_enabled" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; cursor: pointer;">Aktifkan Pajak Transaksi (PB1 / Tax)</label>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Jika dicentang, kasir POS web dan aplikasi mobile akan otomatis menambahkan pajak ke setiap pesanan.</div>
                        </div>
                    </div>
                    <span id="badge-tax-status" style="font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'background: #dcfce7; color: #15803d;' : 'background: #f1f5f9; color: #64748b;' }}">
                        {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? '✓ Pajak Aktif' : 'Pajak Nonaktif' }}
                    </span>
                </div>

                <div class="grid-3">
                    <!-- Persentase Pajak -->
                    <div class="form-group">
                        <label for="tax_percentage">
                            Persentase Pajak (%)
                        </label>
                        <div style="position: relative;">
                            <input type="number" step="0.01" min="0" max="100" name="tax_percentage" id="tax_percentage" value="{{ old('tax_percentage', $kedai->tax_percentage ?? 11.00) }}" class="input-control" placeholder="11.00" required style="padding-right: 36px; font-weight: 700; font-size: 15px;">
                            <span style="position: absolute; right: 14px; top: 12px; font-weight: 700; color: #94a3b8;">%</span>
                        </div>
                        <div style="display: flex; gap: 6px; margin-top: 8px;">
                            <button type="button" class="tax-preset-btn" onclick="setTaxPreset(0)">0% (Bebas)</button>
                            <button type="button" class="tax-preset-btn" onclick="setTaxPreset(10)">10% (PB1)</button>
                            <button type="button" class="tax-preset-btn" onclick="setTaxPreset(11)">11% (Standar)</button>
                        </div>
                        <div class="helper-text">Tarif persentase yang dikenakan pada pesanan.</div>
                    </div>

                    <!-- Nama Pajak di Struk -->
                    <div class="form-group">
                        <label for="tax_name">Nama / Label Pajak di Struk</label>
                        <input type="text" name="tax_name" id="tax_name" value="{{ old('tax_name', $kedai->tax_name ?? 'PB1 (Pajak Restoran)') }}" class="input-control" placeholder="Contoh: PB1 (Pajak Restoran)" required>
                        <div class="helper-text">Tercetak pada struk thermal dan invoice (contoh: "PB1", "PPN 11%", "Pajak Restoran").</div>
                    </div>

                    <!-- Target Budget Belanja Harian -->
                    <div class="form-group">
                        <label for="budget_harian">Plafon Anggaran Belanja Harian (Rp)</label>
                        <input type="number" step="1000" min="0" name="budget_harian" id="budget_harian" value="{{ old('budget_harian', $kedai->budget_harian ?? 1000000) }}" class="input-control" placeholder="1000000">
                        <div class="helper-text">Batas maksimal belanja operasional kedai per hari untuk kontrol kasir.</div>
                    </div>
                </div>

                <!-- Live Simulation Preview Box -->
                <div class="tax-simulation-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px;">
                        <span style="font-size: 13px; font-weight: 700; color: #334155; display: flex; align-items: center; gap: 6px;">
                            🧾 Simulasi Real-Time Perhitungan Struk
                        </span>
                        <span style="font-size: 11px; color: #64748b;">(Contoh Pesanan Rp 50.000 + Voucher Rp 5.000)</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; font-size: 13px;">
                        <div>
                            <span style="color: #64748b; display: block; font-size: 11px;">Subtotal Pesanan</span>
                            <strong style="color: #1e293b;">Rp 50.000</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 11px;">Potongan Diskon</span>
                            <strong style="color: #ef4444;">- Rp 5.000</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 11px;">Dasar Pajak (DPP)</span>
                            <strong style="color: #1e293b;">Rp 45.000</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 11px;" id="sim-tax-label">PB1 (11.00%)</span>
                            <strong style="color: #4f46e5;" id="sim-tax-value">+ Rp 4.950</strong>
                        </div>
                        <div style="background: #ffffff; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <span style="color: #64748b; display: block; font-size: 11px;">Total Tagihan Akhir</span>
                            <strong style="color: #0f172a; font-size: 15px;" id="sim-total-value">Rp 49.950</strong>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 14px; font-size: 12px; color: #475569; display: flex; align-items: center; gap: 8px;">
                    <span>💡</span>
                    <span><b>Catatan Rumus:</b> Pajak dihitung secara otomatis dari Subtotal transaksi setelah potongan diskon voucher. Perubahan tarif langsung tersinkronisasi ke antarmuka POS web dan aplikasi kasir mobile Android.</span>
                </div>
            </div>
        </div>

        <!-- CARD 2: PROFIL KEDAI & FORMAT KOP STRUK INVOICE -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Profil Kedai & Tampilan Header/Footer Struk
                </h2>
                <span>Kop struk thermal, PDF invoice, dan identitas toko</span>
            </div>
            <div class="card-body">
                <!-- Logo Uploader -->
                <div class="form-group">
                    <label>Logo Resmi Kedai (Muncul di Struk Thermal, PDF & Website)</label>
                    <div class="logo-uploader-box">
                        <div class="logo-preview-wrapper">
                            <img id="logo-preview" src="{{ asset('logo.png') }}?v={{ time() }}" alt="Logo MoreBrew" onerror="this.src='https://placehold.co/80x80/212121/ffffff?text=Logo'">
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="logo" id="logo-input" accept="image/*" class="input-control" style="padding: 8px 12px; background: white;">
                            <div class="helper-text">Format yang disarankan: PNG transparan (min. 200x200px, max 2MB). Logo ini langsung tampil di kepala struk printer thermal & PDF invoice.</div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="name">Nama Kedai / Toko</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $kedai->name ?? 'MOREBREWW') }}" class="input-control" required placeholder="Contoh: MOREBREWW">
                    </div>
                    <div class="form-group">
                        <label for="phone">No. WhatsApp / Telepon Toko</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $kedai->phone ?? '') }}" class="input-control" placeholder="Contoh: 0812-3456-7890">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap Toko</label>
                    <textarea name="address" id="address" rows="3" class="input-control" placeholder="Contoh: Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261">{{ old('address', $kedai->address ?? 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung, Jawa Barat 40261') }}</textarea>
                    <div class="helper-text">Alamat pada struk thermal otomatis diformat 2 baris seimbang.</div>
                </div>

                <!-- Custom Receipt Header & Footer -->
                <div class="grid-2">
                    <div class="form-group">
                        <label for="receipt_header">Slogan / Catatan Header Struk</label>
                        <input type="text" name="receipt_header" id="receipt_header" value="{{ old('receipt_header', $kedai->receipt_header ?? 'something, between home and everywhere') }}" class="input-control" placeholder="Contoh: something, between home and everywhere">
                        <div class="helper-text">Teks kecil yang tercetak tepat di bawah nama toko pada struk printer.</div>
                    </div>
                    <div class="form-group">
                        <label for="receipt_footer">Ucapan Terima Kasih / Catatan Footer Struk</label>
                        <input type="text" name="receipt_footer" id="receipt_footer" value="{{ old('receipt_footer', $kedai->receipt_footer ?? 'Silakan datang kembali!') }}" class="input-control" placeholder="Contoh: Silakan datang kembali!">
                        <div class="helper-text">Pesan penutup di bagian paling bawah struk belanja pelanggan.</div>
                    </div>
                </div>

                <!-- Wi-Fi & Media Sosial -->
                <div class="grid-3" style="margin-top: 10px;">
                    <div class="form-group">
                        <label for="wifi_ssid">Nama Wi-Fi Pelanggan (SSID)</label>
                        <input type="text" name="wifi_ssid" id="wifi_ssid" value="{{ old('wifi_ssid', $kedai->wifi_ssid ?? 'moreandmore') }}" class="input-control" placeholder="moreandmore">
                    </div>
                    <div class="form-group">
                        <label for="wifi_password">Password Wi-Fi Pelanggan</label>
                        <input type="text" name="wifi_password" id="wifi_password" value="{{ old('wifi_password', $kedai->wifi_password ?? 'bolehlihatsenyumnya?') }}" class="input-control" placeholder="bolehlihatsenyumnya?">
                    </div>
                    <div class="form-group">
                        <label for="instagram">Akun Instagram Toko</label>
                        <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $kedai->instagram ?? '@morebrewcoffee') }}" class="input-control" placeholder="@morebrewcoffee">
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: TITIK KOORDINAT GPS & PETA INTERAKTIF GEOFENCING -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Peta Interaktif Titik Koordinat GPS & Geofencing Absensi
                </h2>
                <span>Geser pin atau klik peta untuk menentukan titik koordinat yang presisi</span>
            </div>
            <div class="card-body">
                <!-- Map Toolbar -->
                <div class="map-toolbar">
                    <div class="search-map-box">
                        <input type="text" id="map-search-input" placeholder="Ketik nama jalan atau lokasi untuk mencari di peta..." class="input-control" style="padding: 9px 14px;">
                        <button type="button" id="btn-search-location" class="btn-action-map btn-search">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Cari
                        </button>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" id="btn-get-current-gps" class="btn-action-map btn-gps">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            📍 Ambil Lokasi GPS Saya Saat Ini
                        </button>
                    </div>
                </div>

                <!-- Leaflet Interactive Map Container -->
                <div id="map-preview"></div>
                
                <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div style="font-size: 12px; color: #64748b;">
                        💡 <b>Tips:</b> Klik pada peta atau seret (drag) penanda biru untuk mengubah posisi titik koordinat kedai secara realtime. Lingkaran biru menandakan batas radius absensi karyawan yang diizinkan.
                    </div>
                    <div class="badge-coords" id="coords-badge">
                        <span>Koordinat Aktif: </span>
                        <strong id="badge-lat-lon">-6.921477, 107.616654</strong>
                    </div>
                </div>

                <!-- GPS Input Form Fields -->
                <div class="grid-3" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <div class="form-group">
                        <label for="latitude">Latitude Kedai</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $kedai->latitude ?? '-6.9214771') }}" class="input-control" placeholder="-6.9214771" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude Kedai</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $kedai->longitude ?? '107.6166542') }}" class="input-control" placeholder="107.6166542" required>
                    </div>
                    <div class="form-group">
                        <label for="radius_meter">Radius Maksimal Absen (Meter)</label>
                        <input type="number" name="radius_meter" id="radius_meter" value="{{ old('radius_meter', $kedai->radius_meter ?? 50) }}" min="5" max="5000" class="input-control" required>
                        <div class="helper-text">Karyawan wajib berada dalam jarak radius ini saat melakukan clock in.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 4: FITUR KASIR & AKSES ABSENSI -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Fitur Absensi & Akses Kasir (POS)
                </h2>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 14px; cursor: pointer;">
                        <input type="checkbox" name="is_qr_absen_enabled" value="1" {{ old('is_qr_absen_enabled', $kedai->is_qr_absen_enabled ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #212121; cursor: pointer;">
                        <div>
                            <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Tampilkan Tombol Absen di Layar POS Kasir</div>
                            <div style="font-size: 12px; color: #64748b;">Memungkinkan barista/karyawan melakukan absensi QR langsung melalui antarmuka kasir.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 14px; cursor: pointer;">
                        <input type="checkbox" name="is_link_absen_enabled" value="1" {{ old('is_link_absen_enabled', $kedai->is_link_absen_enabled ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #212121; cursor: pointer;">
                        <div>
                            <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Izinkan Absensi Karyawan via Link Mandiri (/absen)</div>
                            <div style="font-size: 12px; color: #64748b;">Memungkinkan karyawan membuka halaman web absen mandiri di smartphone masing-masing dengan verifikasi GPS.</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Tombol Submit Bawah -->
        <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
            <button type="submit" class="btn-submit">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Semua Pengaturan
            </button>
        </div>
    </form>
</div>

<!-- Script Interaktif Leaflet Map, Geolocation & Simulasi Pajak Realtime -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Logo Preview Handler
    const logoInput = document.getElementById('logo-input');
    const logoPreview = document.getElementById('logo-preview');
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Real-time Tax Simulation
    const taxEnabledInput = document.getElementById('is_tax_enabled');
    const taxPctInput = document.getElementById('tax_percentage');
    const taxNameInput = document.getElementById('tax_name');
    const badgeTaxStatus = document.getElementById('badge-tax-status');
    const simTaxLabel = document.getElementById('sim-tax-label');
    const simTaxValue = document.getElementById('sim-tax-value');
    const simTotalValue = document.getElementById('sim-total-value');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateTaxSimulation() {
        const isEnabled = taxEnabledInput ? taxEnabledInput.checked : true;
        const pct = taxPctInput ? parseFloat(taxPctInput.value) || 0 : 0;
        const name = taxNameInput ? (taxNameInput.value.trim() || 'Pajak') : 'Pajak';

        if (badgeTaxStatus) {
            if (isEnabled) {
                badgeTaxStatus.style.background = '#dcfce7';
                badgeTaxStatus.style.color = '#15803d';
                badgeTaxStatus.innerText = '✓ Pajak Aktif';
            } else {
                badgeTaxStatus.style.background = '#f1f5f9';
                badgeTaxStatus.style.color = '#64748b';
                badgeTaxStatus.innerText = 'Pajak Nonaktif';
            }
        }

        const subtotal = 50000;
        const discount = 5000;
        const dpp = subtotal - discount; // 45000
        const taxAmount = isEnabled ? (dpp * (pct / 100)) : 0;
        const totalAmount = dpp + taxAmount;

        if (simTaxLabel) {
            simTaxLabel.innerText = `${name} (${pct.toFixed(2)}%)`;
        }
        if (simTaxValue) {
            simTaxValue.innerText = isEnabled ? `+ ${formatRupiah(taxAmount)}` : 'Rp 0 (Nonaktif)';
            simTaxValue.style.color = isEnabled ? '#4f46e5' : '#94a3b8';
        }
        if (simTotalValue) {
            simTotalValue.innerText = formatRupiah(totalAmount);
        }
    }

    window.setTaxPreset = function(val) {
        if (taxPctInput) {
            taxPctInput.value = val.toFixed(2);
            if (val === 0 && taxEnabledInput) {
                taxEnabledInput.checked = false;
            } else if (val > 0 && taxEnabledInput) {
                taxEnabledInput.checked = true;
            }
            updateTaxSimulation();
        }
    };

    if (taxEnabledInput) taxEnabledInput.addEventListener('change', updateTaxSimulation);
    if (taxPctInput) taxPctInput.addEventListener('input', updateTaxSimulation);
    if (taxNameInput) taxNameInput.addEventListener('input', updateTaxSimulation);

    updateTaxSimulation();

    // 3. Leaflet Map Initialization
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meter');
    const badgeLatLon = document.getElementById('badge-lat-lon');

    let initialLat = parseFloat(latInput.value) || -6.9214771;
    let initialLon = parseFloat(lonInput.value) || 107.6166542;
    let initialRadius = parseInt(radiusInput.value) || 50;

    const map = L.map('map-preview').setView([initialLat, initialLon], 16);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);

    // Marker Pin Draggable
    let marker = L.marker([initialLat, initialLon], {
        draggable: true,
        title: "Titik Lokasi Kedai MoreBrew"
    }).addTo(map);

    // Geofencing Radius Circle
    let circle = L.circle([initialLat, initialLon], {
        color: '#2563eb',
        fillColor: '#3b82f6',
        fillOpacity: 0.2,
        radius: initialRadius
    }).addTo(map);

    function updateCoordinates(lat, lon) {
        lat = parseFloat(lat).toFixed(7);
        lon = parseFloat(lon).toFixed(7);

        latInput.value = lat;
        lonInput.value = lon;
        if (badgeLatLon) {
            badgeLatLon.innerText = `${lat}, ${lon}`;
        }

        marker.setLatLng([lat, lon]);
        circle.setLatLng([lat, lon]);
    }

    // Event: Marker dragged
    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    // Event: Map Click
    map.on('click', function (e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Event: Manual Input changes
    function onManualCoordChange() {
        const lat = parseFloat(latInput.value);
        const lon = parseFloat(lonInput.value);
        if (!isNaN(lat) && !isNaN(lon)) {
            marker.setLatLng([lat, lon]);
            circle.setLatLng([lat, lon]);
            map.panTo([lat, lon]);
            if (badgeLatLon) badgeLatLon.innerText = `${lat.toFixed(7)}, ${lon.toFixed(7)}`;
        }
    }
    latInput.addEventListener('input', onManualCoordChange);
    lonInput.addEventListener('input', onManualCoordChange);

    // Event: Radius Input change
    radiusInput.addEventListener('input', function () {
        const rad = parseInt(this.value) || 50;
        circle.setRadius(rad);
    });

    // 4. Tombol "Ambil Lokasi GPS Saya Saat Ini"
    const btnGps = document.getElementById('btn-get-current-gps');
    if (btnGps) {
        btnGps.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
                return;
            }

            const originalHtml = btnGps.innerHTML;
            btnGps.innerHTML = '<span>📡 Mencari Sinyal GPS...</span>';
            btnGps.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;

                    updateCoordinates(userLat, userLon);
                    map.flyTo([userLat, userLon], 18);

                    btnGps.innerHTML = '<span>✅ Lokasi GPS Ditemukan!</span>';
                    setTimeout(() => {
                        btnGps.innerHTML = originalHtml;
                        btnGps.disabled = false;
                    }, 2000);
                },
                function (error) {
                    alert('Gagal mendeteksi lokasi: ' + error.message + '\nPastikan izin lokasi telah diaktifkan di browser.');
                    btnGps.innerHTML = originalHtml;
                    btnGps.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // 5. Pencarian Alamat di Peta (OSM Nominatim Geocoding)
    const btnSearch = document.getElementById('btn-search-location');
    const searchInput = document.getElementById('map-search-input');

    function searchAddress() {
        const query = searchInput.value.trim();
        if (!query) return;

        btnSearch.innerText = 'Mencari...';
        btnSearch.disabled = true;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;

                if (data && data.length > 0) {
                    const first = data[0];
                    const lat = parseFloat(first.lat);
                    const lon = parseFloat(first.lon);

                    updateCoordinates(lat, lon);
                    map.flyTo([lat, lon], 17);
                } else {
                    alert('Lokasi tidak ditemukan. Coba ketik nama jalan atau kota yang lebih spesifik.');
                }
            })
            .catch(err => {
                console.error(err);
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;
                alert('Terjadi kesalahan koneksi saat mencari alamat.');
            });
    }

    if (btnSearch && searchInput) {
        btnSearch.addEventListener('click', searchAddress);
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchAddress();
            }
        });
    }

    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
</script>
@endsection

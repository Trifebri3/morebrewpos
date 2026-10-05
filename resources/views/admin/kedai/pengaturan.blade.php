@extends(request()->routeIs('kasir.*') ? 'kasir.layouts.app' : 'admin.layouts.app', $data ?? [])

@section('content')
<!-- Leaflet CSS & JS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    :root {
        --pure-black: #000000;
        --dark-slate: #0f172a;
        --border-subtle: #e2e8f0;
        --bg-subtle: #f8fafc;
    }

    .settings-container {
        padding: 28px 36px 80px;
        max-width: 1360px;
        margin: 0 auto;
    }

    /* Top Sticky Header Banner */
    .header-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .header-title-box h1 {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-slate);
        letter-spacing: -0.4px;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-title-box p {
        color: #64748b;
        font-size: 13.5px;
        margin: 0;
    }

    /* Quick Tabs / Anchor Nav */
    .quick-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 24px;
        scrollbar-width: none;
    }
    .quick-tabs::-webkit-scrollbar { display: none; }

    .tab-pill {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        color: #475569;
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .tab-pill:hover, .tab-pill.active {
        background: var(--dark-slate);
        color: #ffffff;
        border-color: var(--dark-slate);
    }
    .tab-pill.active svg, .tab-pill:hover svg {
        stroke: #ffffff !important;
    }

    /* Layout Grid */
    .settings-layout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 28px;
        align-items: flex-start;
    }

    @media (max-width: 1080px) {
        .settings-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Seamless Cards */
    .card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .card:hover {
        border-color: #cbd5e1;
    }

    .card-header {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-header h2 {
        font-size: 15.5px;
        font-weight: 700;
        color: var(--dark-slate);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header .header-badge {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .card-body {
        padding: 24px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }

    .form-group .helper-text {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
        line-height: 1.4;
    }

    .input-control {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        color: var(--dark-slate);
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .input-control:focus {
        border-color: var(--pure-black);
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.06);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px;
    }

    @media (max-width: 640px) {
        .grid-2, .grid-3 {
            grid-template-columns: 1fr;
        }
    }

    /* Pure Seamless Logo (NO CONTAINER BEHIND LOGO) */
    .seamless-logo-row {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 16px 0;
    }

    .seamless-logo-display {
        width: 80px;
        height: 80px;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .seamless-logo-display img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .btn-choose-file {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-choose-file:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    /* iOS Style Toggle Switch */
    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 22px;
        transition: background 0.2s, border-color 0.2s;
    }
    .switch-container.is-active {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .switch-wrapper {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }
    .switch-wrapper input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 28px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(24px);
    }

    /* Tax Preset Buttons */
    .tax-preset-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }
    .tax-preset-btn:hover {
        background: var(--dark-slate);
        color: #ffffff;
        border-color: var(--dark-slate);
    }

    /* Action Buttons */
    .btn-submit {
        background: var(--pure-black);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }
    .btn-submit:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
    }
    .btn-submit:active {
        transform: translateY(0);
    }

    /* Real-life Thermal Receipt Preview */
    .receipt-paper-box {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 24px 20px;
        font-family: 'Courier New', Courier, monospace;
        color: #000000;
        font-size: 12px;
        line-height: 1.5;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        position: relative;
    }
    .receipt-paper-box::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 0;
        right: 0;
        height: 6px;
        background: radial-gradient(circle, transparent, transparent 50%, #ffffff 50%, #ffffff 100% );
        background-size: 12px 6px;
    }
    .receipt-paper-box .dashed-line {
        border-top: 1px dashed #000000;
        margin: 10px 0;
    }

    /* Map Box */
    #map-preview {
        width: 100%;
        height: 360px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        z-index: 10;
    }
    .btn-action-map {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.15s;
    }
    .btn-action-map:hover {
        background: #f1f5f9;
    }

    /* Alerts */
    .alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13.5px;
        font-weight: 500;
    }
</style>

<div class="settings-container">
    <!-- Top Header -->
    <div class="header-banner">
        <div class="header-title-box">
            <h1>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                <span>Pengaturan Kedai, Pajak (PB1) & Sinkronisasi</span>
            </h1>
            <p>Kelola aktivasi & tarif Pajak Restoran (PB1), logo kedai tanpa background container, kop struk thermal, WiFi, koordinat GPS, serta sinkronisasi ke server mobile app.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" onclick="document.getElementById('form-pengaturan').submit();" class="btn-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- Quick Navigation Tabs -->
    <div class="quick-tabs">
        <a href="#section-pajak" class="tab-pill active">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><line x1="19" x2="5" y1="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
            <span>Pajak (PB1) & Keuangan</span>
        </a>
        <a href="#section-profil" class="tab-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Profil Kedai & Logo</span>
        </a>
        <a href="#section-struk" class="tab-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span>Struk, Wi-Fi & Medsos</span>
        </a>
        <a href="#section-gps" class="tab-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>GPS & Absensi</span>
        </a>
        <a href="#section-sync" class="tab-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            <span>Sinkronisasi Server & Mobile App</span>
        </a>
    </div>

    @if (session('success'))
    <div class="alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div style="background: #fef2f2; color: #991b1b; padding: 16px 20px; border-radius: 12px; font-size: 13.5px; margin-bottom: 24px; border: 1px solid #fecaca;">
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

        <div class="settings-layout-grid">
            <!-- LEFT MAIN COLUMN -->
            <div>
                <!-- SECTION 1: PAJAK TRANSAKSI (PB1 / TAX) & KEUANGAN -->
                <div class="card" id="section-pajak">
                    <div class="card-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" x2="5" y1="5" y2="19"/>
                                <circle cx="6.5" cy="6.5" r="2.5"/>
                                <circle cx="17.5" cy="17.5" r="2.5"/>
                            </svg>
                            <span>Pajak Transaksi (PB1 / PPN) & Anggaran Harian</span>
                        </h2>
                        <span class="header-badge">Sistem Kasir Otomatis</span>
                    </div>
                    <div class="card-body">
                        <!-- Toggle Switch Aktif/Nonaktif Pajak -->
                        <div class="switch-container {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'is-active' : '' }}" id="switch-tax-box">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><line x1="19" x2="5" y1="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 700; color: #0f172a;">Aktivasi Pajak Transaksi (PPN / PB1)</div>
                                    <div id="tax-status-subtitle" style="font-size: 12px; color: {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? '#166534' : '#64748b' }}; margin-top: 2px;">
                                        {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'Pajak aktif dihitung saat kasir transaksi' : 'Pajak dinonaktifkan (tidak perlu ada pajak)' }}
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span id="badge-tax-status" style="font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 20px; {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'background: #dcfce7; color: #15803d;' : 'background: #f1f5f9; color: #64748b;' }}">
                                    {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? '✓ Pajak Aktif' : 'Pajak Nonaktif' }}
                                </span>
                                <label class="switch-wrapper">
                                    <input type="checkbox" name="is_tax_enabled" id="is_tax_enabled" value="1" {{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Form fields when tax enabled -->
                        <div class="grid-3">
                            <div class="form-group">
                                <label for="tax_percentage">Besar Tarif Pajak (%)</label>
                                <div style="position: relative;">
                                    <input type="number" step="0.01" min="0" max="100" name="tax_percentage" id="tax_percentage" value="{{ old('tax_percentage', $kedai->tax_percentage ?? 11.00) }}" class="input-control" placeholder="11.00" required style="padding-right: 36px; font-weight: 700;">
                                    <span style="position: absolute; right: 14px; top: 11px; font-weight: 700; color: #94a3b8;">%</span>
                                </div>
                                <div style="display: flex; gap: 5px; margin-top: 8px;">
                                    <button type="button" class="tax-preset-btn" onclick="setTaxPreset(0)">0% Bebas</button>
                                    <button type="button" class="tax-preset-btn" onclick="setTaxPreset(10)">10% PB1</button>
                                    <button type="button" class="tax-preset-btn" onclick="setTaxPreset(11)">11% PPN</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tax_name">Nama / Label Pajak di Struk</label>
                                <input type="text" name="tax_name" id="tax_name" value="{{ old('tax_name', $kedai->tax_name ?? 'PB1 (Pajak Restoran)') }}" class="input-control" placeholder="Contoh: PB1 (Pajak Restoran)" required>
                                <div class="helper-text">Tercetak pada struk thermal & PDF (misal: "PB1", "Pajak Restoran").</div>
                            </div>

                            <div class="form-group">
                                <label for="budget_harian">Plafon Anggaran Harian (Rp)</label>
                                <input type="number" step="1000" min="0" name="budget_harian" id="budget_harian" value="{{ old('budget_harian', $kedai->budget_harian ?? 1000000) }}" class="input-control" placeholder="1000000">
                                <div class="helper-text">Batas belanja operasional harian kedai.</div>
                            </div>
                        </div>

                        <!-- Info Alert when Tax Disabled -->
                        <div id="tax-disabled-notice" style="{{ old('is_tax_enabled', $kedai->is_tax_enabled ?? true) ? 'display: none;' : '' }} background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #475569; display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <span><b>Status: Bebas Pajak (0%).</b> Transaksi di kasir POS dan mobile tidak akan menambahkan biaya pajak tambahan ke tagihan pelanggan.</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: PROFIL KEDAI & LOGO (NO CONTAINER BEHIND LOGO) -->
                <div class="card" id="section-profil">
                    <div class="card-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                            <span>Profil & Logo Resmi Kedai</span>
                        </h2>
                        <span class="header-badge">Identitas Toko</span>
                    </div>
                    <div class="card-body">
                        <!-- Logo Kedai (TRANSPARAN TANPA CONTAINER DI BELAKANG LOGO) -->
                        <div class="form-group" style="padding-bottom: 18px; border-bottom: 1px solid #f1f5f9;">
                            <label>Logo Resmi Kedai (Tampil di Struk Thermal, PDF & Website)</label>
                            <div class="seamless-logo-row">
                                <!-- Logo preview with NO background or box container -->
                                <div class="seamless-logo-display">
                                    <img id="logo-preview" src="{{ asset('logo.png') }}?v={{ file_exists(public_path('logo.png')) ? filemtime(public_path('logo.png')) : time() }}" alt="Logo MoreBrew" onerror="this.src='https://placehold.co/80x80/transparent/000000?text=MB'">
                                </div>
                                <div style="flex: 1;">
                                    <label for="logo-input" class="btn-choose-file">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span>Pilih / Ganti Logo Baru</span>
                                    </label>
                                    <input type="file" name="logo" id="logo-input" accept="image/*" style="display: none;">
                                    <div class="helper-text" style="margin-top: 6px;">
                                        Disarankan file format PNG transparan. Logo ini ditampilkan bersih tanpa latar kotak pada struk thermal & PDF invoice.
                                    </div>
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
                            <div class="helper-text">Alamat dicetak rapi pada bagian atas struk thermal.</div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: FORMAT STRUK, WI-FI & MEDIA SOSIAL -->
                <div class="card" id="section-struk">
                    <div class="card-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                            <span>Format Struk, Informasi Wi-Fi & Media Sosial</span>
                        </h2>
                        <span class="header-badge">Tercetak di Struk Pelanggan</span>
                    </div>
                    <div class="card-body">
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="receipt_header">Slogan / Header Struk</label>
                                <input type="text" name="receipt_header" id="receipt_header" value="{{ old('receipt_header', $kedai->receipt_header ?? 'something, between home and everywhere') }}" class="input-control" placeholder="something, between home and everywhere">
                                <div class="helper-text">Tercetak di bawah nama toko pada struk printer.</div>
                            </div>
                            <div class="form-group">
                                <label for="receipt_footer">Ucapan Terima Kasih / Footer Struk</label>
                                <input type="text" name="receipt_footer" id="receipt_footer" value="{{ old('receipt_footer', $kedai->receipt_footer ?? 'Silakan datang kembali!') }}" class="input-control" placeholder="Silakan datang kembali!">
                                <div class="helper-text">Pesan penutup di bagian paling bawah struk.</div>
                            </div>
                        </div>

                        <div class="grid-3" style="margin-top: 6px;">
                            <div class="form-group">
                                <label for="wifi_ssid">Nama Wi-Fi Kedai (SSID)</label>
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

                <!-- SECTION 4: GPS & PETA INTERAKTIF GEOFENCING -->
                <div class="card" id="section-gps">
                    <div class="card-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>Titik Koordinat GPS & Radius Absensi</span>
                        </h2>
                        <span class="header-badge">Peta Geofencing</span>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                            <div style="display: flex; gap: 8px; flex: 1; min-width: 250px;">
                                <input type="text" id="map-search-input" placeholder="Cari nama jalan atau kota di peta..." class="input-control" style="padding: 8px 12px; font-size: 13px;">
                                <button type="button" id="btn-search-location" class="btn-action-map">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <span>Cari</span>
                                </button>
                            </div>
                            <button type="button" id="btn-get-current-gps" class="btn-action-map" style="background: #0f172a; color: white; border-color: #0f172a;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                <span>Ambil Lokasi GPS Saya</span>
                            </button>
                        </div>

                        <!-- Leaflet Interactive Map -->
                        <div id="map-preview"></div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 12px; color: #64748b;">
                            <span>💡 Klik peta atau geser pin marker untuk memperbarui titik koordinat secara langsung.</span>
                            <div>
                                <span>Koordinat: </span>
                                <strong id="badge-lat-lon" style="color: #0f172a;">-6.921477, 107.616654</strong>
                            </div>
                        </div>

                        <div class="grid-3" style="margin-top: 18px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
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
                            </div>
                        </div>

                        <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                                <input type="checkbox" name="is_qr_absen_enabled" value="1" {{ old('is_qr_absen_enabled', $kedai->is_qr_absen_enabled ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #000000; cursor: pointer;">
                                <div>
                                    <div style="font-size: 13.5px; font-weight: 600; color: #0f172a;">Tampilkan Tombol Absen di Layar POS Kasir</div>
                                    <div style="font-size: 11.5px; color: #64748b;">Memungkinkan karyawan melakukan scan absensi QR langsung melalui terminal POS kasir.</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                                <input type="checkbox" name="is_link_absen_enabled" value="1" {{ old('is_link_absen_enabled', $kedai->is_link_absen_enabled ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #000000; cursor: pointer;">
                                <div>
                                    <div style="font-size: 13.5px; font-weight: 600; color: #0f172a;">Izinkan Absensi Karyawan via Link Mandiri (/absen)</div>
                                    <div style="font-size: 11.5px; color: #64748b;">Karyawan dapat membuka link absen mandiri di smartphone masing-masing dengan verifikasi GPS.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: SINKRONISASI SERVER & APLIKASI MOBILE (MATCHES APP) -->
                <div class="card" id="section-sync">
                    <div class="card-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                            <span>Sinkronisasi Server Web Cloud / Local & Mobile App</span>
                        </h2>
                        <span class="header-badge" id="api-status-badge" style="background: #f1f5f9; color: #0f172a;">
                            Status: Memeriksa...
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="server_api_url">URL Endpoint Server API</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" id="server_api_url" value="{{ url('/api') }}" class="input-control" placeholder="https://app.morebrewcafe.com/api">
                                <button type="button" id="btn-test-ping" class="btn-action-map" style="background: #0f172a; color: white; border-color: #0f172a; white-space: nowrap;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <span>Tes Koneksi API</span>
                                </button>
                            </div>
                            <div style="display: flex; gap: 6px; margin-top: 8px;">
                                <button type="button" class="tax-preset-btn" onclick="document.getElementById('server_api_url').value='{{ url('/api') }}'">Server Ini ({{ url('/api') }})</button>
                                <button type="button" class="tax-preset-btn" onclick="document.getElementById('server_api_url').value='https://app.morebrewcafe.com/api'">Web Cloud Resmi</button>
                                <button type="button" class="tax-preset-btn" onclick="document.getElementById('server_api_url').value='http://127.0.0.1:8000/api'">Local Port 8000</button>
                            </div>
                            <div class="helper-text" id="ping-result-text">
                                Endpoint ini digunakan oleh aplikasi Android POS untuk menyinkronkan data kedai, tarif pajak, produk, dan transaksi secara instan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Submit Button -->
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <button type="submit" class="btn-submit" style="padding: 13px 32px; font-size: 15px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span>Simpan Seluruh Pengaturan</span>
                    </button>
                </div>
            </div>

            <!-- RIGHT COLUMN: REALTIME THERMAL RECEIPT & CALCULATION SIMULATOR -->
            <div style="position: sticky; top: 20px;">
                <!-- Thermal Receipt Paper Simulation -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header" style="background: #ffffff;">
                        <h2>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <span>Simulasi Struk Thermal</span>
                        </h2>
                        <span style="font-size: 11px; font-weight: 700; color: #64748b;">58mm / 80mm</span>
                    </div>
                    <div class="card-body" style="background: #f8fafc; padding: 18px;">
                        <div class="receipt-paper-box">
                            <!-- Logo in receipt without any container -->
                            <div style="text-align: center; margin-bottom: 8px;">
                                <img id="receipt-preview-logo" src="{{ asset('logo.png') }}?v={{ file_exists(public_path('logo.png')) ? filemtime(public_path('logo.png')) : time() }}" alt="Logo" style="height: 38px; width: auto; object-fit: contain; background: transparent; border: none;" onerror="this.style.display='none'">
                            </div>

                            <div style="text-align: center; font-weight: bold; font-size: 13px;" id="receipt-preview-name">{{ $kedai->name ?? 'MOREBREWW' }}</div>
                            <div style="text-align: center; font-size: 10px; color: #4b5563; margin-top: 2px;" id="receipt-preview-header">{{ $kedai->receipt_header ?? 'something, between home and everywhere' }}</div>
                            <div style="text-align: center; font-size: 9.5px; color: #4b5563; margin-top: 3px;" id="receipt-preview-address">{{ $kedai->address ?? 'Jl. Sasmitatmaja No.6, Bandung' }}</div>
                            <div style="text-align: center; font-size: 9.5px; color: #4b5563;" id="receipt-preview-phone">{{ $kedai->phone ?? '0812-3456-7890' }}</div>

                            <div class="dashed-line"></div>

                            <div style="display: flex; justify-content: space-between; font-size: 10.5px;">
                                <span>No: #INV-2601</span>
                                <span>{{ date('d/m/Y H:i') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 10.5px;">
                                <span>Kasir: Admin</span>
                                <span>Dine In (Meja 04)</span>
                            </div>

                            <div class="dashed-line"></div>

                            <!-- Sample items -->
                            <div style="margin-bottom: 4px;">
                                <div style="font-weight: bold; font-size: 11px;">1x Japanese V60</div>
                                <div style="display: flex; justify-content: space-between; font-size: 10.5px; padding-left: 10px;">
                                    <span>Single Origin Gayo</span>
                                    <span>28.000</span>
                                </div>
                            </div>
                            <div style="margin-bottom: 4px;">
                                <div style="font-weight: bold; font-size: 11px;">1x Cafe Latte</div>
                                <div style="display: flex; justify-content: space-between; font-size: 10.5px; padding-left: 10px;">
                                    <span>Oat Milk & Less Sugar</span>
                                    <span>22.000</span>
                                </div>
                            </div>

                            <div class="dashed-line"></div>

                            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                                <span>Subtotal</span>
                                <span>Rp 50.000</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                                <span>Diskon Voucher</span>
                                <span>- Rp 5.000</span>
                            </div>

                            <!-- Dynamic Tax Line -->
                            <div id="receipt-tax-row" style="display: flex; justify-content: space-between; font-size: 11px; font-weight: bold;">
                                <span id="receipt-tax-label">{{ $kedai->tax_name ?? 'PB1' }} ({{ $kedai->tax_percentage ?? 11 }}%)</span>
                                <span id="receipt-tax-val">Rp 4.950</span>
                            </div>

                            <div class="dashed-line"></div>

                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 900;">
                                <span>TOTAL</span>
                                <span id="receipt-total-val">Rp 49.950</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 10.5px; margin-top: 4px;">
                                <span>Bayar (Cash)</span>
                                <span>Rp 50.000</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 10.5px;">
                                <span>Kembalian</span>
                                <span id="receipt-change-val">Rp 50</span>
                            </div>

                            <div class="dashed-line"></div>

                            <!-- WiFi & Social Media in Receipt -->
                            <div style="text-align: center; font-size: 9.5px; margin-bottom: 4px;">
                                <div>Wi-Fi: <strong id="receipt-preview-wifi-ssid">{{ $kedai->wifi_ssid ?? 'moreandmore' }}</strong></div>
                                <div>Password: <strong id="receipt-preview-wifi-pass">{{ $kedai->wifi_password ?? 'bolehlihatsenyumnya?' }}</strong></div>
                                <div style="margin-top: 2px;" id="receipt-preview-instagram">{{ $kedai->instagram ?? '@morebrewcoffee' }}</div>
                            </div>

                            <div style="text-align: center; font-size: 10px; font-weight: bold; margin-top: 6px;" id="receipt-preview-footer">
                                {{ $kedai->receipt_footer ?? 'Silakan datang kembali!' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Realtime Calculation Card -->
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 10h10"/><path d="M12 7v6"/></svg>
                            <span>Kalkulasi Pajak Kasir</span>
                        </h2>
                    </div>
                    <div class="card-body" style="padding: 16px 20px;">
                        <div style="font-size: 12.5px; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Dasar Pajak (DPP):</span>
                                <strong style="color: #0f172a;">Rp 45.000</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;" id="calc-tax-label">Pajak (11%):</span>
                                <strong id="calc-tax-val" style="color: #10b981;">+ Rp 4.950</strong>
                            </div>
                            <div style="border-top: 1px dashed #cbd5e1; padding-top: 8px; display: flex; justify-content: space-between; font-size: 13.5px;">
                                <span style="font-weight: 700; color: #0f172a;">Total Tagihan:</span>
                                <strong id="calc-total-val" style="color: #0f172a;">Rp 49.950</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Scripts for Realtime Reactivity, Map, Preview & API Ping -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Logo Realtime Preview (Floating with NO container behind it)
    const logoInput = document.getElementById('logo-input');
    const logoPreview = document.getElementById('logo-preview');
    const receiptPreviewLogo = document.getElementById('receipt-preview-logo');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreview.src = event.target.result;
                    if (receiptPreviewLogo) {
                        receiptPreviewLogo.src = event.target.result;
                        receiptPreviewLogo.style.display = 'inline-block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Real-time Tax Calculation & Receipt Live Update
    const taxEnabledInput = document.getElementById('is_tax_enabled');
    const taxPctInput = document.getElementById('tax_percentage');
    const taxNameInput = document.getElementById('tax_name');
    const switchTaxBox = document.getElementById('switch-tax-box');
    const badgeTaxStatus = document.getElementById('badge-tax-status');
    const taxSubtitle = document.getElementById('tax-status-subtitle');
    const taxDisabledNotice = document.getElementById('tax-disabled-notice');

    // Receipt DOM elements
    const receiptTaxRow = document.getElementById('receipt-tax-row');
    const receiptTaxLabel = document.getElementById('receipt-tax-label');
    const receiptTaxVal = document.getElementById('receipt-tax-val');
    const receiptTotalVal = document.getElementById('receipt-total-val');
    const receiptChangeVal = document.getElementById('receipt-change-val');

    // Calc box DOM elements
    const calcTaxLabel = document.getElementById('calc-tax-label');
    const calcTaxVal = document.getElementById('calc-tax-val');
    const calcTotalVal = document.getElementById('calc-total-val');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateTaxSim() {
        const isEnabled = taxEnabledInput ? taxEnabledInput.checked : true;
        const pct = taxPctInput ? (parseFloat(taxPctInput.value) || 0) : 0;
        const name = taxNameInput ? (taxNameInput.value.trim() || 'PB1 (Pajak Restoran)') : 'PB1';

        // Switch container UI
        if (switchTaxBox) {
            if (isEnabled) {
                switchTaxBox.classList.add('is-active');
            } else {
                switchTaxBox.classList.remove('is-active');
            }
        }

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

        if (taxSubtitle) {
            taxSubtitle.innerText = isEnabled 
                ? 'Pajak aktif dihitung saat kasir transaksi' 
                : 'Pajak dinonaktifkan (tidak perlu ada pajak)';
            taxSubtitle.style.color = isEnabled ? '#166534' : '#64748b';
        }

        if (taxDisabledNotice) {
            taxDisabledNotice.style.display = isEnabled ? 'none' : 'flex';
        }

        // Calculation: 50.000 subtotal, 5.000 diskon -> 45.000 DPP
        const dpp = 45000;
        const taxAmount = isEnabled ? (dpp * (pct / 100)) : 0;
        const total = dpp + taxAmount;
        const change = Math.max(0, 50000 - total);

        // Update receipt simulation
        if (receiptTaxRow) {
            if (isEnabled && pct > 0) {
                receiptTaxRow.style.display = 'flex';
                if (receiptTaxLabel) receiptTaxLabel.innerText = `${name} (${pct}%)`;
                if (receiptTaxVal) receiptTaxVal.innerText = formatRupiah(taxAmount);
            } else {
                receiptTaxRow.style.display = 'none';
            }
        }
        if (receiptTotalVal) receiptTotalVal.innerText = formatRupiah(total);
        if (receiptChangeVal) receiptChangeVal.innerText = formatRupiah(change);

        // Update calculation card
        if (calcTaxLabel) calcTaxLabel.innerText = `${name} (${pct}%):`;
        if (calcTaxVal) {
            calcTaxVal.innerText = isEnabled ? `+ ${formatRupiah(taxAmount)}` : 'Rp 0 (Bebas Pajak)';
            calcTaxVal.style.color = isEnabled ? '#10b981' : '#94a3b8';
        }
        if (calcTotalVal) calcTotalVal.innerText = formatRupiah(total);
    }

    window.setTaxPreset = function(val) {
        if (taxPctInput) {
            taxPctInput.value = val;
            if (val === 0 && taxEnabledInput) {
                taxEnabledInput.checked = false;
            } else if (val > 0 && taxEnabledInput) {
                taxEnabledInput.checked = true;
            }
            updateTaxSim();
        }
    };

    if (taxEnabledInput) taxEnabledInput.addEventListener('change', updateTaxSim);
    if (taxPctInput) taxPctInput.addEventListener('input', updateTaxSim);
    if (taxNameInput) taxNameInput.addEventListener('input', updateTaxSim);
    updateTaxSim();

    // 3. Live update receipt text fields
    function bindReceiptSync(inputId, targetId) {
        const inp = document.getElementById(inputId);
        const tgt = document.getElementById(targetId);
        if (inp && tgt) {
            inp.addEventListener('input', function () {
                tgt.innerText = this.value || '-';
            });
        }
    }
    bindReceiptSync('name', 'receipt-preview-name');
    bindReceiptSync('receipt_header', 'receipt-preview-header');
    bindReceiptSync('address', 'receipt-preview-address');
    bindReceiptSync('phone', 'receipt-preview-phone');
    bindReceiptSync('wifi_ssid', 'receipt-preview-wifi-ssid');
    bindReceiptSync('wifi_password', 'receipt-preview-wifi-pass');
    bindReceiptSync('instagram', 'receipt-preview-instagram');
    bindReceiptSync('receipt_footer', 'receipt-preview-footer');

    // 4. Leaflet Map Interactive
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
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([initialLat, initialLon], {
        draggable: true,
        title: "Titik Lokasi Kedai"
    }).addTo(map);

    let circle = L.circle([initialLat, initialLon], {
        color: '#0f172a',
        fillColor: '#38bdf8',
        fillOpacity: 0.2,
        radius: initialRadius
    }).addTo(map);

    function updateCoordinates(lat, lon) {
        lat = parseFloat(lat).toFixed(7);
        lon = parseFloat(lon).toFixed(7);

        latInput.value = lat;
        lonInput.value = lon;
        if (badgeLatLon) badgeLatLon.innerText = `${lat}, ${lon}`;

        marker.setLatLng([lat, lon]);
        circle.setLatLng([lat, lon]);
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    radiusInput.addEventListener('input', function () {
        const rad = parseInt(this.value) || 50;
        circle.setRadius(rad);
    });

    // GPS Button
    const btnGps = document.getElementById('btn-get-current-gps');
    if (btnGps) {
        btnGps.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation GPS.');
                return;
            }
            btnGps.innerHTML = '<span>📡 Mencari Sinyal GPS...</span>';
            btnGps.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const uLat = position.coords.latitude;
                    const uLon = position.coords.longitude;
                    updateCoordinates(uLat, uLon);
                    map.flyTo([uLat, uLon], 18);
                    btnGps.innerHTML = '<span>✅ Lokasi GPS Ditemukan!</span>';
                    setTimeout(() => {
                        btnGps.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg><span>Ambil Lokasi GPS Saya</span>`;
                        btnGps.disabled = false;
                    }, 2000);
                },
                function (error) {
                    alert('Gagal mendeteksi GPS: ' + error.message);
                    btnGps.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg><span>Ambil Lokasi GPS Saya</span>`;
                    btnGps.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // Search Location on Map
    const btnSearch = document.getElementById('btn-search-location');
    const searchInput = document.getElementById('map-search-input');
    function searchAddress() {
        const q = searchInput.value.trim();
        if (!q) return;
        btnSearch.innerText = 'Mencari...';
        btnSearch.disabled = true;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    updateCoordinates(lat, lon);
                    map.flyTo([lat, lon], 17);
                } else {
                    alert('Alamat tidak ditemukan.');
                }
            })
            .catch(() => {
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;
                alert('Gagal mencari alamat.');
            });
    }
    if (btnSearch && searchInput) {
        btnSearch.addEventListener('click', searchAddress);
        searchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); searchAddress(); } });
    }

    // 5. Test Ping API Server
    const btnTestPing = document.getElementById('btn-test-ping');
    const apiBadge = document.getElementById('api-status-badge');
    const pingResultText = document.getElementById('ping-result-text');

    function testServerPing() {
        const urlInput = document.getElementById('server_api_url');
        let baseUrl = (urlInput ? urlInput.value.trim() : '') || '{{ url("/api") }}';
        let endpoint = baseUrl.replace(/\/+$/, '') + '/settings';

        if (apiBadge) {
            apiBadge.innerText = 'Menguji Koneksi...';
            apiBadge.style.background = '#fef3c7';
            apiBadge.style.color = '#b45309';
        }

        const start = performance.now();
        fetch(endpoint, { method: 'GET', headers: { 'Accept': 'application/json' } })
            .then(res => {
                const latency = Math.round(performance.now() - start);
                if (res.ok) {
                    if (apiBadge) {
                        apiBadge.innerText = `● Server Online (${res.status} OK • ${latency}ms)`;
                        apiBadge.style.background = '#dcfce7';
                        apiBadge.style.color = '#15803d';
                    }
                    if (pingResultText) {
                        pingResultText.innerHTML = `✅ <b>Koneksi API Sukses:</b> Server merespons dalam ${latency}ms. Aplikasi POS mobile siap melakukan sinkronisasi otomatis.`;
                    }
                } else {
                    throw new Error(`HTTP ${res.status}`);
                }
            })
            .catch(err => {
                if (apiBadge) {
                    apiBadge.innerText = '● Server Offline / Error';
                    apiBadge.style.background = '#fee2e2';
                    apiBadge.style.color = '#991b1b';
                }
                if (pingResultText) {
                    pingResultText.innerHTML = `⚠️ <b>Koneksi Gagal:</b> ${err.message}. Pastikan server berjalan dan URL endpoint API sudah benar.`;
                }
            });
    }

    if (btnTestPing) {
        btnTestPing.addEventListener('click', testServerPing);
    }
    testServerPing();

    setTimeout(() => { map.invalidateSize(); }, 400);
});
</script>
@endsection

@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    :root {
        --pure-black: #000000;
        --dark-slate: #0f172a;
        --border-subtle: #e2e8f0;
        --bg-subtle: #f8fafc;
        --text-muted: #64748b;
    }

    .laporan-produk-container {
        padding: 28px 36px 80px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Bar */
    .app-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .app-title-group h1 {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-slate);
        letter-spacing: -0.4px;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .app-title-group p {
        color: var(--text-muted);
        font-size: 13.5px;
        margin: 0;
    }

    .btn-native {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid var(--border-subtle);
    }
    .btn-native-green {
        background: #166534;
        color: #ffffff;
        border-color: #166534;
    }
    .btn-native-green:hover {
        background: #14532d;
        color: #ffffff;
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .kpi-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--dark-slate);
        line-height: 1.2;
    }

    /* Table Panel */
    .table-app-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .table-toolbar {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .table-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--dark-slate);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-input-box {
        position: relative;
        min-width: 240px;
    }
    .search-input-box input {
        width: 100%;
        padding: 8px 12px 8px 34px;
        border-radius: 9px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-input-box input:focus {
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }
    .search-input-box svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .app-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13.5px;
    }
    .app-table th {
        padding: 13px 20px;
        background: #f8fafc;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-subtle);
        white-space: nowrap;
    }
    .app-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
        color: #1e293b;
    }
    .app-table tr:hover td {
        background: #fafafa;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-aktif { background: #dcfce7; color: #166534; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; }
</style>

<div class="laporan-produk-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Laporan Performa Produk & Menu
            </h1>
            <p>Analisis kuantitas terjual, sisa ketersediaan stok, dan kontribusi omzet per varian menu.</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan.produk.export') }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Total Varian Menu</div>
                <div class="kpi-value">{{ $totalProduk ?? 0 }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Item terdaftar di POS</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Total Kuantitas Terjual</div>
                <div class="kpi-value" style="color: #0284c7;">{{ number_format($totalTerjual ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Cup / Porsi laku terjual</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#166534" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Estimasi Omzet Penjualan</div>
                <div class="kpi-value" style="color: #166534;">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Total nilai penjualan produk</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-app-card">
        <div class="table-toolbar">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Daftar Seluruh Produk & Penjualan ({{ $produks->count() }})
            </h2>

            <div class="search-input-box">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="product-search" placeholder="Cari nama, SKU, kategori..." onkeyup="filterProducts()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Menu / Produk</th>
                        <th>Kategori</th>
                        <th>Harga Satuan</th>
                        <th>Sisa Stok</th>
                        <th>Qty Terjual</th>
                        <th style="text-align: right;">Estimasi Omzet</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $p)
                    <tr class="product-row" data-search="{{ strtolower(($p->sku ?: '') . ' ' . $p->name . ' ' . ($p->category ?: 'umum')) }}">
                        <td style="font-family: monospace; font-size: 12px; font-weight: 600; color: #475569;">
                            {{ $p->sku ?: '-' }}
                        </td>
                        <td>
                            <strong style="color: var(--dark-slate); font-size: 14px;">{{ $p->name }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; padding: 3px 8px; background: #f1f5f9; border-radius: 6px; font-weight: 600; color: #475569;">
                                {{ $p->category ?: 'Umum' }}
                            </span>
                        </td>
                        <td style="font-weight: 600; color: #0f172a;">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </td>
                        <td>
                            <span style="font-weight: 700; color: {{ $p->stock <= 10 ? '#dc2626' : 'var(--dark-slate)' }};">
                                {{ $p->stock }}
                            </span>
                        </td>
                        <td>
                            <span style="background: #f1f5f9; color: #0f172a; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                {{ $p->terjual ?? 0 }} cup
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #166534; font-size: 14px;">
                            Rp {{ number_format($p->omzet ?? 0, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right;">
                            <span class="badge-status {{ $p->is_active ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            Belum ada data produk terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterProducts() {
        const query = document.getElementById('product-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const data = row.getAttribute('data-search') || '';
            if (data.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection

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

    .penjualan-report-container {
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
    .btn-native-dark {
        background: var(--pure-black);
        color: #ffffff;
        border-color: var(--pure-black);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .btn-native-dark:hover {
        background: #1e293b;
        color: #ffffff;
    }
    .btn-native-white {
        background: #ffffff;
        color: #0f172a;
    }
    .btn-native-white:hover, .btn-native-white.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
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

    /* Period Filter Pills */
    .period-filter-row {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
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

    /* Filter Form */
    .filter-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .input-field-app {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
    }
    .input-field-app:focus {
        border-color: #000000;
    }

    /* Tables */
    .card-panel {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .card-panel-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .panel-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--dark-slate);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-input-box {
        position: relative;
        min-width: 200px;
    }
    .search-input-box input {
        width: 100%;
        padding: 7px 10px 7px 30px;
        border-radius: 8px;
        border: 1px solid var(--border-subtle);
        font-size: 12.5px;
        outline: none;
    }
    .search-input-box svg {
        position: absolute;
        left: 9px;
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
        padding: 12px 18px;
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
        padding: 13px 18px;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
        color: #1e293b;
    }
    .app-table tr:hover td {
        background: #fafafa;
    }
</style>

<div class="penjualan-report-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                {{ $data['title'] ?? 'Laporan Penjualan & Transaksi' }}
            </h1>
            <p>{{ $data['subtitle'] ?? 'Ringkasan omset penjualan kasir, jumlah transaksi, dan rekapitulasi produk terjual.' }}</p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div class="period-filter-row">
                <a href="{{ route('admin.laporan.penjualan', ['type' => 'harian']) }}" class="btn-native btn-native-white {{ ($type ?? '') == 'harian' ? 'active' : '' }}">
                    Harian
                </a>
                <a href="{{ route('admin.laporan.penjualan', ['type' => 'bulanan']) }}" class="btn-native btn-native-white {{ ($type ?? '') == 'bulanan' ? 'active' : '' }}">
                    Bulanan
                </a>
                <a href="{{ route('admin.laporan.penjualan', ['type' => 'lengkap']) }}" class="btn-native btn-native-white {{ ($type ?? '') == 'lengkap' ? 'active' : '' }}">
                    Semua
                </a>
            </div>

            <a href="{{ route('admin.laporan.penjualan.export', request()->query()) }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- Date Range Picker -->
    <div class="filter-card">
        <div style="font-weight: 700; font-size: 13px; color: var(--dark-slate); display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Filter Rentang Tanggal Khusus:
        </div>
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            <input type="hidden" name="type" value="pertanggal">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-field-app" required>
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-field-app" required>
            <button type="submit" class="btn-native btn-native-dark" style="height: 35px; padding: 7px 16px;">
                Terapkan
            </button>
            @if(request('type') == 'pertanggal')
                <a href="{{ route('admin.laporan.penjualan') }}" class="btn-native btn-native-white" style="height: 35px; padding: 7px 14px;">Reset</a>
            @endif
        </form>
    </div>

    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Total Transaksi</div>
                <div class="kpi-value">{{ $transaksis->count() }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Struk transaksi tercatat</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#166534" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Total Omset Pemasukan</div>
                <div class="kpi-value" style="color: #166534;">Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Penjualan bersih</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Total Item / Cup Terjual</div>
                <div class="kpi-value">{{ array_sum($itemsSold) }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Porsi / porsi menu laku</div>
            </div>
        </div>
    </div>

    <!-- Content Split Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Left Column: Transactions -->
        <div class="card-panel">
            <div class="card-panel-header">
                <h2 class="panel-title">
                    <svg width="17" height="17" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Riwayat Transaksi ({{ $transaksis->count() }})
                </h2>
                <div class="search-input-box">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="trx-search" placeholder="Cari invoice / pelanggan..." onkeyup="filterTrx()">
                </div>
            </div>
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Waktu Transaksi</th>
                            <th>No. Invoice</th>
                            <th>Pelanggan</th>
                            <th style="text-align: right;">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $t)
                        <tr class="trx-row" data-search="{{ strtolower($t->invoice_number . ' ' . ($t->customer_name ?: 'umum') . ' ' . $t->created_at->format('d/m/Y H:i')) }}">
                            <td>
                                <strong style="color: var(--dark-slate);">{{ $t->created_at->format('d/m/Y') }}</strong><br>
                                <span style="font-family: monospace; font-size: 12px; color: var(--text-muted);">{{ $t->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 12.5px;">
                                    #{{ $t->invoice_number }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $t->customer_name ?: 'Pelanggan Umum' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--dark-slate); font-size: 14px;">
                                Rp {{ number_format($t->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Tidak ada transaksi tercatat pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Products Sold -->
        <div class="card-panel">
            <div class="card-panel-header">
                <h2 class="panel-title">
                    <svg width="17" height="17" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Produk Terjual
                </h2>
                <span style="font-size: 12px; font-weight: 600; color: var(--text-muted);">{{ count($itemsSold) }} Varian</span>
            </div>
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th style="text-align: right;">Qty Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsSold as $name => $qty)
                        <tr>
                            <td style="font-weight: 600; color: var(--dark-slate);">{{ $name }}</td>
                            <td style="text-align: right;">
                                <span style="font-weight: 800; font-size: 13px; color: #0f172a; background: #f1f5f9; padding: 3px 10px; border-radius: 12px;">
                                    {{ $qty }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 36px; color: var(--text-muted);">
                                Belum ada produk terjual.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function filterTrx() {
        const query = document.getElementById('trx-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.trx-row');

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

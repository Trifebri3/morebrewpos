@extends('kasir.layouts.app')

@section('content')
<style>
    :root {
        --pure-black: #000000;
        --dark-slate: #0f172a;
        --border-subtle: #e2e8f0;
        --bg-subtle: #f8fafc;
        --text-muted: #64748b;
    }

    .kasir-report-container {
        padding: 24px 32px 60px;
        max-width: 1300px;
        margin: 0 auto;
    }

    /* Header Bar */
    .header-bar-app {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .title-group h1 {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-slate);
        letter-spacing: -0.4px;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-group p {
        color: var(--text-muted);
        font-size: 13.5px;
        margin: 0;
    }

    .actions-group {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
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
    .badge-date-pill {
        background: #f1f5f9;
        color: #0f172a;
        padding: 9px 16px;
        border-radius: 9px;
        font-weight: 600;
        font-size: 13px;
        border: 1px solid var(--border-subtle);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Summary Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .summary-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .summary-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--dark-slate);
        letter-spacing: -0.5px;
    }

    /* Table */
    .table-card-app {
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
        border-radius: 8px;
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
</style>

<div class="kasir-report-container">
    <!-- Header Bar -->
    <div class="header-bar-app">
        <div class="title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Laporan Kas & Penjualan Hari Ini
            </h1>
            <p>Rekapitulasi penjualan pos, item pesanan pelanggan, dan transaksi kasir hari ini.</p>
        </div>
        <div class="actions-group">
            <span class="badge-date-pill">
                <svg width="15" height="15" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Hari Ini: {{ date('d M Y') }}
            </span>
            <a href="{{ route('kasir.laporan.penjualan.export') }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Total Transaksi Selesai</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <div class="summary-value">{{ $riwayat->count() }} Transaksi</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">Struk terproses hari ini</div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Total Penjualan Bersih</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#166534" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="summary-value" style="color: #166534;">+ Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">Pemasukan kasir hari ini</div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="table-card-app">
        <div class="table-toolbar">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Detail Transaksi Penjualan Hari Ini
            </h2>

            <div class="search-input-box">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="kasir-trx-search" placeholder="Cari invoice atau pelanggan..." onkeyup="filterKasirTrx()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No Invoice</th>
                        <th>Pelanggan</th>
                        <th>Menu / Item Pesanan</th>
                        <th>Metode Bayar</th>
                        <th style="text-align: right;">Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $transaksi)
                    <tr class="trx-row" data-search="{{ strtolower($transaksi->invoice_number . ' ' . ($transaksi->customer_name ?? '') . ' ' . $transaksi->payment_method) }}">
                        <td>
                            <strong style="color: var(--dark-slate); font-family: monospace;">{{ $transaksi->created_at->format('H:i') }} WIB</strong>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 12.5px;">
                                #{{ $transaksi->invoice_number }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--dark-slate);">
                                {{ $transaksi->customer_name ?: 'Pelanggan Umum' }}
                            </div>
                            @if($transaksi->order_type === 'take_away')
                                <span style="font-size: 11px; color: #0284c7; font-weight: 600;">Take Away</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $itemsDesc = '';
                                if(is_array($transaksi->items)) {
                                    $itemStrings = array_map(function($item) {
                                        return ($item['qty'] ?? 1) . 'x ' . ($item['name'] ?? '');
                                    }, $transaksi->items);
                                    $itemsDesc = implode(', ', $itemStrings);
                                }
                            @endphp
                            <span style="color: var(--text-muted); font-size: 13px;">{{ Str::limit($itemsDesc, 55) }}</span>
                        </td>
                        <td>
                            <span style="font-size: 11.5px; font-weight: 700; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; color: #0f172a;">
                                {{ strtoupper($transaksi->payment_method) }}
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #166534; font-size: 14px;">
                            + Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Belum ada transaksi penjualan hari ini.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterKasirTrx() {
        const query = document.getElementById('kasir-trx-search').value.toLowerCase().trim();
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

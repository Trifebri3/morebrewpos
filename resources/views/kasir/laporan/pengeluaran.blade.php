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

    .pengeluaran-container {
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

    /* Financial Summary Grid */
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
    .summary-value.danger {
        color: #dc2626;
    }

    .budget-progress-track {
        height: 6px;
        border-radius: 3px;
        background: #f1f5f9;
        margin-top: 14px;
        overflow: hidden;
    }
    .budget-progress-fill {
        height: 100%;
        background: #0f172a;
        border-radius: 3px;
        transition: width 0.3s;
    }

    /* Table Card */
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

    /* Badges */
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
</style>

<div class="pengeluaran-container">
    <!-- Header Bar -->
    <div class="header-bar-app">
        <div class="title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Laporan Belanja & Kas Keluar
            </h1>
            <p>Rekapitulasi pengeluaran kasir untuk pembelian belanja keperluan kedai hari ini.</p>
        </div>
        <div class="actions-group">
            <span class="badge-date-pill">
                <svg width="15" height="15" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Hari Ini: {{ date('d M Y') }}
            </span>
            <a href="{{ route('kasir.laporan.pengeluaran.export') }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
            <a href="{{ route('kasir.pengeluaran') }}" class="btn-native btn-native-dark">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Catat Belanja Baru
            </a>
        </div>
    </div>

    <!-- Financial Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Alokasi Kas Harian</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div class="summary-value">Rp {{ number_format($budget, 0, ',', '.') }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">Batas pagu operasional kasir</div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Total Belanja (Keluar)</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
            <div class="summary-value danger">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">{{ $riwayat->count() }} transaksi belanja dicatat</div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Sisa Saldo Kas Belanja</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="summary-value">Rp {{ number_format($sisaBudget, 0, ',', '.') }}</div>
            @php
                $pct = $budget > 0 ? min(100, round(($totalPengeluaran / $budget) * 100)) : 0;
            @endphp
            <div class="budget-progress-track">
                <div class="budget-progress-fill" style="width: {{ $pct }}%; background: {{ $pct > 85 ? '#dc2626' : '#0f172a' }};"></div>
            </div>
        </div>
    </div>

    <!-- Table of Expenses -->
    <div class="table-card-app">
        <div class="table-toolbar">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Rincian Pengeluaran Kasir Hari Ini
            </h2>

            <div class="search-input-box">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="expense-quick-search" placeholder="Cari item atau catatan..." onkeyup="filterExpenses()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="app-table" id="expenses-table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Keperluan / Item Belanja</th>
                        <th>Keterangan / Catatan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Nominal Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr class="expense-row" data-search="{{ strtolower($item->nama_item . ' ' . $item->keterangan . ' ' . $item->status) }}">
                        <td>
                            <strong style="color: var(--dark-slate); font-weight: 600;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</strong><br>
                            <span style="font-family: monospace; font-size: 12px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i') }} WIB</span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--dark-slate); font-size: 14px;">{{ $item->nama_item }}</div>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 13px;">{{ $item->keterangan ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $item->status }}">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span style="font-weight: 700; color: #dc2626; font-size: 14px;">
                                - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Belum ada catatan pengeluaran belanja untuk hari ini.</span>
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
    function filterExpenses() {
        const query = document.getElementById('expense-quick-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.expense-row');

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

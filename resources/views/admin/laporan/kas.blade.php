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

    .laporan-kas-container {
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
    .btn-native-white:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
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

    /* Filter Form */
    .filter-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .form-group-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .form-group-item label {
        font-size: 11.5px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .input-field-app {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s;
    }
    .input-field-app:focus {
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }

    /* Table Panel */
    .table-app-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .table-app-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    .badge-surplus { background: #dcfce7; color: #166534; }
    .badge-defisit { background: #fee2e2; color: #991b1b; }
</style>

<div class="laporan-kas-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Laporan Arus Kas (Cash Flow)
            </h1>
            <p>Rekapitulasi uang kas masuk dari penjualan tunai, modal buka laci, pengeluaran kasir, dan saldo kas bersih.</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan.kas.export', request()->query()) }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#166534" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Kas Masuk (Penjualan Tunai)</div>
                <div class="kpi-value" style="color: #166534;">Rp {{ number_format($totalKasMasuk ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Uang masuk laci kasir</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Kas Keluar (Pengeluaran)</div>
                <div class="kpi-value" style="color: #dc2626;">- Rp {{ number_format($totalKasKeluar ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Belanja keperluan kedai</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <div class="kpi-label">Arus Kas Bersih (Net)</div>
                <div class="kpi-value" style="color: {{ ($totalSaldoBersih ?? 0) >= 0 ? '#0f172a' : '#dc2626' }};">
                    Rp {{ number_format($totalSaldoBersih ?? 0, 0, ',', '.') }}
                </div>
                <div style="font-size: 11px; color: #94a3b8;">Surplus / defisit periode</div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.laporan.kas') }}">
            <div class="filter-row">
                <div class="form-group-item">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="input-field-app">
                </div>
                <div class="form-group-item">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="input-field-app">
                </div>
                <div>
                    <button type="submit" class="btn-native btn-native-dark" style="height: 35px;">
                        <svg width="14" height="14" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Kas
                    </button>
                </div>
                @if($startDate || $endDate)
                <div>
                    <a href="{{ route('admin.laporan.kas') }}" class="btn-native btn-native-white" style="height: 35px;">
                        Reset
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-app-card">
        <div class="table-app-header">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Rincian Arus Kas Harian ({{ count($kasRows ?? []) }} Hari Tercatat)
            </h2>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Penjualan Tunai</th>
                        <th>Modal Awal Kasir</th>
                        <th>Pengeluaran Kasir</th>
                        <th style="text-align: right;">Saldo Bersih Harian</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kasRows ?? [] as $row)
                    <tr>
                        <td style="font-weight: 600; color: var(--dark-slate);">
                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                        </td>
                        <td style="color: #166534; font-weight: 600;">
                            + Rp {{ number_format($row->pemasukan, 0, ',', '.') }}
                        </td>
                        <td style="color: var(--text-muted); font-weight: 500;">
                            Rp {{ number_format($row->modal, 0, ',', '.') }}
                        </td>
                        <td style="color: #dc2626; font-weight: 600;">
                            - Rp {{ number_format($row->pengeluaran, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-weight: 800; color: {{ $row->saldo_bersih >= 0 ? 'var(--dark-slate)' : '#dc2626' }};">
                            Rp {{ number_format($row->saldo_bersih, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right;">
                            <span class="badge-status {{ $row->saldo_bersih >= 0 ? 'badge-surplus' : 'badge-defisit' }}">
                                {{ strtoupper($row->status ?? 'SURPLUS') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            Belum ada catatan arus kas pada rentang tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

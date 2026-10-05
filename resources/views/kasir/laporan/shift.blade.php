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

    .kasir-shift-container {
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
        font-size: 24px;
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
    .badge-buka { background: #dbeafe; color: #1e40af; }
    .badge-tutup { background: #f1f5f9; color: #475569; }
</style>

<div class="kasir-shift-container">
    <!-- Header Bar -->
    <div class="header-bar-app">
        <div class="title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Laporan Sesi Shift Kasir
            </h1>
            <p>Rekapitulasi riwayat pembukaan laci kasir, penjualan shift, dan setoran fisik laci.</p>
        </div>
        <div class="actions-group">
            <span class="badge-date-pill">
                <svg width="15" height="15" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Hari Ini: {{ date('d M Y') }}
            </span>
            <a href="{{ route('kasir.laporan.shift.export') }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
            <a href="{{ route('kasir.tutup') }}" class="btn-native btn-native-dark">
                Kelola Shift (Buka / Tutup)
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Status Shift Sekarang</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <div class="summary-value" style="color: {{ isset($activeSession) && $activeSession ? '#166534' : '#64748b' }};">
                {{ isset($activeSession) && $activeSession ? 'Shift Aktif' : 'Shift Tertutup' }}
            </div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">
                {{ isset($activeSession) && $activeSession ? 'Sedang melayani transaksi' : 'Belum buka laci kasir' }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Modal Awal Sesi Aktif</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div class="summary-value">Rp {{ number_format($activeSession ? $activeSession->modal_awal : 0, 0, ',', '.') }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">Kas uang kecil di laci</div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <span class="summary-label">Total Riwayat Sesi Saya</span>
                <div class="summary-icon-box">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <div class="summary-value">{{ count($sesis ?? []) }} Sesi</div>
            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">Total seluruh shift kasir</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card-app">
        <div class="table-toolbar">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Riwayat Sesi Shift Kasir
            </h2>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>ID Sesi</th>
                        <th>Waktu Buka</th>
                        <th>Waktu Tutup</th>
                        <th>Modal Awal</th>
                        <th>Total Penjualan</th>
                        <th>Uang Fisik</th>
                        <th>Selisih</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesis as $s)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #0f172a;">
                            SESI-{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="font-size: 13px;">{{ $s->waktu_buka ? $s->waktu_buka->format('d M Y H:i') : '-' }}</td>
                        <td style="font-size: 13px;">{{ $s->waktu_tutup ? $s->waktu_tutup->format('d M Y H:i') : 'Sedang Berjalan' }}</td>
                        <td style="font-weight: 600;">Rp {{ number_format($s->modal_awal ?? 0, 0, ',', '.') }}</td>
                        <td style="color: #166534; font-weight: 700;">Rp {{ number_format($s->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                        <td style="font-weight: 600;">Rp {{ number_format($s->uang_fisik ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @php $diff = $s->selisih ?? 0; @endphp
                            <span style="font-weight: 800; color: {{ $diff == 0 ? '#166534' : ($diff < 0 ? '#dc2626' : '#0f172a') }};">
                                {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="badge-status {{ $s->status === 'buka' ? 'badge-buka' : 'badge-tutup' }}">
                                {{ strtoupper($s->status ?? 'BUKA') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Belum ada riwayat sesi kasir.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

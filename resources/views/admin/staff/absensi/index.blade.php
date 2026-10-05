@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    :root {
        --pure-black: #000000;
        --dark-slate: #0f172a;
        --border-subtle: #e2e8f0;
        --bg-subtle: #f8fafc;
    }

    .absensi-page-container {
        padding: 28px 36px 80px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Top Page Header */
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

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-app {
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
    .btn-app-dark {
        background: var(--pure-black);
        color: #ffffff;
        border-color: var(--pure-black);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .btn-app-dark:hover {
        background: #1e293b;
        color: #ffffff;
    }
    .btn-app-white {
        background: #ffffff;
        color: #0f172a;
    }
    .btn-app-white:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    /* Cards */
    .app-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .app-card:hover {
        border-color: #cbd5e1;
    }

    .app-card-header {
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .app-card-title {
        font-size: 15.5px;
        font-weight: 700;
        color: var(--dark-slate);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Shift Monitoring Board */
    .shift-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        padding: 20px 24px;
        background: #fafafa;
        border-bottom: 1px solid var(--border-subtle);
    }

    .shift-box {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid var(--border-subtle);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .shift-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .shift-box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .shift-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }
    .shift-time {
        font-size: 11.5px;
        font-family: monospace;
        color: #64748b;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
    }

    .progress-bar-bg {
        height: 8px;
        border-radius: 4px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: #10b981;
        border-radius: 4px;
        transition: width 0.3s;
    }

    /* KPI Summary Stats */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

    .kpi-info-box .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    .kpi-info-box .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-info-box .kpi-subtext {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* Filter Toolbar */
    .filter-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: center;
        justify-content: space-between;
    }

    .filter-presets {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .preset-pill {
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border-subtle);
        color: #475569;
        background: #ffffff;
        transition: all 0.15s;
    }
    .preset-pill:hover, .preset-pill.active {
        background: var(--dark-slate);
        color: #ffffff;
        border-color: var(--dark-slate);
    }

    .search-input-box {
        position: relative;
        flex: 1;
        min-width: 240px;
        max-width: 380px;
    }
    .search-input-box input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        border-radius: 10px;
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
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* App-style Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    .app-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        text-align: left;
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

    /* User Avatar */
    .avatar-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #0f172a;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* Badges */
    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .pill-masuk {
        background: #dcfce7;
        color: #166534;
    }
    .pill-keluar {
        background: #fef3c7;
        color: #92400e;
    }

    .status-chip {
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-tepat {
        color: #15803d;
    }
    .status-telat {
        color: #dc2626;
    }
    .status-luar {
        color: #d97706;
    }

    /* Photo Thumb */
    .selfie-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
    }
    .selfie-thumb:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    /* Modal Lightbox */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 18px;
        max-width: 520px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 25px 30px -5px rgba(0,0,0,0.25);
        animation: modalScale 0.2s ease-out;
    }
    @keyframes modalScale {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div class="absensi-page-container">
    <!-- Top Header Banner -->
    <div class="header-banner">
        <div class="header-title-box">
            <h1>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Rekapan Absensi & Monitoring Kehadiran</span>
            </h1>
            <p>Pantau kehadiran karyawan, validasi bukti selfie, toleransi keterlambatan shift, dan verifikasi geofencing GPS.</p>
        </div>
        <div class="header-actions">
            <button type="button" onclick="openQrScannerModal()" class="btn-app btn-app-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
                <span>Scan QR Masuk/Keluar</span>
            </button>
            <a href="{{ route('admin.staff.absensi.export', request()->query()) }}" class="btn-app btn-app-dark">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor Excel (.xlsx)</span>
            </a>
            <a href="#pengaturan-geofencing" class="btn-app btn-app-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Pengaturan GPS</span>
            </a>
        </div>
    </div>

    @if (session('success'))
    <div style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 14px 18px; border-radius: 10px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 500;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- LIVE SHIFT MONITORING BOARD (HARI INI) -->
    @if(isset($recap['shifts']) && count($recap['shifts']) > 0)
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Monitoring Shift Hari Ini: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </h2>
            <div style="font-size: 12px; font-weight: 700; color: #475569; background: #f1f5f9; padding: 4px 12px; border-radius: 20px;">
                Total Hadir: {{ $recap['hadir'] ?? 0 }} / {{ $recap['total_karyawan'] ?? 0 }} Karyawan
            </div>
        </div>
        <div class="shift-grid">
            @foreach($recap['shifts'] as $sh)
            @php
                $pct = $sh['total_karyawan'] > 0 ? round(($sh['hadir'] / $sh['total_karyawan']) * 100) : 0;
            @endphp
            <div class="shift-box">
                <div class="shift-box-header">
                    <span class="shift-name">{{ $sh['name'] }}</span>
                    <span class="shift-time">{{ $sh['jam'] }}</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px;">
                        <span style="color: #64748b;">Kehadiran Staf:</span>
                        <strong style="color: #0f172a;">{{ $sh['hadir'] }} / {{ $sh['total_karyawan'] }} ({{ $pct }}%)</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11.5px; border-top: 1px dashed var(--border-subtle); padding-top: 6px;">
                    <span style="color: {{ $sh['terlambat'] > 0 ? '#dc2626' : '#64748b' }}; font-weight: 600;">
                        {{ $sh['terlambat'] > 0 ? '⚠ Terlambat: ' . $sh['terlambat'] . ' orang' : '✓ Tidak ada keterlambatan' }}
                    </span>
                    <span class="pill-badge" style="background: {{ $sh['hadir'] >= $sh['total_karyawan'] && $sh['total_karyawan'] > 0 ? '#dcfce7; color: #166534;' : '#f1f5f9; color: #475569;' }}">
                        {{ $sh['hadir'] >= $sh['total_karyawan'] && $sh['total_karyawan'] > 0 ? 'Lengkap' : 'Sedang Berjalan' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Total Data Absen</div>
                <div class="kpi-value">{{ $filterStats['total'] ?? 0 }}</div>
                <div class="kpi-subtext">Record pada periode ini</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box" style="background: #f0fdf4;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Tepat Waktu</div>
                <div class="kpi-value" style="color: #15803d;">{{ $filterStats['tepat_waktu'] ?? 0 }}</div>
                <div class="kpi-subtext">Disiplin sesuai jam shift</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box" style="background: #fef2f2;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Terlambat</div>
                <div class="kpi-value" style="color: #dc2626;">{{ $filterStats['terlambat'] ?? 0 }}</div>
                <div class="kpi-subtext">Melewati batas shift</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box" style="background: #faf5ff;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Dengan Foto Selfie</div>
                <div class="kpi-value" style="color: #7c3aed;">{{ $filterStats['dengan_foto'] ?? 0 }}</div>
                <div class="kpi-subtext">Verifikasi kamera aktif</div>
            </div>
        </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="filter-card">
        <div class="filter-presets">
            <a href="{{ route('admin.staff.absensi', array_merge(request()->except(['range', 'start_date', 'end_date']), ['range' => 'hari_ini'])) }}" class="preset-pill {{ $range === 'hari_ini' ? 'active' : '' }}">Hari Ini</a>
            <a href="{{ route('admin.staff.absensi', array_merge(request()->except(['range', 'start_date', 'end_date']), ['range' => '7_hari'])) }}" class="preset-pill {{ $range === '7_hari' ? 'active' : '' }}">7 Hari</a>
            <a href="{{ route('admin.staff.absensi', array_merge(request()->except(['range', 'start_date', 'end_date']), ['range' => 'bulan_ini'])) }}" class="preset-pill {{ $range === 'bulan_ini' ? 'active' : '' }}">Bulan Ini</a>
            <a href="{{ route('admin.staff.absensi', array_merge(request()->except(['range', 'start_date', 'end_date']), ['range' => 'semua'])) }}" class="preset-pill {{ $range === 'semua' ? 'active' : '' }}">Semua</a>
        </div>

        <!-- Realtime Client-side Search -->
        <div class="search-input-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="live-table-search" placeholder="Cari nama staff, status, atau jam..." autocomplete="off">
        </div>

        <!-- Dropdown Form Filters -->
        <form method="GET" action="{{ route('admin.staff.absensi') }}" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="range" value="{{ $range }}">

            <select name="user_id" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border-subtle); font-size: 12.5px; background: white; font-weight: 500;">
                <option value="">Semua Karyawan</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border-subtle); font-size: 12.5px; background: white; font-weight: 500;">
                <option value="">Semua Status</option>
                <option value="tepat_waktu" {{ $statusFilter === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                <option value="terlambat" {{ $statusFilter === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="luar_zona" {{ $statusFilter === 'luar_zona' ? 'selected' : '' }}>Luar Zona</option>
            </select>

            @if($range === 'custom')
            <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 7px 10px; border-radius: 8px; border: 1px solid var(--border-subtle); font-size: 12px;">
            <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 7px 10px; border-radius: 8px; border: 1px solid var(--border-subtle); font-size: 12px;">
            <button type="submit" class="btn-app btn-app-dark" style="padding: 7px 12px; font-size: 12px;">Filter</button>
            @endif
        </form>
    </div>

    <!-- MAIN ATTENDANCE TABLE CARD -->
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <span>Daftar Rekap Absensi (<span id="total-visible-count">{{ $absensis->count() }}</span> Record)</span>
            </h2>
            <div style="font-size: 12px; color: #64748b;">
                Klik bukti foto untuk melihat informasi lengkap & koordinat GPS
            </div>
        </div>

        <div class="table-responsive">
            <table class="app-table" id="absensi-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Waktu & Tanggal</th>
                        <th>Tipe Absen</th>
                        <th>Status Kehadiran</th>
                        <th>Bukti Selfie</th>
                        <th>Verifikasi GPS</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensis as $a)
                    @php
                        $photoUrl = $a->photo_path ? Storage::url($a->photo_path) : null;
                        $initial = strtoupper(substr($a->user->name ?? 'K', 0, 1));
                    @endphp
                    <tr class="table-row-item" data-search="{{ strtolower(($a->user->name ?? '') . ' ' . ($a->user->role ?? '') . ' ' . $a->type . ' ' . $a->status . ' ' . $a->created_at->format('d M Y H:i')) }}">
                        <td>
                            <div class="avatar-cell">
                                <div class="avatar-circle">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $a->user ? $a->user->name : 'Staff' }}</div>
                                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">{{ $a->user ? $a->user->role : 'Staff' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 14px; font-weight: 800; color: #0f172a;">{{ $a->created_at->format('H:i:s') }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">WIB</span></div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 1px;">{{ $a->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="pill-badge {{ $a->type === 'Masuk' ? 'pill-masuk' : 'pill-keluar' }}">
                                @if($a->type === 'Masuk')
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                @else
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                @endif
                                <span>{{ $a->type }}</span>
                            </span>
                        </td>
                        <td>
                            <span class="status-chip {{ str_contains($a->status, 'Terlambat') ? 'status-telat' : (str_contains($a->status, 'Luar') ? 'status-luar' : 'status-tepat') }}">
                                <span>●</span>
                                <span>{{ $a->status }}</span>
                            </span>
                        </td>
                        <td>
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="Selfie" class="selfie-thumb" 
                                     onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user->name ?? 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }} WIB', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')"
                                     onerror="this.src='https://placehold.co/80x80/transparent/000000?text=Foto'">
                            @else
                                <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">Tanpa Foto</span>
                            @endif
                        </td>
                        <td>
                            @if($a->latitude && $a->longitude)
                                <a href="https://maps.google.com/?q={{ $a->latitude }},{{ $a->longitude }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 6px; font-size: 12px; font-weight: 600; color: #0f172a; text-decoration: none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>{{ round($a->latitude, 4) }}, {{ round($a->longitude, 4) }}</span>
                                </a>
                            @else
                                <span style="font-size: 12px; color: #94a3b8;">Tidak Terlacak</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <button type="button" onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user->name ?? 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }} WIB', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')" class="btn-app btn-app-white" style="padding: 6px 12px; font-size: 12px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: #64748b;">
                            <div style="font-size: 28px; margin-bottom: 8px;">📋</div>
                            <div style="font-weight: 700; font-size: 15px; color: #0f172a;">Belum Ada Data Absensi</div>
                            <div style="font-size: 12.5px; color: #64748b; margin-top: 4px;">Tidak ada catatan kehadiran pada filter tanggal yang dipilih.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PENGATURAN TIPE & GEOFENCING ABSENSI -->
    <div class="app-card" id="pengaturan-geofencing" style="margin-top: 36px;">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Pengaturan Akses & Radius Geofencing Kedai</span>
            </h2>
            <a href="{{ route('admin.kedai.pengaturan') }}" style="font-size: 12.5px; font-weight: 600; color: #0f172a; text-decoration: underline;">
                Buka Pengaturan Lengkap Kedai & Peta →
            </a>
        </div>
        <div style="padding: 24px;">
            <form action="{{ route('admin.staff.absensi.settings.update') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px; margin-bottom: 20px;">
                    <!-- Option 1: QR Absen Kasir -->
                    <div style="border: 1px solid var(--border-subtle); border-radius: 12px; padding: 18px; display: flex; justify-content: space-between; align-items: flex-start; gap: 14px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0f172a;">Absensi QR Code (Terminal Kasir)</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 3px; line-height: 1.4;">Karyawan scan kartu QR pribadi di layar POS kasir saat memulai atau mengakhiri shift.</div>
                        </div>
                        <input type="checkbox" name="is_qr_absen_enabled" value="1" {{ (isset($kedai) && $kedai->is_qr_absen_enabled) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #000000; cursor: pointer;">
                    </div>

                    <!-- Option 2: Link Mandiri Smartphone -->
                    <div style="border: 1px solid var(--border-subtle); border-radius: 12px; padding: 18px; display: flex; justify-content: space-between; align-items: flex-start; gap: 14px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0f172a;">Absensi Tautan HP Mandiri (/absen)</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 3px; line-height: 1.4;">Karyawan membuka tautan di browser HP masing-masing dengan verifikasi GPS + Selfie.</div>
                            <div style="margin-top: 8px;">
                                <a href="{{ url('/absen') }}" target="_blank" style="font-family: monospace; font-size: 11.5px; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; text-decoration: none;">
                                    {{ url('/absen') }} ↗
                                </a>
                            </div>
                        </div>
                        <input type="checkbox" name="is_link_absen_enabled" value="1" {{ (isset($kedai) && $kedai->is_link_absen_enabled) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #000000; cursor: pointer;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Latitude Kedai</label>
                        <input type="text" name="latitude" value="{{ $kedai->latitude ?? '' }}" placeholder="-6.921477" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-subtle); border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Longitude Kedai</label>
                        <input type="text" name="longitude" value="{{ $kedai->longitude ?? '' }}" placeholder="107.616654" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-subtle); border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Radius Absensi (Meter)</label>
                        <input type="number" name="radius_meter" value="{{ $kedai->radius_meter ?? 50 }}" placeholder="50" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-subtle); border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn-app btn-app-dark" style="padding: 10px 24px;">
                        Simpan Pengaturan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL LIGHTBOX DETAIL HASIL & BUKTI FOTO ABSENSI -->
<div id="photo-modal" class="modal-backdrop" onclick="if(event.target === this) closePhotoModal()">
    <div class="modal-box">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 15.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <span>Detail Bukti Absensi Staff</span>
            </h3>
            <button type="button" onclick="closePhotoModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div style="padding: 22px;">
            <div id="modal-img-container" style="background: #f8fafc; border-radius: 12px; overflow: hidden; margin-bottom: 18px; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; min-height: 240px; max-height: 360px;">
                <img id="modal-img" src="" alt="Bukti Foto Selfie" style="width: 100%; height: auto; max-height: 360px; object-fit: contain;">
            </div>

            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid var(--border-subtle);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 13px;">
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700;">Nama Karyawan</div>
                        <strong id="modal-user" style="color: #0f172a; font-size: 14px;">-</strong>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700;">Waktu & Tanggal</div>
                        <span id="modal-time" style="color: #0f172a; font-weight: 600;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700;">Tipe & Status</div>
                        <span id="modal-type-status" style="font-weight: 700;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700;">Lokasi Koordinat GPS</div>
                        <a id="modal-map-link" href="#" target="_blank" style="color: #0f172a; font-weight: 700; text-decoration: underline; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                            Buka di Google Maps ↗
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 14px 22px; background: #f8fafc; border-top: 1px solid var(--border-subtle); text-align: right;">
            <button type="button" onclick="closePhotoModal()" class="btn-app btn-app-dark" style="padding: 8px 22px;">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- IN-PAGE QR CAMERA SCANNER MODAL -->
<div id="qr-scanner-modal" class="modal-backdrop" onclick="if(event.target === this) closeQrScannerModal()">
    <div class="modal-box" style="max-width: 440px;">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 15.5px; font-weight: 700; color: #0f172a;">
                Scan QR Code Absensi Staff
            </h3>
            <button type="button" onclick="closeQrScannerModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div style="padding: 22px; text-align: center;">
            <div id="reader-admin" style="width: 100%; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border-subtle);"></div>
            <div id="scan-status-alert" style="margin-top: 14px; padding: 12px; border-radius: 8px; font-size: 13px; font-weight: 600; display: none;"></div>
            <p style="margin: 12px 0 0 0; font-size: 12px; color: #64748b;">
                Dekatkan kartu QR karyawan ke depan kamera untuk Clock In / Out instan.
            </p>
        </div>
        <div style="padding: 14px 22px; background: #f8fafc; border-top: 1px solid var(--border-subtle); text-align: right;">
            <button type="button" onclick="closeQrScannerModal()" class="btn-app btn-app-white">
                Selesai
            </button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // 1. Realtime Instant Client-side Table Filter
    const liveSearch = document.getElementById('live-table-search');
    const tableRows = document.querySelectorAll('.table-row-item');
    const visibleCounter = document.getElementById('total-visible-count');

    if (liveSearch) {
        liveSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visible = 0;

            tableRows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                if (!query || text.includes(query)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCounter) {
                visibleCounter.innerText = visible;
            }
        });
    }

    // 2. Photo Lightbox Modal
    function openPhotoModal(imgUrl, userName, timeStr, type, status, lat, lon) {
        const modalImg = document.getElementById('modal-img');
        const modalImgCont = document.getElementById('modal-img-container');
        
        if (imgUrl && imgUrl !== 'null') {
            modalImg.src = imgUrl;
            modalImg.style.display = 'block';
            modalImgCont.style.display = 'flex';
        } else {
            modalImg.style.display = 'none';
            modalImgCont.style.display = 'none';
        }

        document.getElementById('modal-user').innerText = userName;
        document.getElementById('modal-time').innerText = timeStr;
        document.getElementById('modal-type-status').innerHTML = '<span style="color: ' + (type === 'Masuk' ? '#166534' : '#92400e') + '">' + type + '</span> • ' + status;

        const mapLink = document.getElementById('modal-map-link');
        if (lat && lon && lat !== 'null') {
            mapLink.href = 'https://maps.google.com/?q=' + lat + ',' + lon;
            mapLink.innerHTML = lat + ', ' + lon + ' ↗';
            mapLink.style.display = 'inline-flex';
        } else {
            mapLink.style.display = 'none';
        }

        document.getElementById('photo-modal').style.display = 'flex';
    }

    function closePhotoModal() {
        document.getElementById('photo-modal').style.display = 'none';
    }

    // 3. QR Camera Scanner Modal
    let html5QrScanner = null;
    let isProcessingScan = false;

    function openQrScannerModal() {
        document.getElementById('qr-scanner-modal').style.display = 'flex';
        const alertBox = document.getElementById('scan-status-alert');
        alertBox.style.display = 'none';
        isProcessingScan = false;

        setTimeout(() => {
            if (!html5QrScanner) {
                html5QrScanner = new Html5QrcodeScanner("reader-admin", { fps: 10, qrbox: 240 });
                html5QrScanner.render(onScanQrSuccess);
            }
        }, 200);
    }

    function closeQrScannerModal() {
        document.getElementById('qr-scanner-modal').style.display = 'none';
        if (html5QrScanner) {
            html5QrScanner.clear().catch(err => console.log(err));
            html5QrScanner = null;
        }
    }

    function onScanQrSuccess(decodedText) {
        if (isProcessingScan) return;
        isProcessingScan = true;

        const alertBox = document.getElementById('scan-status-alert');
        alertBox.style.display = 'block';
        alertBox.style.background = '#fef3c7';
        alertBox.style.color = '#92400e';
        alertBox.innerText = 'Memproses data absensi...';

        fetch("{{ route('admin.staff.absensi.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alertBox.style.background = '#dcfce7';
                alertBox.style.color = '#166534';
                alertBox.innerText = data.message;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                alertBox.style.background = '#fee2e2';
                alertBox.style.color = '#991b1b';
                alertBox.innerText = data.message || 'Gagal memproses absensi.';
                setTimeout(() => {
                    isProcessingScan = false;
                    alertBox.style.display = 'none';
                }, 3000);
            }
        })
        .catch(err => {
            alertBox.style.background = '#fee2e2';
            alertBox.style.color = '#991b1b';
            alertBox.innerText = 'Terjadi kesalahan sistem.';
            setTimeout(() => {
                isProcessingScan = false;
                alertBox.style.display = 'none';
            }, 3000);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoModal();
            closeQrScannerModal();
        }
    });
</script>
@endsection

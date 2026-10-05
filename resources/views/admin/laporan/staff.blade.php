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

    .laporan-staff-container {
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

    .app-actions-group {
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
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    .kpi-info-box .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--dark-slate);
        line-height: 1.2;
    }
    .kpi-info-box .kpi-subtext {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* Filter & Controls Card */
    .controls-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }

    .preset-pills-row {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .preset-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-right: 4px;
    }

    .pill-btn {
        background: #f8fafc;
        border: 1px solid var(--border-subtle);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
    }
    .pill-btn:hover {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    .filter-fields-row {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
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
        padding: 9px 12px;
        border-radius: 9px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-field-app:focus {
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }

    /* Live Instant Search Bar */
    .instant-search-container {
        position: relative;
        flex: 1;
        min-width: 220px;
    }
    .instant-search-container input {
        width: 100%;
        padding: 9px 12px 9px 34px;
        border-radius: 9px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }
    .instant-search-container input:focus {
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }
    .instant-search-container svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* App-style Table Card */
    .table-app-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .table-app-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
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

    /* Photo Thumbnail */
    .photo-thumb-app {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--border-subtle);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .photo-thumb-app:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .photo-placeholder-app {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
    }

    /* Badges */
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-masuk {
        background: #dcfce7;
        color: #166534;
    }
    .badge-keluar {
        background: #fef9c3;
        color: #854d0e;
    }
    .badge-tepat {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .badge-telat {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .badge-luar {
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #ffe4e6;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .modal-content-app {
        background: #ffffff;
        border-radius: 16px;
        max-width: 520px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
    }
</style>

<div class="laporan-staff-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Rekapan & Laporan Absensi Staff
            </h1>
            <p>Rekapitulasi lengkap riwayat presensi karyawan, ketepatan waktu kerja, bukti swafoto, dan koordinat GPS.</p>
        </div>
        <div class="app-actions-group">
            <a href="{{ route('admin.staff.absensi') }}" class="btn-native btn-native-white">
                <svg width="15" height="15" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Presensi Hari Ini
            </a>
            <a href="{{ route('admin.laporan.staff.export', request()->query()) }}" class="btn-native btn-native-green">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Total Kehadiran</div>
                <div class="kpi-value">{{ $totalAbsen ?? 0 }}</div>
                <div class="kpi-subtext">Semua log periode ini</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Tepat Waktu</div>
                <div class="kpi-value" style="color: #166534;">{{ $totalTepatWaktu ?? 0 }}</div>
                <div class="kpi-subtext">
                    {{ $totalAbsen > 0 ? round(($totalTepatWaktu / $totalAbsen) * 100) : 0 }}% tingkat kepatuhan
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Terlambat</div>
                <div class="kpi-value" style="color: #dc2626;">{{ $totalTerlambat ?? 0 }}</div>
                <div class="kpi-subtext">Perlu evaluasi disiplin</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box">
                <svg width="22" height="22" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-label">Foto Terverifikasi</div>
                <div class="kpi-value">{{ $totalDenganFoto ?? 0 }}</div>
                <div class="kpi-subtext">Swafoto tervalidasi</div>
            </div>
        </div>
    </div>

    <!-- Filter & Presets Controls -->
    <div class="controls-card">
        <!-- Date Preset Quick Buttons -->
        <div class="preset-pills-row">
            <span class="preset-label">Rentang Cepat:</span>
            <button type="button" class="pill-btn" onclick="applyDatePreset('today')">Hari Ini</button>
            <button type="button" class="pill-btn" onclick="applyDatePreset('yesterday')">Kemarin</button>
            <button type="button" class="pill-btn" onclick="applyDatePreset('7days')">7 Hari Terakhir</button>
            <button type="button" class="pill-btn" onclick="applyDatePreset('30days')">30 Hari Terakhir</button>
            <button type="button" class="pill-btn" onclick="applyDatePreset('thisMonth')">Bulan Ini</button>
        </div>

        <form id="filter-form" method="GET" action="{{ route('admin.laporan.staff') }}">
            <div class="filter-fields-row">
                <div class="form-group-item">
                    <label>Pilih Karyawan</label>
                    <select name="user_id" class="input-field-app" style="min-width: 170px;">
                        <option value="">Semua Karyawan</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item">
                    <label>Status Kehadiran</label>
                    <select name="status" class="input-field-app" style="min-width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="tepat_waktu" {{ ($status ?? '') === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="terlambat" {{ ($status ?? '') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="luar_zona" {{ ($status ?? '') === 'luar_zona' ? 'selected' : '' }}>Luar Zona</option>
                    </select>
                </div>

                <div class="form-group-item">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="start_date_input" name="start_date" value="{{ $startDate ?? '' }}" class="input-field-app">
                </div>

                <div class="form-group-item">
                    <label>Tanggal Selesai</label>
                    <input type="date" id="end_date_input" name="end_date" value="{{ $endDate ?? '' }}" class="input-field-app">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-native btn-native-dark" style="height: 38px;">
                        <svg width="14" height="14" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter
                    </button>
                    @if(request('start_date') || request('end_date') || request('user_id') || request('status'))
                    <a href="{{ route('admin.laporan.staff') }}" class="btn-native btn-native-white" style="height: 38px;">
                        Reset
                    </a>
                    @endif
                </div>

                <!-- Instant Live Filter Client-Side -->
                <div class="instant-search-container">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="instant-table-search" placeholder="Cari cepat nama, jam, status..." onkeyup="filterTableRealtime()">
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Records -->
    <div class="table-app-card">
        <div class="table-app-header">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Log Riwayat Absensi Karyawan
            </h2>
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;" id="table-count-label">
                Menampilkan {{ $absensis->count() }} data
            </span>
        </div>

        <div class="table-responsive">
            <table class="app-table" id="absensi-table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Foto Selfie</th>
                        <th>Karyawan</th>
                        <th>Sesi</th>
                        <th>Status</th>
                        <th>Lokasi GPS</th>
                        <th style="text-align: right;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensis as $a)
                    <tr class="searchable-row" data-search="{{ strtolower(($a->user ? $a->user->name : '') . ' ' . $a->type . ' ' . $a->status . ' ' . $a->created_at->format('d M Y H:i')) }}">
                        <td>
                            <strong style="color: var(--dark-slate); font-weight: 600;">{{ $a->created_at->format('d M Y') }}</strong><br>
                            <span style="font-family: monospace; font-size: 12px; color: var(--text-muted);">{{ $a->created_at->format('H:i:s') }} WIB</span>
                        </td>
                        <td>
                            @if($a->photo_path)
                                @php $photoUrl = Storage::url($a->photo_path); @endphp
                                <img src="{{ $photoUrl }}" alt="Selfie" class="photo-thumb-app" 
                                     onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user ? $a->user->name : 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }} WIB', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')"
                                     onerror="this.src='https://placehold.co/80x80/f1f5f9/64748b?text=Foto'">
                            @else
                                <div class="photo-placeholder-app">
                                    No Pic
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: #0f172a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;">
                                    {{ strtoupper(substr($a->user ? $a->user->name : 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark-slate);">{{ $a->user ? $a->user->name : 'Karyawan' }}</div>
                                    <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">{{ $a->user ? $a->user->role : 'Staff' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $a->type == 'Masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                                @if($a->type == 'Masuk')
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                @else
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                @endif
                                {{ $a->type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = 'badge-tepat';
                                if (str_contains($a->status, 'Terlambat')) $statusClass = 'badge-telat';
                                elseif (str_contains($a->status, 'Luar Zona')) $statusClass = 'badge-luar';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $a->status }}
                            </span>
                        </td>
                        <td>
                            @if($a->latitude && $a->longitude)
                                <a href="https://maps.google.com/?q={{ $a->latitude }},{{ $a->longitude }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #f1f5f9; border-radius: 6px; font-size: 11.5px; font-weight: 600; color: #0f172a; text-decoration: none; border: 1px solid var(--border-subtle);">
                                    <svg width="13" height="13" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ round($a->latitude, 4) }}, {{ round($a->longitude, 4) }}
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">Tanpa GPS</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            @if($a->photo_path)
                                <button type="button" class="btn-native btn-native-white" style="padding: 5px 12px; font-size: 12px;"
                                        onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user ? $a->user->name : 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }} WIB', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')">
                                    <svg width="13" height="13" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Bukti
                                </button>
                            @else
                                <span style="font-size: 12px; color: #94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Tidak ditemukan riwayat absensi untuk filter yang dipilih.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bukti Absensi Selfie & Detail -->
<div id="photo-modal" class="modal-overlay" onclick="if(event.target === this) closePhotoModal()">
    <div class="modal-content-app">
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--dark-slate); display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                Bukti Foto Swafoto Absensi
            </h3>
            <button type="button" onclick="closePhotoModal()" style="background: none; border: none; font-size: 22px; color: #94a3b8; cursor: pointer; padding: 0 4px; line-height: 1;">&times;</button>
        </div>
        <div style="padding: 24px; text-align: center;">
            <div style="background: #000000; border-radius: 14px; overflow: hidden; margin-bottom: 18px; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; min-height: 280px; max-height: 380px;">
                <img id="modal-img" src="" alt="Bukti Foto Selfie" style="width: 100%; height: auto; max-height: 380px; object-fit: contain;">
            </div>

            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; text-align: left; border: 1px solid var(--border-subtle);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 13px;">
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Karyawan</div>
                        <strong id="modal-user" style="color: var(--dark-slate); font-size: 14px;">-</strong>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Waktu & Tanggal</div>
                        <span id="modal-time" style="color: var(--dark-slate); font-weight: 600;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Sesi & Status</div>
                        <span id="modal-type-status" style="font-weight: 700;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;">Lokasi GPS</div>
                        <a id="modal-map-link" href="#" target="_blank" style="color: #0f172a; font-weight: 600; text-decoration: underline; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                            Buka di Google Maps &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end;">
            <button type="button" onclick="closePhotoModal()" class="btn-native btn-native-dark" style="padding: 8px 22px;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openPhotoModal(imgUrl, userName, timeStr, type, status, lat, lon) {
        document.getElementById('modal-img').src = imgUrl;
        document.getElementById('modal-user').innerText = userName;
        document.getElementById('modal-time').innerText = timeStr;
        
        let typeBadge = type === 'Masuk' ? '<span style="color:#166534">Masuk</span>' : '<span style="color:#854d0e">Keluar</span>';
        document.getElementById('modal-type-status').innerHTML = typeBadge + ' &bull; ' + status;
        
        const mapLink = document.getElementById('modal-map-link');
        if (lat && lon && lat !== 'null' && lon !== 'null') {
            mapLink.href = 'https://maps.google.com/?q=' + lat + ',' + lon;
            mapLink.innerText = lat + ', ' + lon;
            mapLink.style.display = 'inline-flex';
        } else {
            mapLink.style.display = 'none';
        }
        
        document.getElementById('photo-modal').style.display = 'flex';
    }

    function closePhotoModal() {
        document.getElementById('photo-modal').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePhotoModal();
    });

    // Realtime instant search filter across rows
    function filterTableRealtime() {
        const query = document.getElementById('instant-table-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.searchable-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const content = row.getAttribute('data-search') || '';
            if (content.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const counter = document.getElementById('table-count-label');
        if (counter) {
            counter.innerText = 'Menampilkan ' + visibleCount + ' data';
        }
    }

    // Quick date presets
    function applyDatePreset(preset) {
        const startInput = document.getElementById('start_date_input');
        const endInput = document.getElementById('end_date_input');
        const now = new Date();

        function formatDate(d) {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        if (preset === 'today') {
            const todayStr = formatDate(now);
            startInput.value = todayStr;
            endInput.value = todayStr;
        } else if (preset === 'yesterday') {
            const y = new Date();
            y.setDate(y.getDate() - 1);
            const yStr = formatDate(y);
            startInput.value = yStr;
            endInput.value = yStr;
        } else if (preset === '7days') {
            const past7 = new Date();
            past7.setDate(past7.getDate() - 6);
            startInput.value = formatDate(past7);
            endInput.value = formatDate(now);
        } else if (preset === '30days') {
            const past30 = new Date();
            past30.setDate(past30.getDate() - 29);
            startInput.value = formatDate(past30);
            endInput.value = formatDate(now);
        } else if (preset === 'thisMonth') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        }

        document.getElementById('filter-form').submit();
    }
</script>
@endsection

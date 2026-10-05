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

    .shift-page-container {
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
        padding: 9px 18px;
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

    /* Day Navigation Pills */
    .day-filter-bar {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 24px;
    }
    .day-pill-btn {
        padding: 8px 16px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .day-pill-btn:hover, .day-pill-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Schedule Card */
    .schedule-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 24px;
    }

    .day-section {
        padding: 22px 24px;
        border-bottom: 1px solid var(--border-subtle);
    }
    .day-section:last-child {
        border-bottom: none;
    }

    .day-header {
        font-size: 16px;
        font-weight: 700;
        color: var(--dark-slate);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .shifts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }

    .shift-box-app {
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px;
        background: #ffffff;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .shift-box-app:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        border-color: #cbd5e1;
    }
    .shift-box-app.is-libur {
        background: #fff5f5;
        border-color: #fecaca;
    }

    .shift-box-title {
        font-weight: 700;
        font-size: 15px;
        color: var(--dark-slate);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .shift-box-time {
        font-size: 12.5px;
        font-family: monospace;
        color: var(--text-muted);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .assigned-users-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 16px;
    }

    .assigned-user-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        padding: 8px 12px;
        border-radius: 9px;
        border: 1px solid var(--border-subtle);
        font-size: 13px;
    }

    .btn-delete-shift-app {
        position: absolute;
        top: 14px;
        right: 14px;
        color: #94a3b8;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        transition: all 0.15s;
    }
    .btn-delete-shift-app:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .btn-remove-user {
        color: #94a3b8;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.15s;
    }
    .btn-remove-user:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-container-app {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
    }
    .modal-header-app {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-title-app {
        font-size: 16px;
        font-weight: 700;
        color: var(--dark-slate);
        margin: 0;
    }
    .modal-close-app {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #94a3b8;
        line-height: 1;
    }
    .modal-body-app {
        padding: 24px;
    }
    .modal-footer-app {
        padding: 14px 24px;
        background: #f8fafc;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-group-app {
        margin-bottom: 16px;
    }
    .form-group-app label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
    }
    .input-control-app {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border-subtle);
        border-radius: 9px;
        font-size: 13.5px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }
    .input-control-app:focus {
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }
</style>

<div class="shift-page-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Jadwal & Sesi Shift Mingguan
            </h1>
            <p>Atur jam kerja harian, alokasikan staf jaga, dan tandai hari libur operasional kedai.</p>
        </div>
        <div>
            <button type="button" class="btn-native btn-native-dark" onclick="openModal('addShiftModal')">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Sesi Shift Baru
            </button>
        </div>
    </div>

    <!-- Day Navigation Filter Pills -->
    <div class="day-filter-bar">
        <button type="button" class="day-pill-btn active" onclick="filterDaySection('all', this)">Semua Hari (7 Hari)</button>
        @foreach($haris as $h)
            <button type="button" class="day-pill-btn" onclick="filterDaySection('{{ $h }}', this)">{{ $h }}</button>
        @endforeach
    </div>

    <!-- Main Schedule Cards -->
    <div class="schedule-card">
        @foreach($haris as $hari)
            @php
                $shiftsHariIni = isset($groupedShifts[$hari]) ? $groupedShifts[$hari] : collect();
            @endphp
            <div class="day-section" id="day-section-{{ $hari }}" data-day="{{ $hari }}">
                <div class="day-header">
                    <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Hari {{ $hari }}</span>
                    <span style="font-size: 12px; font-weight: 500; color: var(--text-muted);">
                        ({{ $shiftsHariIni->count() }} sesi shift)
                    </span>
                </div>
                
                @if($shiftsHariIni->isEmpty())
                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px; background: #f8fafc; border-radius: 12px; border: 1px dashed var(--border-subtle);">
                        Belum ada sesi shift terjadwal untuk hari {{ $hari }}.
                    </div>
                @else
                    <div class="shifts-grid">
                        @foreach($shiftsHariIni as $shift)
                            <div class="shift-box-app {{ $shift->is_libur ? 'is-libur' : '' }}">
                                <form action="{{ route('admin.staff.shift.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Hapus sesi shift {{ addslashes($shift->nama) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-shift-app" title="Hapus Shift">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>

                                <div class="shift-box-title">
                                    {{ $shift->nama }}
                                    @if($shift->is_libur)
                                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #fee2e2; color: #dc2626; font-weight: 700;">LIBUR</span>
                                    @endif
                                </div>

                                @if(!$shift->is_libur)
                                    <div class="shift-box-time">
                                        <svg width="14" height="14" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                @endif

                                <div style="margin-top: 14px;">
                                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        Karyawan Bertugas ({{ $shift->users->count() }}):
                                    </div>
                                    <div class="assigned-users-list">
                                        @foreach($shift->users as $u)
                                            <div class="assigned-user-item">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="width: 26px; height: 26px; border-radius: 50%; background: #0f172a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                    <span style="font-weight: 600; color: var(--dark-slate);">{{ $u->name }}</span>
                                                </div>
                                                <form action="{{ route('admin.staff.shift.remove', [$shift->id, $u->id]) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-remove-user" title="Cabut Karyawan">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                        @if($shift->users->isEmpty())
                                            <div style="font-size: 12px; color: #94a3b8; font-style: italic; padding: 6px 0;">Belum ada staf bertugas.</div>
                                        @endif
                                    </div>
                                    
                                    @if(!$shift->is_libur)
                                    <button type="button" class="btn-native btn-native-white" style="width: 100%; justify-content: center; padding: 7px 12px; font-size: 12.5px;" onclick="openAssignModal({{ $shift->id }}, '{{ addslashes($shift->nama) }}')">
                                        <svg width="14" height="14" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Utus Karyawan
                                    </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Tambah Shift -->
<div id="addShiftModal" class="modal-overlay" onclick="closeModal(event, 'addShiftModal')">
    <div class="modal-container-app" onclick="event.stopPropagation()">
        <form action="{{ route('admin.staff.shift.store') }}" method="POST">
            @csrf
            <div class="modal-header-app">
                <h3 class="modal-title-app">Buat Sesi Shift Baru</h3>
                <button type="button" class="modal-close-app" onclick="document.getElementById('addShiftModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body-app">
                <div class="form-group-app">
                    <label>Pilih Hari</label>
                    <select name="hari" class="input-control-app" required>
                        @foreach($haris as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group-app" style="background: #f8fafc; padding: 12px; border-radius: 9px; border: 1px solid var(--border-subtle);">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; text-transform: none;">
                        <input type="checkbox" name="is_libur" id="is_libur_check" onchange="toggleLibur()">
                        <span style="font-weight: 700; color: var(--dark-slate);">Tandai Hari Libur Operasional</span>
                    </label>
                    <p style="font-size: 11.5px; color: var(--text-muted); margin: 4px 0 0 24px;">Tidak mewajibkan presensi staf pada hari ini.</p>
                </div>

                <div id="shift_details">
                    <div class="form-group-app">
                        <label>Nama Sesi</label>
                        <input type="text" name="nama" class="input-control-app" placeholder="Cth: Shift Pagi / Siang">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group-app">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="input-control-app">
                        </div>
                        <div class="form-group-app">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="input-control-app">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer-app">
                <button type="button" class="btn-native btn-native-white" onclick="document.getElementById('addShiftModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-native btn-native-dark">Simpan Sesi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Utus Karyawan -->
<div id="assignModal" class="modal-overlay" onclick="closeModal(event, 'assignModal')">
    <div class="modal-container-app" onclick="event.stopPropagation()">
        <form id="assignForm" method="POST">
            @csrf
            <div class="modal-header-app">
                <h3 class="modal-title-app">Tugaskan Karyawan ke <span id="assignShiftName" style="color: #0f172a;"></span></h3>
                <button type="button" class="modal-close-app" onclick="document.getElementById('assignModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body-app">
                <div class="form-group-app">
                    <label>Pilih Karyawan</label>
                    <select name="user_id" class="input-control-app" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->position ?: 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer-app">
                <button type="button" class="btn-native btn-native-white" onclick="document.getElementById('assignModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-native btn-native-dark">Tugaskan Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    
    function closeModal(e, id) {
        if(e.target.id === id) {
            document.getElementById(id).classList.remove('active');
        }
    }

    function toggleLibur() {
        const isChecked = document.getElementById('is_libur_check').checked;
        const details = document.getElementById('shift_details');
        
        if(isChecked) {
            details.style.opacity = '0.4';
            details.style.pointerEvents = 'none';
        } else {
            details.style.opacity = '1';
            details.style.pointerEvents = 'auto';
        }
    }

    function openAssignModal(shiftId, shiftName) {
        document.getElementById('assignForm').action = `/admin/staff/shift/${shiftId}/assign`;
        document.getElementById('assignShiftName').innerText = shiftName;
        openModal('assignModal');
    }

    function filterDaySection(selectedDay, btn) {
        document.querySelectorAll('.day-pill-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const sections = document.querySelectorAll('.day-section');
        sections.forEach(sec => {
            if (selectedDay === 'all' || sec.getAttribute('data-day') === selectedDay) {
                sec.style.display = '';
            } else {
                sec.style.display = 'none';
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
        }
    });
</script>
@endsection

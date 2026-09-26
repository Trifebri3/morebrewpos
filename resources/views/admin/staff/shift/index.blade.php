@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; }
    
    .btn-primary { background: var(--text-main); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { background: var(--primary-hover); }
    
    .btn-secondary { background: white; color: var(--text-main); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { border-color: var(--text-main); }
    
    .day-container { padding: 24px; border-bottom: 1px solid var(--border-color); }
    .day-container:last-child { border-bottom: none; }
    .day-title { font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    
    .shifts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
    .shift-box { border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #f8fafc; position: relative; }
    .shift-box.libur { background: #fee2e2; border-color: #fca5a5; }
    .shift-name { font-weight: 700; font-size: 15px; color: var(--text-main); margin-bottom: 4px; }
    .shift-time { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 4px; }
    
    .employee-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
    .employee-item { display: flex; justify-content: space-between; align-items: center; background: white; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; }
    
    .btn-remove { color: #ef4444; background: none; border: none; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
    .btn-remove:hover { background: #fee2e2; }

    .btn-delete-shift { position: absolute; top: 16px; right: 16px; color: #ef4444; background: none; border: none; cursor: pointer; padding: 4px; }
    .btn-delete-shift:hover { background: #fee2e2; border-radius: 4px; }
    
    /* MODAL STYLES */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
    .modal-overlay.active { display: flex; opacity: 1; }
    .modal-container { background: white; width: 100%; max-width: 500px; border-radius: 16px; overflow: hidden; transform: scale(0.95); transition: transform 0.3s ease; }
    .modal-overlay.active .modal-container { transform: scale(1); }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .modal-title { font-size: 18px; font-weight: 700; margin: 0; }
    .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted); }
    .modal-body { padding: 24px; }
    .modal-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; }
    
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    .input-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box; }
    .input-control:focus { border-color: var(--text-main); }
    
    .empty-state { text-align: center; padding: 20px; font-size: 13px; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 8px; }
</style>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Jadwal Shift Mingguan</h2>
        <button class="btn-primary" onclick="openModal('addShiftModal')">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Sesi Shift Baru
        </button>
    </div>
    
    <div>
        @foreach($haris as $hari)
            @php
                $shiftsHariIni = isset($groupedShifts[$hari]) ? $groupedShifts[$hari] : collect();
            @endphp
            <div class="day-container">
                <div class="day-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Hari {{ $hari }}
                </div>
                
                @if($shiftsHariIni->isEmpty())
                    <div class="empty-state">Belum ada sesi shift di hari {{ $hari }}.</div>
                @else
                    <div class="shifts-grid">
                        @foreach($shiftsHariIni as $shift)
                            <div class="shift-box {{ $shift->is_libur ? 'libur' : '' }}">
                                <form action="{{ route('admin.staff.shift.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Hapus sesi shift ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-shift" title="Hapus Shift">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>

                                <div class="shift-name">{{ $shift->nama }} {!! $shift->is_libur ? '<span style="color:#ef4444; font-size:12px;">(Libur)</span>' : '' !!}</div>
                                @if(!$shift->is_libur)
                                    <div class="shift-time">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}
                                    </div>
                                @endif

                                <div style="margin-top: 16px;">
                                    <div style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px;">KARYAWAN BERTUGAS:</div>
                                    <div class="employee-list">
                                        @foreach($shift->users as $u)
                                            <div class="employee-item">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                    {{ $u->name }}
                                                </div>
                                                <form action="{{ route('admin.staff.shift.remove', [$shift->id, $u->id]) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-remove" title="Cabut Karyawan">
                                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                        @if($shift->users->isEmpty())
                                            <div style="font-size: 12px; color: #94a3b8; font-style: italic;">Belum ada karyawan.</div>
                                        @endif
                                    </div>
                                    
                                    @if(!$shift->is_libur)
                                    <button type="button" class="btn-secondary" style="width: 100%; justify-content: center;" onclick="openAssignModal({{ $shift->id }}, '{{ $shift->nama }}')">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
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
    <div class="modal-container" onclick="event.stopPropagation()">
        <form action="{{ route('admin.staff.shift.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">Buat Sesi Shift Baru</h3>
                <button type="button" class="modal-close" onclick="document.getElementById('addShiftModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Hari</label>
                    <select name="hari" class="input-control" required>
                        @foreach($haris as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_libur" id="is_libur_check" onchange="toggleLibur()">
                        Tandai sebagai Hari Libur
                    </label>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Jika dicentang, sesi ini akan ditandai libur dan tidak perlu diisi jamnya.</p>
                </div>

                <div id="shift_details">
                    <div class="form-group">
                        <label>Nama Sesi</label>
                        <input type="text" name="nama" class="input-control" placeholder="Cth: Shift Pagi">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="input-control">
                        </div>
                        <div class="form-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="input-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('addShiftModal').classList.remove('active')" style="background: white; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" class="btn-primary">Simpan Sesi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Utus Karyawan -->
<div id="assignModal" class="modal-overlay" onclick="closeModal(event, 'assignModal')">
    <div class="modal-container" onclick="event.stopPropagation()">
        <form id="assignForm" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">Utus Karyawan ke <span id="assignShiftName"></span></h3>
                <button type="button" class="modal-close" onclick="document.getElementById('assignModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Karyawan</label>
                    <select name="user_id" class="input-control" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->position ?: 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('assignModal').classList.remove('active')" style="background: white; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" class="btn-primary">Tugaskan</button>
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
            details.style.opacity = '0.5';
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

    @if(session('success'))
        alert("{{ session('success') }}");
    @endif
    @if($errors->any())
        alert("Terjadi kesalahan: {{ $errors->first() }}");
    @endif
</script>
@endsection

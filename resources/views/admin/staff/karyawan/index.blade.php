@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; }
    
    .table-container { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { padding: 16px 24px; background: #f8fafc; font-size: 13px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid var(--border-color); white-space: nowrap; }
    td { padding: 16px 24px; font-size: 14px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    
    .btn-primary { background: var(--text-main); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { background: var(--primary-hover); }
    
    .btn-icon { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border-color); background: white; color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
    .btn-icon:hover { border-color: var(--text-main); color: var(--text-main); }
    .btn-icon.delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-admin { background: #fee2e2; color: #b91c1c; }
    .badge-kasir { background: #e0e7ff; color: #4338ca; }
    
    .badge-staff { background: #f1f5f9; color: #475569; }
    
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
    
    .profile-img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color); }
    .profile-placeholder { width: 48px; height: 48px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #94a3b8; font-size: 18px; }
</style>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Karyawan</h2>
        <button class="btn-primary" onclick="openModal('addModal')">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Karyawan
        </button>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Profil</th>
                    <th>ID / Nama</th>
                    <th>Posisi / HP</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($karyawans as $k)
                <tr>
                    <td>
                        @if($k->photo)
                            <img src="{{ asset('storage/' . $k->photo) }}" class="profile-img" alt="Foto">
                        @else
                            <div class="profile-placeholder">{{ strtoupper(substr($k->name, 0, 1)) }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $k->name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted); font-family: monospace;">ID: #{{ str_pad($k->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </td>
                    <td>
                        <div style="font-size: 13px;">{{ $k->position ?: '-' }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $k->phone ?: '-' }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" class="btn-icon" onclick="openQrModal('{{ route('admin.staff.karyawan.qr', $k->id) }}', '{{ $k->name }}')" title="Lihat QR Absensi">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                            <button type="button" class="btn-icon" onclick="openEditModal({{ json_encode($k) }})" title="Edit">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <form action="{{ route('admin.staff.karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" title="Hapus">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($karyawans->isEmpty())
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada karyawan.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="modal-overlay" onclick="closeModal(event, 'addModal')">
    <div class="modal-container" onclick="event.stopPropagation()">
        <form action="{{ route('admin.staff.karyawan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">Tambah Karyawan</h3>
                <button type="button" class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="input-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Posisi (Opsional)</label>
                        <input type="text" name="position" class="input-control" placeholder="Cth: Barista">
                    </div>
                    <div class="form-group">
                        <label>Nomor HP (Opsional)</label>
                        <input type="text" name="phone" class="input-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Foto Profil (Opsional)</label>
                    <input type="file" name="photo" class="input-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('addModal').classList.remove('active')" style="background: white; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" class="btn-primary">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal-overlay" onclick="closeModal(event, 'editModal')">
    <div class="modal-container" onclick="event.stopPropagation()">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h3 class="modal-title">Edit Karyawan</h3>
                <button type="button" class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="input-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label>Posisi</label>
                        <input type="text" name="position" id="edit_position" class="input-control">
                    </div>
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text" name="phone" id="edit_phone" class="input-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Update Foto (Kosongkan jika tidak diubah)</label>
                    <input type="file" name="photo" class="input-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('editModal').classList.remove('active')" style="background: white; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" class="btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR -->
<div id="qrModal" class="modal-overlay" onclick="closeModal(event, 'qrModal')">
    <div class="modal-container" onclick="event.stopPropagation()" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; position: relative;">
            <h3 class="modal-title">QR Code Absensi</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('qrModal').classList.remove('active')" style="position: absolute; right: 24px;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 40px 24px;">
            <div id="qrName" style="font-weight: 700; font-size: 18px; margin-bottom: 24px;"></div>
            <img id="qrImage" src="" alt="QR Code" style="width: 250px; height: 250px; margin: 0 auto; display: block; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 24px;">Simpan atau bagikan QR ini ke karyawan untuk keperluan absensi.</p>
        </div>
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

    function openEditModal(data) {
        document.getElementById('editForm').action = `/admin/staff/karyawan/${data.id}`;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_position').value = data.position || '';
        document.getElementById('edit_phone').value = data.phone || '';
        
        openModal('editModal');
    }

    function openQrModal(url, name) {
        document.getElementById('qrName').innerText = name;
        document.getElementById('qrImage').src = url;
        openModal('qrModal');
    }

    // Toast Notification System (from layouts if available, or custom alert if errors)
    @if(session('success'))
        alert("{{ session('success') }}"); // Bisa diganti dengan sistem Toast UI
    @endif
    @if($errors->any())
        alert("Terjadi kesalahan: {{ $errors->first() }}");
    @endif
</script>
@endsection

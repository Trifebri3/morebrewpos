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

    .karyawan-container {
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

    /* Table Card */
    .table-app-card {
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
        border-radius: 9px;
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

    /* Avatar and Icons */
    .profile-img-app {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid var(--border-subtle);
    }
    .profile-placeholder-app {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #0f172a;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .btn-action-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--border-subtle);
        background: #ffffff;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-action-icon:hover {
        background: #f8fafc;
        border-color: #000000;
    }
    .btn-action-icon.delete:hover {
        background: #fef2f2;
        border-color: #ef4444;
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
        max-width: 500px;
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

<div class="karyawan-container">
    <!-- Header Bar -->
    <div class="app-header-bar">
        <div class="app-title-group">
            <h1>
                <svg width="24" height="24" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Manajemen Karyawan
            </h1>
            <p>Kelola data anggota tim, posisi kerja, nomor kontak, dan barcode QR untuk absensi.</p>
        </div>
        <div>
            <button type="button" class="btn-native btn-native-dark" onclick="openModal('addModal')">
                <svg width="15" height="15" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-app-card">
        <div class="table-toolbar">
            <h2 class="table-title">
                <svg width="18" height="18" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Daftar Seluruh Karyawan ({{ $karyawans->count() }})
            </h2>

            <div class="search-input-box">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="karyawan-search" placeholder="Cari nama, posisi, telepon..." onkeyup="filterKaryawan()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="app-table" id="karyawan-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Profil</th>
                        <th>ID & Nama Karyawan</th>
                        <th>Posisi & Telepon</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $k)
                    <tr class="karyawan-row" data-search="{{ strtolower($k->name . ' ' . $k->position . ' ' . $k->phone . ' #' . str_pad($k->id, 4, '0', STR_PAD_LEFT)) }}">
                        <td>
                            @if($k->photo)
                                <img src="{{ asset('storage/' . $k->photo) }}" class="profile-img-app" alt="{{ $k->name }}">
                            @else
                                <div class="profile-placeholder-app">
                                    {{ strtoupper(substr($k->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--dark-slate); font-size: 14px;">{{ $k->name }}</div>
                            <div style="font-size: 11.5px; color: var(--text-muted); font-family: monospace;">ID: #{{ str_pad($k->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a; font-size: 13px;">{{ $k->position ?: 'Staff Operasional' }}</div>
                            <div style="font-size: 12px; color: var(--text-muted); font-family: monospace;">{{ $k->phone ?: '-' }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <button type="button" class="btn-action-icon" onclick="openQrModal('{{ route('admin.staff.karyawan.qr', $k->id) }}', '{{ addslashes($k->name) }}')" title="Lihat QR Absensi">
                                    <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                </button>
                                <button type="button" class="btn-action-icon" onclick="openEditModal({{ json_encode($k) }})" title="Edit Profil">
                                    <svg width="15" height="15" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.staff.karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus karyawan {{ addslashes($k->name) }}?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon delete" title="Hapus Karyawan">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span>Belum ada data karyawan terdaftar.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="modal-overlay" onclick="closeModal(event, 'addModal')">
    <div class="modal-container-app" onclick="event.stopPropagation()">
        <form action="{{ route('admin.staff.karyawan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header-app">
                <h3 class="modal-title-app">Tambah Karyawan Baru</h3>
                <button type="button" class="modal-close-app" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body-app">
                <div class="form-group-app">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="input-control-app" placeholder="Nama karyawan..." required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group-app">
                        <label>Posisi / Peran</label>
                        <input type="text" name="position" class="input-control-app" placeholder="Cth: Barista / Kasir">
                    </div>
                    <div class="form-group-app">
                        <label>Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" class="input-control-app" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="form-group-app">
                    <label>Foto Profil (Opsional)</label>
                    <input type="file" name="photo" class="input-control-app" accept="image/*">
                </div>
            </div>
            <div class="modal-footer-app">
                <button type="button" class="btn-native btn-native-white" onclick="document.getElementById('addModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-native btn-native-dark">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal-overlay" onclick="closeModal(event, 'editModal')">
    <div class="modal-container-app" onclick="event.stopPropagation()">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header-app">
                <h3 class="modal-title-app">Edit Data Karyawan</h3>
                <button type="button" class="modal-close-app" onclick="document.getElementById('editModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body-app">
                <div class="form-group-app">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="input-control-app" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group-app">
                        <label>Posisi / Peran</label>
                        <input type="text" name="position" id="edit_position" class="input-control-app">
                    </div>
                    <div class="form-group-app">
                        <label>Nomor HP</label>
                        <input type="text" name="phone" id="edit_phone" class="input-control-app">
                    </div>
                </div>
                <div class="form-group-app">
                    <label>Perbarui Foto (Biarkan kosong jika tetap)</label>
                    <input type="file" name="photo" class="input-control-app" accept="image/*">
                </div>
            </div>
            <div class="modal-footer-app">
                <button type="button" class="btn-native btn-native-white" onclick="document.getElementById('editModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-native btn-native-dark">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code -->
<div id="qrModal" class="modal-overlay" onclick="closeModal(event, 'qrModal')">
    <div class="modal-container-app" onclick="event.stopPropagation()" style="max-width: 380px; text-align: center;">
        <div class="modal-header-app">
            <h3 class="modal-title-app" style="width: 100%; text-align: center;">QR Code Absensi</h3>
            <button type="button" class="modal-close-app" onclick="document.getElementById('qrModal').classList.remove('active')">&times;</button>
        </div>
        <div class="modal-body-app" style="padding: 32px 24px;">
            <div id="qrName" style="font-weight: 700; font-size: 16px; color: var(--dark-slate); margin-bottom: 20px;"></div>
            <div style="background: #ffffff; padding: 16px; border-radius: 14px; border: 1px solid var(--border-subtle); display: inline-block;">
                <img id="qrImage" src="" alt="QR Code" style="width: 220px; height: 220px; display: block; border-radius: 8px;">
            </div>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 16px; line-height: 1.4;">
                Karyawan dapat memindai kode QR ini pada kamera pos kasir untuk presensi kilat.
            </p>
        </div>
        <div class="modal-footer-app" style="justify-content: center;">
            <button type="button" class="btn-native btn-native-dark" onclick="document.getElementById('qrModal').classList.remove('active')">
                Tutup
            </button>
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

    function filterKaryawan() {
        const query = document.getElementById('karyawan-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.karyawan-row');

        rows.forEach(row => {
            const data = row.getAttribute('data-search') || '';
            if (data.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
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

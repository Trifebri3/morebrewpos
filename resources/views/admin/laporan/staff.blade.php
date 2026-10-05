@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 2px 6px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 17px; font-weight: 700; color: var(--text-main); margin: 0; }

    .table-container { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 14px 20px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { font-size: 12px; font-weight: 600; color: var(--text-muted); background: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px; }
    td { font-size: 13.5px; color: var(--text-main); vertical-align: middle; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-masuk { background: #dcfce7; color: #166534; }
    .badge-keluar { background: #fef9c3; color: #854d0e; }
    
    .status-text { font-size: 13px; font-weight: 600; }
    .status-tepat { color: #16a34a; }
    .status-telat { color: #dc2626; }

    .btn-export { background: #166534; color: white; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background 0.2s; }
    .btn-export:hover { background: #14532d; }
    
    .photo-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 2px solid #e2e8f0; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
    .photo-thumb:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-content { background: white; border-radius: 16px; max-width: 520px; width: 100%; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); }
</style>

<div style="padding: 24px 32px 40px;">
    <!-- Title & Export -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 6px 0;">Laporan & Rekapitulasi Staff</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">Laporan absensi, kedisiplinan jam kerja, bukti foto swafoto, dan ekspor ke Excel.</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan.staff.export', request()->query()) }}" class="btn-export">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #2563eb;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Kehadiran</div>
            <div style="font-size: 26px; font-weight: 800; color: #1e293b;">{{ $totalAbsen ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Record</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #16a34a;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Tepat Waktu</div>
            <div style="font-size: 26px; font-weight: 800; color: #166534;">{{ $totalTepatWaktu ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Kali</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #dc2626;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Terlambat</div>
            <div style="font-size: 26px; font-weight: 800; color: #991b1b;">{{ $totalTerlambat ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Kali</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #8b5cf6;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Foto Terverifikasi</div>
            <div style="font-size: 26px; font-weight: 800; color: #6d28d9;">{{ $totalDenganFoto ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Foto</span></div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="padding: 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.laporan.staff') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Pilih Karyawan</label>
                <select name="user_id" style="padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; min-width: 160px;">
                    <option value="">Semua Karyawan</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Status Kehadiran</label>
                <select name="status" style="padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; min-width: 140px;">
                    <option value="">Semua Status</option>
                    <option value="tepat_waktu" {{ $status === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="terlambat" {{ $status === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="luar_zona" {{ $status === 'luar_zona' ? 'selected' : '' }}>Luar Zona</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
            </div>

            <div>
                <button type="submit" style="padding: 9px 20px; background: #000000; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    Filter Data
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Records -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Data Log Absensi Karyawan</h2>
            <span style="font-size: 13px; color: var(--text-muted);">{{ $absensis->count() }} Data Ditemukan</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Foto Selfie</th>
                        <th>Nama Staff</th>
                        <th>Tipe Absen</th>
                        <th>Status</th>
                        <th>Lokasi GPS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensis as $a)
                    <tr>
                        <td>
                            <strong style="color: #111827;">{{ $a->created_at->format('d M Y') }}</strong><br>
                            <span style="font-family: monospace; font-size: 12px; color: #64748b;">{{ $a->created_at->format('H:i:s') }} WIB</span>
                        </td>
                        <td>
                            @if($a->photo_path)
                                @php $photoUrl = Storage::url($a->photo_path); @endphp
                                <img src="{{ $photoUrl }}" alt="Selfie" class="photo-thumb" 
                                     onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user ? $a->user->name : 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }}', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')"
                                     onerror="this.src='https://placehold.co/80x80/f1f5f9/64748b?text=Foto'">
                            @else
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; font-weight: 600;">
                                    No Pic
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: #111827; font-size: 14px;">{{ $a->user ? $a->user->name : 'Karyawan' }}</strong><br>
                            <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">{{ $a->user ? $a->user->role : 'Staff' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $a->type == 'Masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                                {{ $a->type }}
                            </span>
                        </td>
                        <td>
                            <span class="status-text {{ str_contains($a->status, 'Terlambat') || str_contains($a->status, 'Luar Zona') ? 'status-telat' : 'status-tepat' }}">
                                {{ $a->status }}
                            </span>
                        </td>
                        <td>
                            @if($a->latitude && $a->longitude)
                                <a href="https://maps.google.com/?q={{ $a->latitude }},{{ $a->longitude }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; background: #eff6ff; border-radius: 6px; font-size: 12px; font-weight: 600; color: #1d4ed8; text-decoration: none;">
                                    {{ round($a->latitude, 4) }}, {{ round($a->longitude, 4) }}
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">Tanpa GPS</span>
                            @endif
                        </td>
                        <td>
                            @if($a->photo_path)
                                <button type="button" onclick="openPhotoModal('{{ $photoUrl }}', '{{ addslashes($a->user ? $a->user->name : 'Staff') }}', '{{ $a->created_at->format('d M Y H:i:s') }}', '{{ $a->type }}', '{{ addslashes($a->status) }}', '{{ $a->latitude }}', '{{ $a->longitude }}')" style="background: #000000; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                    Lihat Hasil
                                </button>
                            @else
                                <span style="font-size: 12px; color: #94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: #64748b;">
                            Tidak ditemukan data absensi untuk filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Hasil & Foto Absensi -->
<div id="photo-modal" class="modal-overlay" onclick="if(event.target === this) closePhotoModal()">
    <div class="modal-content">
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Hasil & Bukti Foto Absensi</h3>
            <button type="button" onclick="closePhotoModal()" style="background: none; border: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0 4px;">&times;</button>
        </div>
        <div style="padding: 24px; text-align: center;">
            <div style="background: #f8fafc; border-radius: 12px; overflow: hidden; margin-bottom: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; min-height: 280px; max-height: 380px;">
                <img id="modal-img" src="" alt="Bukti Foto Selfie" style="width: 100%; height: auto; max-height: 380px; object-fit: contain;">
            </div>

            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; text-align: left; border: 1px solid var(--border-color);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 600;">Nama Karyawan</div>
                        <strong id="modal-user" style="color: #111827; font-size: 14px;">-</strong>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 600;">Waktu & Tanggal</div>
                        <span id="modal-time" style="color: #111827; font-weight: 600;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 600;">Tipe & Status</div>
                        <span id="modal-type-status" style="font-weight: 700;">-</span>
                    </div>
                    <div>
                        <div style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 600;">Lokasi GPS</div>
                        <a id="modal-map-link" href="#" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline; font-size: 12px;">
                            Buka di Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid var(--border-color); text-align: right;">
            <button type="button" onclick="closePhotoModal()" style="padding: 8px 20px; background: #000000; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
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
        document.getElementById('modal-type-status').innerHTML = '<span style="color: ' + (type === 'Masuk' ? '#166534' : '#854d0e') + '">' + type + '</span> &bull; ' + status;
        
        const mapLink = document.getElementById('modal-map-link');
        if (lat && lon && lat !== 'null') {
            mapLink.href = 'https://maps.google.com/?q=' + lat + ',' + lon;
            mapLink.innerText = lat + ', ' + lon;
            mapLink.style.display = 'inline';
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
</script>
@endsection

@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .grid-container { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; }
    
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; }
    
    .scanner-box { padding: 24px; text-align: center; }
    #reader { width: 100%; max-width: 400px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px solid var(--border-color); }
    
    .status-alert { margin-top: 16px; padding: 16px; border-radius: 8px; display: none; font-size: 14px; font-weight: 600; text-align: center; }
    .status-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; display: block; }
    .status-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; display: block; }

    .table-container { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { font-size: 13px; font-weight: 600; color: var(--text-muted); background: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px; }
    td { font-size: 14px; color: var(--text-main); }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-masuk { background: #dcfce7; color: #166534; }
    .badge-keluar { background: #fef9c3; color: #854d0e; }
    
    .status-text { font-size: 13px; font-weight: 600; }
    .status-tepat { color: #16a34a; }
    .status-telat { color: #dc2626; }
</style>

<div class="grid-container">
    <!-- Pengaturan Absensi -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Pengaturan Tipe Absensi</h2>
        </div>
        <div style="padding: 24px;">
            @if (session('success'))
                <div class="status-alert status-success" style="display: block; margin-bottom: 24px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.staff.absensi.settings.update') }}" method="POST">
                @csrf
                <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; align-items: flex-start; gap: 16px;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 16px; color: var(--text-main);">Absen Via QR Code (Kasir)</h3>
                        <p style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                            Jika diaktifkan, karyawan dapat melakukan absensi Masuk/Pulang dengan men-scan QR Code mereka melalui perangkat POS Kasir. Sebuah tombol "Absen QR" akan muncul di layar Kasir.
                        </p>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 28px;">
                            <input type="checkbox" name="is_qr_absen_enabled" value="1" onchange="this.form.submit()" {{ (isset($kedai) && $kedai->is_qr_absen_enabled) ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ (isset($kedai) && $kedai->is_qr_absen_enabled) ? 'var(--text-main)' : '#ccc' }}; transition: .4s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; transform: {{ (isset($kedai) && $kedai->is_qr_absen_enabled) ? 'translateX(22px)' : 'translateX(0)' }};"></span>
                            </span>
                        </label>
                    </div>
                </div>
                
                <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; align-items: flex-start; gap: 16px; margin-top: 16px;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 16px; color: var(--text-main);">Absen Via Tautan Publik (GPS + Selfie)</h3>
                        <p style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                            Karyawan melakukan absen melalui HP mereka masing-masing dengan mengakses tautan publik di bawah. Sistem akan meminta deteksi lokasi (Geofencing sesuai radius Kedai) dan swafoto (selfie).
                        </p>
                        
                        <div style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border-color);">
                            <div style="font-family: monospace; font-size: 13px; font-weight: 600; color: #334155;">{{ url('/absen') }}</div>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ url('/absen') }}'); alert('Tautan disalin!')" style="background: white; border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Salin Tautan</button>
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 28px;">
                            <input type="checkbox" name="is_link_absen_enabled" value="1" onchange="this.form.submit()" {{ (isset($kedai) && $kedai->is_link_absen_enabled) ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ (isset($kedai) && $kedai->is_link_absen_enabled) ? 'var(--text-main)' : '#ccc' }}; transition: .4s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; transform: {{ (isset($kedai) && $kedai->is_link_absen_enabled) ? 'translateX(22px)' : 'translateX(0)' }};"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Absensi Hari Ini -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
            <h2 class="card-title">Rekap & Daftar Absensi Hari Ini</h2>
            <span style="font-size: 14px; color: var(--text-muted); font-weight: 600; background: #f1f5f9; padding: 6px 12px; border-radius: 6px;">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>
        
        <div style="padding: 24px;">
            <!-- Recap Widgets -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px; border-radius: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: #1e3a8a; margin-bottom: 8px;">TOTAL JADWAL HARI INI</div>
                    <div style="font-size: 28px; font-weight: 800; color: #1e40af;">{{ $recap['total_karyawan'] }} <span style="font-size: 14px; font-weight: 500;">Orang</span></div>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: #14532d; margin-bottom: 8px;">SUDAH HADIR</div>
                    <div style="font-size: 28px; font-weight: 800; color: #166534;">{{ $recap['hadir'] }} <span style="font-size: 14px; font-weight: 500;">Orang</span></div>
                </div>
                <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 16px; border-radius: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: #7f1d1d; margin-bottom: 8px;">TERLAMBAT</div>
                    <div style="font-size: 28px; font-weight: 800; color: #991b1b;">{{ $recap['terlambat'] }} <span style="font-size: 14px; font-weight: 500;">Orang</span></div>
                </div>
                <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 16px; border-radius: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: #78350f; margin-bottom: 8px;">BELUM ABSEN</div>
                    <div style="font-size: 28px; font-weight: 800; color: #92400e;">{{ $recap['belum_absen'] }} <span style="font-size: 14px; font-weight: 500;">Orang</span></div>
                </div>
            </div>

            @if(count($recap['shifts']) > 0)
            <div style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                @foreach($recap['shifts'] as $s)
                <div style="border: 1px solid var(--border-color); padding: 12px 16px; border-radius: 8px; font-size: 13px;">
                    <strong style="color: var(--text-main);">{{ $s['name'] }} ({{ $s['jam'] }})</strong><br>
                    <span style="color: var(--text-muted);">Hadir: {{ $s['hadir'] }}/{{ $s['total_karyawan'] }} | Telat: {{ $s['terlambat'] }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="table-container" style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden;">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Karyawan</th>
                            <th>Tipe</th>
                            <th>Bukti / Lokasi</th>
                            <th>Status / Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="absensi-table-body">
                        @foreach($absensis as $a)
                        <tr>
                            <td style="font-family: monospace; font-weight: 600;">{{ $a->created_at->format('H:i:s') }}</td>
                            <td style="font-weight: 600;">{{ $a->user->name }}</td>
                            <td>
                                <span class="badge {{ $a->type == 'Masuk' ? 'badge-masuk' : 'badge-keluar' }}">{{ $a->type }}</span>
                            </td>
                            <td>
                                @if($a->photo_path)
                                <a href="{{ Storage::url($a->photo_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #f1f5f9; border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-main); text-decoration: none;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Lihat Foto
                                </a>
                                @elseif($a->latitude)
                                <a href="https://maps.google.com/?q={{ $a->latitude }},{{ $a->longitude }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #eff6ff; border-radius: 6px; font-size: 12px; font-weight: 600; color: #1d4ed8; text-decoration: none;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Lihat Peta
                                </a>
                                @else
                                <span style="font-size: 12px; color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-text {{ str_contains($a->status, 'Terlambat') || str_contains($a->status, 'Luar Zona') ? 'status-telat' : 'status-tepat' }}">
                                    {{ $a->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        @if($absensis->isEmpty())
                        <tr id="empty-row">
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada karyawan yang absen hari ini.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

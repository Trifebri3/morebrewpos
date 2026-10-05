@extends('kasir.layouts.app')

@section('content')
<style>
    :root {
        --pure-black: #000000;
        --dark-slate: #0f172a;
        --border-subtle: #e2e8f0;
        --bg-subtle: #f8fafc;
    }

    .kasir-absensi-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 28px 24px 60px;
    }

    .header-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 28px;
        margin-bottom: 24px;
        border: 1px solid var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .app-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .live-clock-card {
        background: var(--dark-slate);
        color: #ffffff;
        border-radius: 16px;
        padding: 24px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .btn-clock {
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        font-size: 15px;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-clock-in {
        background: #10b981;
    }
    .btn-clock-in:hover {
        background: #059669;
        transform: translateY(-2px);
    }
    .btn-clock-out {
        background: #ef4444;
    }
    .btn-clock-out:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .chip-in {
        background: #dcfce7;
        color: #166534;
    }
    .chip-out {
        background: #fef3c7;
        color: #92400e;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        text-align: left;
    }
    .table th {
        padding: 12px 20px;
        background: #f8fafc;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border-subtle);
    }
    .table td {
        padding: 13px 20px;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
    }
</style>

<div class="kasir-absensi-container">
    <!-- Header Greeting -->
    <div class="header-box">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Presensi & Absensi Kasir</h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Catat waktu mulai dan selesai shift kerja Anda secara akurat.</p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <span style="background: #f1f5f9; color: #0f172a; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 12.5px; border: 1px solid var(--border-subtle); display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 14px 18px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 12px; margin-bottom: 22px; font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; margin-bottom: 22px; font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- LIVE CLOCK & ACTION BANNER -->
    <div class="live-clock-card">
        <div>
            <div style="font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Waktu Sistem POS</div>
            <div id="live-time" style="font-size: 36px; font-weight: 900; font-family: monospace; letter-spacing: -1px; margin: 2px 0;">--:--:--</div>
            <div style="font-size: 12.5px; color: #cbd5e1;">Staf: <strong>{{ auth()->user()->name ?? 'Kasir' }}</strong> • Kedai: {{ $kedai->name ?? 'MOREBREWW' }}</div>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            @if(!$sudahMasuk)
                <form id="form-masuk" action="{{ route('kasir.absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="Masuk">
                    <input type="hidden" name="latitude" id="lat-masuk">
                    <input type="hidden" name="longitude" id="lon-masuk">
                    <button type="button" onclick="submitAbsenGps('form-masuk', 'lat-masuk', 'lon-masuk')" class="btn-clock btn-clock-in">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <span>Clock In (Masuk)</span>
                    </button>
                </form>
            @elseif($sudahMasuk && !$sudahKeluar)
                <form id="form-keluar" action="{{ route('kasir.absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="Keluar">
                    <input type="hidden" name="latitude" id="lat-keluar">
                    <input type="hidden" name="longitude" id="lon-keluar">
                    <button type="button" onclick="submitAbsenGps('form-keluar', 'lat-keluar', 'lon-keluar')" class="btn-clock btn-clock-out">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>Clock Out (Keluar)</span>
                    </button>
                </form>
            @else
                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; border-radius: 12px; padding: 12px 20px; text-align: right;">
                    <div style="font-size: 13.5px; font-weight: 700; color: #34d399;">✓ Absensi Lengkap Hari Ini</div>
                    <div style="font-size: 11.5px; color: #cbd5e1; margin-top: 2px;">Anda sudah tercatat Clock In & Clock Out.</div>
                </div>
            @endif
        </div>
    </div>

    <!-- QR CAMERA SCANNER OR SMARTPHONE LINK CARD -->
    @if(isset($kedai) && $kedai->is_qr_absen_enabled)
    <div class="app-card" style="padding: 24px; text-align: center;">
        <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #0f172a;">Kamera Scanner QR Code (Opsional)</h3>
        <p style="margin: 0 0 16px 0; font-size: 13px; color: #64748b;">Arahkan kartu QR code karyawan Anda ke kamera untuk absen otomatis tanpa klik tombol.</p>
        
        <div id="reader" style="width: 100%; max-width: 380px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border-subtle);"></div>
        <div id="scan-result" style="margin-top: 14px; padding: 12px; border-radius: 8px; font-size: 13.5px; font-weight: 600; display: none;"></div>
    </div>
    @endif

    <!-- TODAY'S SHIFT RECAP -->
    @if(count($recap) > 0)
    <div class="app-card" style="padding: 20px 24px;">
        <h3 style="margin: 0 0 14px 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Jadwal & Rekap Kehadiran Shift Hari Ini</span>
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
            @foreach($recap as $s)
            <div style="border: 1px solid var(--border-subtle); padding: 14px 16px; border-radius: 12px; background: #fafafa;">
                <div style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $s['name'] }}</div>
                <div style="font-size: 12px; font-family: monospace; color: #64748b; margin-top: 2px;">{{ $s['jam'] }}</div>
                <div style="margin-top: 10px; display: flex; justify-content: space-between; font-size: 12.5px; border-top: 1px dashed var(--border-subtle); padding-top: 8px;">
                    <span>Hadir: <strong style="color: #166534;">{{ $s['hadir'] }}</strong> / {{ $s['total_karyawan'] }}</span>
                    <span>Telat: <strong style="color: {{ $s['terlambat'] > 0 ? '#dc2626' : '#64748b' }};">{{ $s['terlambat'] }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- PERSONAL ATTENDANCE HISTORY -->
    <div class="app-card">
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Riwayat Absensi Terakhir Anda</h3>
            <span style="font-size: 12px; color: #64748b;">10 data terakhir</span>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Tipe Absen</th>
                        <th>Status</th>
                        <th>Lokasi GPS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr>
                        <td>
                            <strong style="color: #0f172a;">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }} WIB</strong>
                            <div style="font-size: 11.5px; color: #64748b;">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="status-chip {{ $item->type === 'Masuk' ? 'chip-in' : 'chip-out' }}">
                                {{ $item->type }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 12.5px; font-weight: 600; color: {{ str_contains($item->status, 'Terlambat') ? '#dc2626' : '#15803d' }};">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>
                            @if($item->latitude && $item->longitude)
                                <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" style="font-size: 12px; color: #0f172a; text-decoration: underline; font-weight: 600;">
                                    {{ round($item->latitude, 4) }}, {{ round($item->longitude, 4) }} ↗
                                </a>
                            @else
                                <span style="font-size: 12px; color: #94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 36px;">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live Digital Clock (Asia/Jakarta / WIB)
    function updateClock() {
        const d = new Date();
        const timeFormatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        const clockEl = document.getElementById('live-time');
        if (clockEl) {
            clockEl.innerText = `${timeFormatter.format(d)} WIB`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Geolocation submission helper
    function submitAbsenGps(formId, latId, lonId) {
        if (!navigator.geolocation) {
            document.getElementById(formId).submit();
            return;
        }

        const btn = document.querySelector(`#${formId} button`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>Mengambil Lokasi GPS...</span>';
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById(latId).value = position.coords.latitude;
                document.getElementById(lonId).value = position.coords.longitude;
                document.getElementById(formId).submit();
            },
            function() {
                // Submit without coordinates if permission denied
                document.getElementById(formId).submit();
            },
            { enableHighAccuracy: true, timeout: 6000 }
        );
    }
</script>

@if(isset($kedai) && $kedai->is_qr_absen_enabled)
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isScanning = false;
        const html5QrCode = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 220 });
        
        html5QrCode.render(function(decodedText) {
            if (isScanning) return;
            isScanning = true;

            const res = document.getElementById('scan-result');
            res.style.display = 'block';
            res.style.background = '#fef3c7';
            res.style.color = '#92400e';
            res.innerText = 'Memproses scan QR...';

            fetch("{{ route('admin.staff.absensi.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    res.style.background = '#dcfce7';
                    res.style.color = '#166534';
                    res.innerText = data.message;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    res.style.background = '#fee2e2';
                    res.style.color = '#991b1b';
                    res.innerText = data.message;
                    setTimeout(() => { isScanning = false; res.style.display = 'none'; }, 3000);
                }
            })
            .catch(() => {
                isScanning = false;
                res.style.display = 'none';
            });
        });
    });
</script>
@endif
@endsection

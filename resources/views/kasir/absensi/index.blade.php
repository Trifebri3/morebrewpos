@extends('kasir.layouts.app')

@section('content')
<style>
    .container { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; padding: 24px; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 500; color: var(--text-muted); background: #fafafa; }
    .table tr:last-child td { border-bottom: none; }
    
    .btn-action { padding: 14px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; font-size: 16px; color: white; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-masuk { background: #0ea5e9; }
    .btn-masuk:hover { background: #0284c7; }
    .btn-keluar { background: #ef4444; }
    .btn-keluar:hover { background: #dc2626; }
    .btn-disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-valid { background: #dcfce7; color: #166534; }
</style>

<div class="container" style="max-width: 100%; padding: 24px;">
    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Clock In / Absensi</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Catat kehadiran harian Anda sebagai kasir.</p>
        </div>
        <div>
            <span style="background: #e0f2fe; color: #0369a1; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px;">Hari Ini: {{ date('d M Y') }}</span>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 16px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="display: flex; gap: 16px; align-items: center;">
        @if(isset($kedai) && $kedai->is_qr_absen_enabled)
            <div style="flex: 1; text-align: center; padding: 24px;">
                <h3 style="margin: 0 0 16px 0; font-size: 18px; color: var(--text-main);">Kamera Absensi (Scan QR)</h3>
                
                <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px solid var(--border-color);"></div>
                <div id="scan-result" style="margin-top: 16px; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; display: none;"></div>
                
                <p style="margin: 16px 0 0 0; font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                    Arahkan QR Code Karyawan ke depan kamera untuk melakukan Clock In atau Clock Out.
                </p>
            </div>
        @elseif(isset($kedai) && $kedai->is_link_absen_enabled)
            <div style="flex: 1; text-align: center; padding: 24px;">
                <svg style="width: 48px; height: 48px; color: var(--text-main); margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <h3 style="margin: 0 0 8px 0; font-size: 18px; color: var(--text-main);">Mode Absensi Tautan Publik Aktif</h3>
                <p style="margin: 0; font-size: 14px; color: var(--text-muted); line-height: 1.5;">
                    Silakan gunakan *smartphone* Anda dan akses tautan publik untuk mengambil *selfie* di lokasi kedai.
                </p>
                <div style="margin-top: 16px; font-family: monospace; font-size: 16px; font-weight: 700; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px dashed var(--border-color); color: #0f172a;">
                    {{ url('/absen') }}
                </div>
            </div>
        @else
            @if(!$sudahMasuk)
                <form action="{{ route('kasir.absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="Masuk">
                    <button type="submit" class="btn-action btn-masuk">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Clock In (Masuk)
                    </button>
                </form>
            @endif

            @if($sudahMasuk && !$sudahKeluar)
                <form action="{{ route('kasir.absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="Keluar">
                    <button type="submit" class="btn-action btn-keluar">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Clock Out (Keluar)
                    </button>
                </form>
            @endif

            @if($sudahMasuk && $sudahKeluar)
                <div style="flex: 1; text-align: center; padding: 12px; color: #166534; font-weight: 600; background: #dcfce7; border-radius: 8px;">
                    Anda sudah menyelesaikan absensi (Masuk & Keluar) untuk hari ini. Selamat beristirahat!
                </div>
            @endif
        @endif
    </div>

    @if(count($recap) > 0)
    <div class="card" style="padding: 16px 24px;">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; color: var(--text-main);">Rekap Kehadiran Shift Hari Ini</h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @foreach($recap as $s)
            <div style="border: 1px solid var(--border-color); padding: 12px 16px; border-radius: 8px; font-size: 13px; background: #fafafa; flex: 1; min-width: 200px;">
                <strong style="color: var(--text-main); display: block; margin-bottom: 4px;">{{ $s['name'] }} <span style="font-weight: 400; color: var(--text-muted);">({{ $s['jam'] }})</span></strong>
                <span style="color: var(--text-main); font-weight: 600;">Hadir: <span style="color: #166534;">{{ $s['hadir'] }}</span>/{{ $s['total_karyawan'] }}</span> | 
                <span style="color: var(--text-main); font-weight: 600;">Telat: <span style="color: #991b1b;">{{ $s['terlambat'] }}</span></span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card" style="padding: 0;">
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa;">
            Riwayat Absensi Terakhir
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu & Tanggal</th>
                    <th>Tipe</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i | d M Y') }}</td>
                    <td style="font-weight: 600; color: {{ $item->type == 'Masuk' ? '#0ea5e9' : '#ef4444' }};">{{ $item->type }}</td>
                    <td><span class="badge badge-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 32px;">Belum ada riwayat absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(isset($kedai) && $kedai->is_qr_absen_enabled)
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isScanning = false;
        
        function onScanSuccess(decodedText, decodedResult) {
            if (isScanning) return; // Prevent multiple scans
            isScanning = true;
            
            const resultBox = document.getElementById('scan-result');
            resultBox.className = 'status-alert';
            resultBox.style.display = 'block';
            resultBox.style.background = '#eff6ff';
            resultBox.style.color = '#1d4ed8';
            resultBox.innerHTML = 'Memproses...';
            
            const csrfToken = '{{ csrf_token() }}';

            fetch("{{ route('admin.staff.absensi.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultBox.style.background = '#dcfce7';
                    resultBox.style.color = '#166534';
                    resultBox.innerHTML = data.message;
                    
                    // Reload after 2 seconds to show updated riwayat
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    resultBox.style.background = '#fee2e2';
                    resultBox.style.color = '#991b1b';
                    resultBox.innerHTML = data.message;
                    
                    setTimeout(() => {
                        isScanning = false;
                        resultBox.style.display = 'none';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultBox.style.background = '#fee2e2';
                resultBox.style.color = '#991b1b';
                resultBox.innerHTML = 'Terjadi kesalahan sistem!';
                
                setTimeout(() => { 
                    isScanning = false; 
                    resultBox.style.display = 'none'; 
                }, 3000);
            });
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
@endif

@endsection

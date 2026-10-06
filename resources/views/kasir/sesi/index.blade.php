@extends('kasir.layouts.app', $data ?? [])

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; }
    .btn-primary:hover { opacity: 0.9; }
    .btn-danger { background: #ef4444; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; }
    .btn-danger:hover { background: #dc2626; }
    
    .card-session { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 32px; text-align: center; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .card-session h2 { margin-top: 0; color: var(--text-main); margin-bottom: 8px; font-size: 24px; }
    .card-session p { color: var(--text-muted); margin-bottom: 24px; }
    
    .form-group { text-align: left; margin-bottom: 16px; }
    .form-label { display: block; font-weight: 500; margin-bottom: 8px; color: var(--text-main); font-size: 14px; }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; box-sizing: border-box; outline: none; font-size: 15px; }
    .form-control:focus { border-color: #3b82f6; }
    
    .history-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); margin-top: 40px; }
    .history-table th, .history-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .history-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <div>
        <h1>Sesi Kasir</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola waktu buka dan tutup laci kasir (Cash Drawer).</p>
    </div>
</div>

<div style="padding: 32px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #bbf7d0;">
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #fecaca;">
            <strong>Gagal!</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card-session">
        @if(!$sesiAktif)
            <h2>Mulai Sesi Baru</h2>
            <p>Silakan masukkan modal awal (uang tunai) di laci untuk membuka sesi.</p>
            
            <form action="{{ route('kasir.sesi.buka') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Modal Awal (Rp)</label>
                    <input type="number" name="modal_awal" class="form-control" placeholder="Contoh: 100000" required min="0">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Buka Kasir
                </button>
            </form>
        @else
            <h2>Sesi Sedang Aktif</h2>
            <p>Sesi dibuka sejak <strong>{{ $sesiAktif->waktu_buka->format('d M Y, H:i') }}</strong>.</p>
            
            <form action="{{ route('kasir.sesi.tutup') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Total Uang Tunai di Laci Saat Ini (Rp)</label>
                    <input type="number" name="uang_fisik" class="form-control" placeholder="Contoh: 500000" required min="0">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">Uang fisik ini akan dicocokkan dengan pendapatan sistem.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Penutupan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Jika ada selisih, tuliskan alasannya..."></textarea>
                </div>
                <button type="submit" class="btn-danger" style="width: 100%; justify-content: center;" onclick="return confirm('Anda yakin ingin menutup sesi ini? Pastikan uang fisik sudah dihitung dengan benar.')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Akhiri & Tutup Kasir
                </button>
            </form>
        @endif
    </div>

    @if($riwayatSesi->count() > 0)
    <h3 style="margin-top: 60px; margin-bottom: 16px;">Riwayat Sesi Terakhir</h3>
    <table class="history-table">
        <thead>
            <tr>
                <th>Waktu Buka</th>
                <th>Waktu Tutup</th>
                <th>Modal Awal</th>
                <th>Pendapatan</th>
                <th>Selisih</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatSesi as $sesi)
            <tr>
                <td>{{ $sesi->waktu_buka ? $sesi->waktu_buka->format('d M, H:i') : '-' }}</td>
                <td>{{ $sesi->waktu_tutup ? $sesi->waktu_tutup->format('d M, H:i') : '-' }}</td>
                <td>Rp {{ number_format($sesi->modal_awal, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sesi->total_pendapatan, 0, ',', '.') }}</td>
                <td>
                    @if($sesi->selisih > 0)
                        <span style="color: #16a34a;">+Rp {{ number_format($sesi->selisih, 0, ',', '.') }}</span>
                    @elseif($sesi->selisih < 0)
                        <span style="color: #dc2626;">-Rp {{ number_format(abs($sesi->selisih), 0, ',', '.') }}</span>
                    @else
                        <span style="color: var(--text-muted);">Pas (Rp 0)</span>
                    @endif
                </td>
                <td>
                    @if(in_array(strtolower($sesi->status ?? ''), ['buka', 'open']))
                        <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Sedang Aktif</span>
                    @else
                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

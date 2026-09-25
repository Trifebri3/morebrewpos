@extends('admin.layouts.app')

@section('content')
<style>
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 500; color: var(--text-muted); background: #fafafa; }
    .table tr:last-child td { border-bottom: none; }
    
    .text-danger { color: #dc2626; font-weight: 600; }
    
    .btn-detail { background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s; }
    .btn-detail:hover { background: #e2e8f0; }
</style>

<div style="max-width: 1000px; margin: 0 auto; padding-bottom: 40px;">
    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Laporan Harian Pengeluaran</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Rekapitulasi total uang keluar setiap harinya.</p>
        </div>
        <div>
            <a href="{{ route('admin.operasional.pengeluaran.index') }}" style="background: var(--text-main); color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 500;">Validasi Pengeluaran</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Riwayat Total Pengeluaran Per Hari
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah Transaksi (Approved)</th>
                    <th style="text-align: right;">Total Pengeluaran</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporanHarian as $hari)
                <tr>
                    <td style="font-weight: 500;">{{ \Carbon\Carbon::parse($hari->tanggal_harian)->translatedFormat('l, d F Y') }}</td>
                    <td>{{ $hari->jumlah_transaksi }} Laporan item</td>
                    <td style="text-align: right;" class="text-danger">- Rp {{ number_format($hari->total_pengeluaran, 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        <a href="{{ route('admin.operasional.pengeluaran.index') }}?date={{ $hari->tanggal_harian }}" class="btn-detail">Lihat Rincian</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada data pengeluaran yang disetujui.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

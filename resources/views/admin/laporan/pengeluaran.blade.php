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
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('admin.laporan.pengeluaran.export', request()->query()) }}" style="background: #166534; color: white; padding: 10px 18px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
            <a href="{{ route('admin.operasional.pengeluaran.index') }}" style="background: var(--text-main); color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 500;">Validasi Pengeluaran</a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="padding: 16px 24px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.laporan.pengeluaran') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
            </div>
            <div>
                <button type="submit" style="padding: 9px 18px; background: #000000; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
            </div>
            @if(request('start_date') || request('end_date'))
            <div>
                <a href="{{ route('admin.laporan.pengeluaran') }}" style="padding: 9px 14px; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block;">
                    Reset
                </a>
            </div>
            @endif
        </form>
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

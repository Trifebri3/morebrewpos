@extends('kasir.layouts.app')

@section('content')
<style>
    .container { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .summary-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); }
    .summary-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500; }
    .summary-value { font-size: 24px; font-weight: 700; color: var(--text-main); }
    
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 500; color: var(--text-muted); background: #fafafa; }
    .table tr:last-child td { border-bottom: none; }
    
    .text-danger { color: #dc2626; font-weight: 500; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-pending { background: #fef08a; color: #854d0e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
</style>

<div class="container" style="max-width: 100%; padding: 24px;">
    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Laporan Belanja / Pengeluaran</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Rekapitulasi uang keluar untuk belanja keperluan dari luar oleh kasir hari ini.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('kasir.laporan.pengeluaran.export') }}" style="background: #166534; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
            <span style="background: #fee2e2; color: #991b1b; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px;">Hari Ini: {{ date('d M Y') }}</span>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Alokasi Kas Harian</div>
            <div class="summary-value">Rp {{ number_format($budget, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Belanja (Keluar)</div>
            <div class="summary-value text-danger">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card" style="background: #f8fafc; border-color: #cbd5e1;">
            <div class="summary-label">Sisa Saldo Kas Belanja</div>
            <div class="summary-value" style="color: #0f172a;">Rp {{ number_format($sisaBudget, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Rincian Pengeluaran Kasir Hari Ini</span>
            <a href="{{ route('kasir.pengeluaran') }}" style="background: var(--text-main); color: white; padding: 8px 16px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">+ Catat Belanja Baru</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu & Tanggal</th>
                    <th>Nama Item / Keperluan</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Nominal Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i | d M Y') }}</td>
                    <td style="font-weight: 500;">{{ $item->nama_item }}</td>
                    <td style="color: var(--text-muted);">{{ $item->keterangan ?: '-' }}</td>
                    <td><span class="badge badge-{{ $item->status }}">{{ strtoupper($item->status) }}</span></td>
                    <td style="text-align: right;" class="text-danger">- Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">Belum ada laporan pengeluaran belanja hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('kasir.layouts.app')

@section('content')
<style>
    .container { max-width: 1000px; margin: 0 auto; padding: 32px 20px; }
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .summary-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); }
    .summary-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500; }
    .summary-value { font-size: 24px; font-weight: 700; color: var(--text-main); }
    
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 14px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 600; color: var(--text-muted); background: #fafafa; font-size: 12px; text-transform: uppercase; }
    .table tr:last-child td { border-bottom: none; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-buka { background: #eff6ff; color: #1d4ed8; }
    .badge-tutup { background: #f1f5f9; color: #475569; }
</style>

<div class="container" style="max-width: 100%; padding: 24px;">
    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Laporan Shift & Sesi Kasir</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Rekapitulasi riwayat pembukaan laci kasir, penjualan shift, dan setoran fisik.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('kasir.laporan.shift.export') }}" style="background: #166534; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
            <span style="background: #e0f2fe; color: #0284c7; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px;">Hari Ini: {{ date('d M Y') }}</span>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Status Shift Sekarang</div>
            <div class="summary-value" style="color: {{ isset($activeSession) && $activeSession ? '#166534' : '#64748b' }};">
                {{ isset($activeSession) && $activeSession ? '🟢 Shift Aktif' : '⚪ Shift Tertutup' }}
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Modal Awal Sesi Aktif</div>
            <div class="summary-value">Rp {{ number_format($activeSession ? $activeSession->modal_awal : 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Riwayat Sesi Saya</div>
            <div class="summary-value">{{ count($sesis ?? []) }} Sesi</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Riwayat Sesi Shift Kasir</span>
            <a href="{{ route('kasir.tutup') }}" style="background: var(--text-main); color: white; padding: 6px 14px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500;">
                Kelola Shift (Buka / Tutup Kasir)
            </a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Sesi</th>
                    <th>Waktu Buka</th>
                    <th>Waktu Tutup</th>
                    <th>Modal Awal</th>
                    <th>Total Penjualan</th>
                    <th>Uang Fisik</th>
                    <th>Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sesis as $s)
                <tr>
                    <td style="font-family: monospace; font-weight: 600; color: #2563eb;">SESI-{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $s->waktu_buka ? $s->waktu_buka->format('d M Y H:i') : '-' }}</td>
                    <td>{{ $s->waktu_tutup ? $s->waktu_tutup->format('d M Y H:i') : 'Sedang Berjalan' }}</td>
                    <td>Rp {{ number_format($s->modal_awal ?? 0, 0, ',', '.') }}</td>
                    <td style="color: #166534; font-weight: 600;">Rp {{ number_format($s->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($s->uang_fisik ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @php $diff = $s->selisih ?? 0; @endphp
                        <span style="font-weight: 700; color: {{ $diff == 0 ? '#166534' : ($diff < 0 ? '#dc2626' : '#2563eb') }};">
                            {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                        </span>
                    </td>
                    <td><span class="badge {{ $s->status === 'buka' ? 'badge-buka' : 'badge-tutup' }}">{{ strtoupper($s->status ?? 'BUKA') }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 32px;">Belum ada riwayat sesi kasir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

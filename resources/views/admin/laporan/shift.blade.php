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

    .btn-export { background: #166534; color: white; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background 0.2s; }
    .btn-export:hover { background: #14532d; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-buka { background: #eff6ff; color: #1d4ed8; }
    .badge-tutup { background: #f1f5f9; color: #475569; }
</style>

<div style="padding: 24px 32px 40px;">
    <!-- Title & Export -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 6px 0;">Laporan Sesi Shift & Rekap Kasir</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">Laporan pembukaan dan penutupan laci kasir (End of Day), modal awal, dan selisih kas fisik.</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan.shift.export', request()->query()) }}" class="btn-export">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #2563eb;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Sesi Kasir</div>
            <div style="font-size: 26px; font-weight: 800; color: #1e293b;">{{ $totalSesi ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Sesi</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #16a34a;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Pendapatan Sesi</div>
            <div style="font-size: 26px; font-weight: 800; color: #166534;">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Selisih Laci Fisik</div>
            <div style="font-size: 26px; font-weight: 800; color: {{ ($totalSelisih ?? 0) != 0 ? '#b91c1c' : '#166534' }};">
                Rp {{ number_format($totalSelisih ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="padding: 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.laporan.shift') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
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
                    Filter Shift
                </button>
            </div>
            @if($startDate || $endDate)
            <div>
                <a href="{{ route('admin.laporan.shift') }}" style="padding: 9px 16px; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block;">
                    Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Riwayat Sesi Shift Kasir</h2>
            <span style="font-size: 13px; color: var(--text-muted);">{{ count($sesis ?? []) }} Sesi Ditemukan</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Sesi</th>
                        <th>Kasir</th>
                        <th>Waktu Buka</th>
                        <th>Waktu Tutup</th>
                        <th>Modal Awal</th>
                        <th>Penjualan</th>
                        <th>Kas Fisik Laci</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesis as $s)
                    <tr>
                        <td style="font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb;">
                            SESI-{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <strong style="color: #111827;">{{ $s->user ? $s->user->name : 'Kasir' }}</strong>
                        </td>
                        <td>{{ $s->waktu_buka ? $s->waktu_buka->format('d M Y H:i') : '-' }}</td>
                        <td>{{ $s->waktu_tutup ? $s->waktu_tutup->format('d M Y H:i') : 'Sedang Aktif' }}</td>
                        <td style="font-weight: 600;">Rp {{ number_format($s->modal_awal ?? 0, 0, ',', '.') }}</td>
                        <td style="color: #166534; font-weight: 700;">Rp {{ number_format($s->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                        <td style="font-weight: 600;">Rp {{ number_format($s->uang_fisik ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @php $diff = $s->selisih ?? 0; @endphp
                            <span style="font-weight: 700; color: {{ $diff == 0 ? '#166534' : ($diff < 0 ? '#dc2626' : '#2563eb') }};">
                                {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $s->status === 'buka' ? 'badge-buka' : 'badge-tutup' }}">
                                {{ strtoupper($s->status ?? 'BUKA') }}
                            </span>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">{{ $s->catatan ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 48px; color: #64748b;">
                            Belum ada riwayat sesi shift kasir pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

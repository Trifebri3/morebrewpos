@extends('admin.layouts.app', $data)

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-success { background: #16a34a; color: white; padding: 6px 12px; border-radius: 4px; font-size: 13px; text-decoration: none; border: none; cursor: pointer; }
    .btn-danger { background: #dc2626; color: white; padding: 6px 12px; border-radius: 4px; font-size: 13px; text-decoration: none; border: none; cursor: pointer; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    
    .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-pending { background: #fef08a; color: #854d0e; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    .summary-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .summary-title { font-size: 14px; color: var(--text-muted); font-weight: 500; }
    .summary-value { font-size: 24px; font-weight: 700; color: var(--text-main); }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Belanja Harian (Kas Kecil)</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola batas anggaran dan setujui laporan belanja dari kasir.</p>
</div>

<div style="padding: 24px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; gap: 24px; margin-bottom: 32px;">
        <!-- Budget Setting -->
        <div class="summary-card">
            <div class="summary-title">Batas Anggaran Harian (Alokasi)</div>
            <div class="summary-value">Rp {{ number_format($kedai->budget_harian, 0, ',', '.') }}</div>
            <form action="{{ route('admin.operasional.pengeluaran.budget') }}" method="POST" style="margin-top: 12px; display: flex; gap: 8px;">
                @csrf @method('PUT')
                <input type="number" name="budget_harian" value="{{ (int)$kedai->budget_harian }}" class="form-control" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 14px;" required>
                <button type="submit" class="btn-primary" style="padding: 8px 16px;">Update</button>
            </form>
        </div>
        
        <div class="summary-card">
            <div class="summary-title">Total Disetujui (Bulan Ini)</div>
            <div class="summary-value">Rp {{ number_format($totalApproved, 0, ',', '.') }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Uang keluar aktual bulan ini.</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Dilaporkan Oleh</th>
                <th>Item Belanja</th>
                <th>Nominal</th>
                <th>Keterangan</th>
                <th>Bukti Foto</th>
                <th>Status</th>
                <th width="180">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluarans as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td style="font-weight: 500;">{{ $item->nama_item }}</td>
                    <td style="font-weight: 600;">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td style="color: var(--text-muted); font-size: 13px;">{{ $item->keterangan ?: '-' }}</td>
                    <td>
                        @if($item->bukti)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($item->bukti) }}" target="_blank" style="color: #0284c7; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lihat
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-size: 13px;">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->status }}">
                            {{ strtoupper($item->status) }}
                        </span>
                        @if($item->approver)
                            <div style="font-size: 11px; margin-top: 4px; color: var(--text-muted);">by {{ $item->approver->name }}</div>
                        @endif
                    </td>
                    <td>
                        @if($item->status == 'pending')
                            <form action="{{ route('admin.operasional.pengeluaran.approve', $item->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-success">Setujui</button>
                            </form>
                            <form action="{{ route('admin.operasional.pengeluaran.reject', $item->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-danger">Tolak</button>
                            </form>
                        @else
                            <span style="color: var(--text-muted); font-size: 13px;">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada laporan pengeluaran/belanja dari Kasir.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

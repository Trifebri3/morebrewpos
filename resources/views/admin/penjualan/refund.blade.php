@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .badge-error { background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; display: inline-block; }
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { opacity: 0.9; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>{{ $data['title'] ?? 'Kelola Refund' }}</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">{{ $data['subtitle'] ?? 'Daftar transaksi yang dikembalikan.' }}</p>
</div>

<div style="padding: 24px 40px 40px;">
    
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

    <div style="margin-bottom: 24px; background: white; padding: 24px; border-radius: 8px; border: 1px solid var(--border-color);">
        <h3 style="margin-top: 0; font-size: 16px; color: var(--text-main); margin-bottom: 16px;">Ajukan Refund Baru</h3>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 16px;">Silakan pilih transaksi yang ingin di-refund dari menu <a href="{{ route('admin.penjualan.transaksi') }}" style="color: var(--text-main); font-weight: 600;">Semua Transaksi</a>, atau Anda bisa menggunakan tombol "Refund" pada riwayat transaksi.</p>
    </div>

    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px; color: var(--text-main);">Riwayat Transaksi Refund</h3>
    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Refund</th>
                    <th>No. Invoice</th>
                    <th>Total Dikembalikan</th>
                    <th>Alasan Refund</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $t)
                    <tr>
                        <td style="color: var(--text-muted);">
                            @if($t->refunded_at)
                                {{ \Carbon\Carbon::parse($t->refunded_at)->format('d/m/Y H:i:s') }}
                            @else
                                {{ $t->updated_at->format('d/m/Y H:i:s') }}
                            @endif
                        </td>
                        <td style="font-weight: bold; color: var(--text-main);">#{{ $t->invoice_number }}</td>
                        <td style="font-weight: 600; color: #ef4444;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td>{{ $t->refund_reason ?? 'Tidak ada alasan' }}</td>
                        <td><span class="badge-error">Dikembalikan</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada riwayat refund.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $refunds->links() }}
    </div>

</div>
@endsection

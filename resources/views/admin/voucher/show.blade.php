@extends('admin.layouts.app', $data ?? ['title' => 'Detail Voucher'])

@section('content')
<style>
    .btn-secondary { background: white; color: var(--text-main); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); margin-top: 24px; }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .info-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px; margin-bottom: 24px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; }
    .info-item .label { font-size: 13px; color: var(--text-muted); margin-bottom: 4px; }
    .info-item .value { font-size: 15px; font-weight: 600; color: var(--text-main); }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Detail Penggunaan Voucher: {{ $voucher->kode }}</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Melihat siapa saja yang telah menggunakan voucher ini.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('admin.penjualan.voucher.export', $voucher) }}" style="padding: 10px 16px; background: #166534; color: white; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Ekspor Excel (.xlsx)
        </a>
        <a href="{{ route('admin.penjualan.voucher.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>

<div style="padding: 24px 40px 40px;">
    
    <div class="info-card">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; color: var(--text-main);">Informasi Voucher</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nama Promo</div>
                <div class="value">{{ $voucher->nama }}</div>
            </div>
            <div class="info-item">
                <div class="label">Nilai Diskon</div>
                <div class="value">{{ $voucher->tipe_diskon === 'persen' ? round($voucher->nilai_diskon) . '%' : 'Rp ' . number_format($voucher->nilai_diskon, 0, ',', '.') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Total Terpakai</div>
                <div class="value">{{ $voucher->terpakai }} kali</div>
            </div>
            <div class="info-item">
                <div class="label">Sisa Kuota</div>
                <div class="value">{{ $voucher->kuota !== null ? $voucher->kuota : 'Tanpa Batas' }}</div>
            </div>
        </div>
    </div>

    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px; color: var(--text-main);">Riwayat Transaksi</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Waktu & Tanggal</th>
                <th>No. Invoice</th>
                <th>Nama Pelanggan</th>
                <th>Total Belanja</th>
                <th>Diskon Diterima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $transaksi)
                <tr>
                    <td>
                        <strong>{{ $transaksi->created_at->format('d M Y') }}</strong><br>
                        <small style="color: var(--text-muted);">{{ $transaksi->created_at->format('H:i') }} WIB</small>
                    </td>
                    <td style="font-family: monospace; font-weight: 600;">INV-{{ $transaksi->invoice_number }}</td>
                    <td>{{ $transaksi->customer_name ?: 'Pelanggan Umum' }}</td>
                    <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                    <td style="color: #ef4444; font-weight: 600;">- Rp {{ number_format($transaksi->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada riwayat penggunaan untuk voucher ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@extends('admin.layouts.app', $data)

@section('content')
<style>
    .btn-secondary { background: white; color: var(--text-main); padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover, .btn-secondary.active { background: var(--bg-color); border-color: var(--text-main); }
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .summary-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px; text-align: center; }
    .summary-card h3 { color: var(--text-muted); font-size: 14px; margin-bottom: 8px; font-weight: 500; }
    .summary-card p { color: var(--text-main); font-size: 24px; font-weight: bold; margin: 0; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>{{ $data['title'] ?? 'Laporan Penjualan' }}</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">{{ $data['subtitle'] ?? '' }}</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center;">
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'harian']) }}" class="btn-secondary {{ $type == 'harian' ? 'active' : '' }}">Laporan Harian</a>
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'bulanan']) }}" class="btn-secondary {{ $type == 'bulanan' ? 'active' : '' }}">Laporan Bulanan</a>
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'lengkap']) }}" class="btn-secondary {{ $type == 'lengkap' ? 'active' : '' }}">Rekapan Lengkap</a>
    </div>
</div>

<div style="padding: 24px 40px 40px;">

    <!-- Filter Pertanggal -->
    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div style="font-weight: 500; font-size: 14px;">Atau pilih rentang tanggal:</div>
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" style="display: flex; gap: 12px; margin: 0;">
            <input type="hidden" name="type" value="pertanggal">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="btn-secondary" style="background: white;" required>
            <span style="display: flex; align-items: center;">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="btn-secondary" style="background: white;" required>
            <button type="submit" class="btn-primary">Tampilkan Lap. Pertanggal</button>
        </form>
    </div>

    <!-- Ringkasan Singkat -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
        <div class="summary-card">
            <h3>Total Transaksi</h3>
            <p>{{ $transaksis->count() }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Pemasukan</h3>
            <p>Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Produk Terjual</h3>
            <p>{{ array_sum($itemsSold) }}</p>
        </div>
    </div>

    <!-- Detail Tabel -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <h2 style="font-size: 16px; margin-bottom: 16px;">Riwayat Transaksi</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td style="font-weight: bold; color: var(--text-main);">#{{ $t->invoice_number }}</td>
                            <td>{{ $t->customer_name ?: 'Umum' }}</td>
                            <td style="font-weight: 500;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted);">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            <h2 style="font-size: 16px; margin-bottom: 16px;">Rekapan Produk Terjual</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: right;">Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsSold as $name => $qty)
                        <tr>
                            <td>{{ $name }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $qty }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted);">Belum ada produk terjual.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

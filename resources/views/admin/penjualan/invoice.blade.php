@extends('admin.layouts.app', $data)

@section('content')
<style>
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .summary-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px; text-align: center; }
    .summary-card h3 { color: var(--text-muted); font-size: 14px; margin-bottom: 8px; font-weight: 500; }
    .summary-card p { color: var(--text-main); font-size: 24px; font-weight: bold; margin: 0; }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; cursor: pointer; }
    .btn-action:hover { background: var(--bg-color); }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>{{ $data['title'] ?? 'Daftar Invoice' }}</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">{{ $data['subtitle'] ?? 'Berikut adalah semua data invoice transaksi dari kasir beserta rekapan totalnya.' }}</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center;">
        <a href="{{ route('admin.penjualan.invoice', ['type' => 'harian']) }}" class="btn-action {{ $type == 'harian' ? 'active' : '' }}" style="{{ $type == 'harian' ? 'background: var(--text-main); color: white;' : '' }}">Harian</a>
        <a href="{{ route('admin.penjualan.invoice', ['type' => 'mingguan']) }}" class="btn-action {{ $type == 'mingguan' ? 'active' : '' }}" style="{{ $type == 'mingguan' ? 'background: var(--text-main); color: white;' : '' }}">Mingguan</a>
        <a href="{{ route('admin.penjualan.invoice', ['type' => 'bulanan']) }}" class="btn-action {{ $type == 'bulanan' ? 'active' : '' }}" style="{{ $type == 'bulanan' ? 'background: var(--text-main); color: white;' : '' }}">Bulanan</a>
        <a href="{{ route('admin.penjualan.invoice', ['type' => 'pertanggal']) }}" class="btn-action {{ $type == 'pertanggal' ? 'active' : '' }}" style="{{ $type == 'pertanggal' ? 'background: var(--text-main); color: white;' : '' }}">Pertanggal</a>
        <a href="{{ route('admin.penjualan.invoice', ['type' => 'lengkap']) }}" class="btn-action {{ $type == 'lengkap' ? 'active' : '' }}" style="{{ $type == 'lengkap' ? 'background: var(--text-main); color: white;' : '' }}">Semua</a>
    </div>
</div>

<div style="padding: 24px 40px 40px;">
    @if($type === 'pertanggal')
    <!-- Filter Pertanggal -->
    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div style="font-weight: 500; font-size: 14px;">Pilih rentang tanggal invoice:</div>
        <form method="GET" action="{{ route('admin.penjualan.invoice') }}" style="display: flex; gap: 12px; margin: 0; align-items: center;">
            <input type="hidden" name="type" value="pertanggal">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="btn-action" style="background: white; padding: 8px 12px;" required>
            <span>s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="btn-action" style="background: white; padding: 8px 12px;" required>
            <button type="submit" class="btn-action" style="background: var(--text-main); color: white; padding: 8px 16px; font-weight: bold;">Tampilkan</button>
        </form>
    </div>
    @endif
    <!-- Rekapan Singkat -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px;">
        <div class="summary-card">
            <h3>Total Invoice / Transaksi</h3>
            <p>{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Pendapatan Terkumpul</h3>
            <p>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Tabel Daftar Invoice -->
    <h2 style="font-size: 16px; margin-bottom: 16px;">Semua Daftar Invoice</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal & Waktu</th>
                <th>Nama Pelanggan</th>
                <th>Metode Bayar</th>
                <th>Total Belanja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $t)
                <tr>
                    <td style="font-weight: bold; color: var(--text-main);">#{{ $t->invoice_number }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->customer_name ?: 'Pelanggan Umum' }}</td>
                    <td style="text-transform: capitalize;">{{ $t->payment_method ?? 'Cash' }}</td>
                    <td style="font-weight: 500;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Lunas</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada invoice transaksi saat ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

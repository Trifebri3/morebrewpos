@extends('kasir.layouts.app')

@section('content')
<style>
    .container { max-width: 1000px; margin: 0 auto; padding: 32px 20px; }
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    
    .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .summary-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color); }
    .summary-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500; }
    .summary-value { font-size: 24px; font-weight: 700; color: var(--text-main); }
    
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 500; color: var(--text-muted); background: #fafafa; }
    .table tr:last-child td { border-bottom: none; }
    
    .text-success { color: #16a34a; font-weight: 500; }
    .text-danger { color: #dc2626; font-weight: 500; }
</style>

<div class="container" style="max-width: 100%; padding: 24px;">
    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Laporan Kas & Penjualan Harian</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Rekapitulasi penjualan, pengeluaran kasir, dan sisa uang kas hari ini.</p>
        </div>
        <div>
            <span style="background: #e0f2fe; color: #0284c7; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px;">Hari Ini: {{ date('d M Y') }}</span>
        </div>
    </div>

    <div class="summary-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="summary-card">
            <div class="summary-label">Total Transaksi</div>
            <div class="summary-value">{{ $riwayat->count() }} Transaksi</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Penjualan</div>
            <div class="summary-value text-success">+ Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Detail Transaksi Penjualan
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>No Invoice</th>
                    <th>Pelanggan</th>
                    <th>Item</th>
                    <th>Metode Bayar</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $transaksi)
                <tr>
                    <td>{{ $transaksi->created_at->format('H:i') }}</td>
                    <td style="font-family: monospace; font-weight: 600;">{{ $transaksi->invoice_number }}</td>
                    <td>{{ $transaksi->customer_name ?? '-' }} {{ $transaksi->order_type === 'take_away' ? '(Take Away)' : '' }}</td>
                    <td>
                        @php
                            $itemsDesc = '';
                            if(is_array($transaksi->items)) {
                                $itemStrings = array_map(function($item) {
                                    return ($item['qty'] ?? 1) . 'x ' . ($item['name'] ?? '');
                                }, $transaksi->items);
                                $itemsDesc = implode(', ', $itemStrings);
                            }
                        @endphp
                        {{ Str::limit($itemsDesc, 50) }}
                    </td>
                    <td>{{ strtoupper($transaksi->payment_method) }}</td>
                    <td style="text-align: right;" class="text-success">+ Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada transaksi penjualan hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('admin.layouts.app', $data)

@section('content')
<style>
    .transaction-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 24px; overflow: hidden; }
    .transaction-header { padding: 16px 24px; background: #f9fafb; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .transaction-body { padding: 24px; }
    .item-list { width: 100%; border-collapse: collapse; }
    .item-list th, .item-list td { padding: 12px 0; border-bottom: 1px dashed var(--border-color); font-size: 14px; }
    .item-list th { color: var(--text-muted); font-weight: 500; text-align: left; }
    .item-list td { color: var(--text-main); }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .badge-success { background: #dcfce7; color: #166534; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>{{ $data['title'] ?? 'Riwayat Transaksi Detail' }}</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">{{ $data['subtitle'] ?? 'Detail barang yang dibeli per transaksi' }}</p>
</div>

<div style="padding: 24px 40px 40px;">
    @forelse($transaksis as $t)
        <div class="transaction-card">
            <div class="transaction-header">
                <div>
                    <h3 style="margin: 0; font-size: 16px;">Invoice <span style="color: var(--text-main);">#{{ $t->invoice_number }}</span></h3>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">{{ $t->created_at->format('d F Y, H:i') }} • Pelanggan: <strong>{{ $t->customer_name ?: 'Umum' }}</strong></div>
                </div>
                <div style="text-align: right;">
                    <span class="badge badge-success">Lunas ({{ ucfirst($t->payment_method) }})</span>
                    <h3 style="margin: 8px 0 0 0; font-size: 18px;">Rp {{ number_format($t->total, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="transaction-body">
                <table class="item-list">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Item Produk</th>
                            <th style="text-align: center;">Kuantitas</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $items = is_string($t->items) ? json_decode($t->items, true) : $t->items;
                        @endphp
                        @if(is_array($items))
                            @foreach($items as $item)
                                <tr>
                                    <td style="font-weight: 500;">{{ $item['name'] ?? 'Produk' }}</td>
                                    <td style="text-align: center;">{{ $item['qty'] ?? 1 }}x</td>
                                    <td style="text-align: right;">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 500;">Rp {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4">Gagal memuat detail pesanan.</td></tr>
                        @endif
                    </tbody>
                </table>
                @if($t->discount_amount > 0 || $t->tax > 0)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 40px; font-size: 14px;">
                    @if($t->discount_amount > 0)
                        <div><span style="color: var(--text-muted);">Diskon:</span> <span style="font-weight: 500; color: #dc2626;">-Rp {{ number_format($t->discount_amount, 0, ',', '.') }}</span></div>
                    @endif
                    @if($t->tax > 0)
                        <div><span style="color: var(--text-muted);">Pajak (PB1):</span> <span style="font-weight: 500;">Rp {{ number_format($t->tax, 0, ',', '.') }}</span></div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align: center; color: var(--text-muted); padding: 40px; background: white; border-radius: 8px; border: 1px solid var(--border-color);">
            Belum ada riwayat transaksi detail saat ini.
        </div>
    @endforelse
    
    <div style="margin-top: 24px;">
        {{ $transaksis->links() }}
    </div>
</div>
@endsection

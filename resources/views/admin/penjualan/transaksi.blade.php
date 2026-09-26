@extends('admin.layouts.app', $data)

@section('content')
<style>
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; cursor: pointer; }
    .btn-action:hover { background: var(--bg-color); }
    .badge-success { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; display: inline-block; }
    .badge-neutral { background: #f3f4f6; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; display: inline-block; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>{{ $data['title'] ?? 'Semua Transaksi Masuk' }}</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">{{ $data['subtitle'] ?? 'Pantau seluruh transaksi terbaru secara real-time yang terjadi di kasir.' }}</p>
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

    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Transaksi</th>
                    <th>No. Invoice</th>
                    <th>Tipe Order</th>
                    <th>Total Item</th>
                    <th>Total Bayar</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $t)
                    @php
                        $items = is_string($t->items) ? json_decode($t->items, true) : $t->items;
                        $totalItem = is_array($items) ? count($items) : 0;
                    @endphp
                    <tr>
                        <td style="color: var(--text-muted);">{{ $t->created_at->format('d/m/Y H:i:s') }}</td>
                        <td style="font-weight: bold; color: var(--text-main);">#{{ $t->invoice_number }}</td>
                        <td>
                            @if($t->order_type == 'dine_in')
                                <span class="badge-neutral">Dine In</span>
                            @else
                                <span class="badge-neutral">Take Away</span>
                            @endif
                        </td>
                        <td>{{ $totalItem }} Produk</td>
                        <td style="font-weight: 500;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td style="text-transform: capitalize;">{{ $t->payment_method ?? 'Cash' }}</td>
                        <td>
                            @if($t->is_refunded)
                                <span class="badge-neutral" style="background: #fee2e2; color: #991b1b;">Refunded</span>
                            @else
                                <span class="badge-success">Berhasil</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.penjualan.riwayat') }}" class="btn-action">Detail</a>
                                @if(!$t->is_refunded)
                                    <button type="button" class="btn-action" style="border-color: #fca5a5; color: #ef4444; background: transparent;" onclick="openRefundModal({{ $t->id }}, '{{ $t->invoice_number }}')">Refund</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada transaksi sama sekali.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $transaksis->links() }}
    </div>

</div>

<!-- Modal Refund -->
<div id="refundModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 400px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; font-size: 18px;">Proses Refund</h3>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 20px;">Invoice: <strong id="modalInvoiceText"></strong></p>
        
        <form id="refundForm" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; margin-bottom: 8px; font-weight: 500;">Alasan Refund (Wajib)</label>
                <textarea name="refund_reason" rows="3" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; box-sizing: border-box; outline: none; font-size: 13px;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeRefundModal()" style="padding: 8px 16px; background: white; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">Batal</button>
                <button type="submit" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">Proses Refund</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRefundModal(id, invoiceNumber) {
        document.getElementById('modalInvoiceText').innerText = '#' + invoiceNumber;
        document.getElementById('refundForm').action = '/admin/penjualan/refund/' + id;
        document.getElementById('refundModal').style.display = 'flex';
    }

    function closeRefundModal() {
        document.getElementById('refundModal').style.display = 'none';
        document.getElementById('refundForm').reset();
    }
</script>
@endsection

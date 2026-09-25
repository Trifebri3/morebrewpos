@extends('kasir.layouts.app', $data ?? [])

@section('content')
<style>
    .page-header { padding: 32px 40px 0; }
    .page-header h1 { font-size: 24px; font-weight: 600; }
    .header-desc { color: var(--text-muted); font-size: 14px; margin-top: 8px; }
    
    .invoice-card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin: 24px 40px; }
    
    .filter-bar { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--border-color); background: #fafafa; }
    .search-box { display: flex; align-items: center; background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 16px; width: 300px; }
    .search-box input { border: none; outline: none; margin-left: 8px; width: 100%; font-size: 14px; }
    
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .table th { font-weight: 600; color: var(--text-muted); }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover { background-color: #f8fafc; }
    
    .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-success { background: #dcfce7; color: #166534; }
    .status-refund { background: #fee2e2; color: #991b1b; }
    
    .btn-action { background: #f1f5f9; color: #334155; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: 0.2s; }
    .btn-action:hover { background: #e2e8f0; }
</style>

<div class="page-header">
    <h1>Riwayat Invoice</h1>
    <div class="header-desc">Daftar semua transaksi penjualan yang telah selesai atau di-refund.</div>
</div>

<div class="invoice-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Cari No. Invoice atau Nama...">
        </div>
        <div>
            <select style="padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none;">
                <option>Hari Ini ({{ \Carbon\Carbon::now()->format('d M Y') }})</option>
                <option>Kemarin</option>
                <option>7 Hari Terakhir</option>
            </select>
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Total Tagihan</th>
                <th>Metode</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td>{{ $inv->created_at->format('H:i') }}</td>
                <td style="font-family: monospace; font-weight: 600;">{{ $inv->invoice_number }}</td>
                <td>{{ $inv->customer_name ?? '-' }} {{ $inv->order_type === 'take_away' ? '(Take Away)' : '' }}</td>
                <td style="font-weight: 600;">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                <td>{{ strtoupper($inv->payment_method) }}</td>
                <td><span class="status-badge status-success">SELESAI</span></td>
                <td style="text-align: right;">
                    @php
                        $detailData = [
                            "invoice" => $inv->invoice_number,
                            "time" => $inv->created_at->format("d M Y H:i"),
                            "customer" => $inv->customer_name ?? "-",
                            "type" => $inv->order_type === "take_away" ? "Take Away" : "Dine In",
                            "items" => is_string($inv->items) ? json_decode($inv->items, true) : $inv->items,
                            "subtotal" => $inv->subtotal,
                            "discount" => $inv->discount_amount,
                            "tax" => $inv->tax,
                            "total" => $inv->total,
                            "method" => strtoupper($inv->payment_method),
                            "paid" => $inv->amount_paid
                        ];
                    @endphp
                    <button class="btn-action" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" onclick='showDetail(@json($detailData))'>Detail</button>
                    <button class="btn-action" onclick="cetakUlang({{ $inv->id }})">Cetak Ulang</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada invoice.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Detail Transaksi -->
<div id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 500px; max-width: 90%; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px;">Detail Transaksi</h3>
            <button onclick="document.getElementById('detailModal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 24px; max-height: 70vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 13px; color: #64748b;">No. Invoice</div>
                    <div style="font-weight: 600; font-family: monospace;" id="detail-invoice"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 13px; color: #64748b;">Waktu</div>
                    <div style="font-weight: 500;" id="detail-time"></div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px dashed #cbd5e1;">
                <div>
                    <div style="font-size: 13px; color: #64748b;">Pelanggan</div>
                    <div style="font-weight: 500;" id="detail-customer"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 13px; color: #64748b;">Tipe Pesanan</div>
                    <div style="font-weight: 500;" id="detail-type"></div>
                </div>
            </div>

            <div style="font-weight: 600; margin-bottom: 12px;">Item Pesanan</div>
            <div id="detail-items" style="margin-bottom: 24px;"></div>

            <div style="border-top: 1px dashed #cbd5e1; padding-top: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #64748b;">Subtotal</span>
                    <span id="detail-subtotal"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #dc2626;" id="detail-discount-row">
                    <span>Diskon</span>
                    <span id="detail-discount"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;" id="detail-tax-row">
                    <span style="color: #64748b;">Pajak (11%)</span>
                    <span id="detail-tax"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-weight: 700; font-size: 16px;">
                    <span>Total Tagihan</span>
                    <span id="detail-total"></span>
                </div>
                
                <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: #64748b;">Metode Pembayaran</span>
                        <span style="font-weight: 600;" id="detail-method"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: #64748b;">Jumlah Dibayar</span>
                        <span id="detail-paid"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function showDetail(data) {
    document.getElementById('detail-invoice').innerText = data.invoice;
    document.getElementById('detail-time').innerText = data.time;
    document.getElementById('detail-customer').innerText = data.customer;
    document.getElementById('detail-type').innerText = data.type;
    
    const itemsContainer = document.getElementById('detail-items');
    itemsContainer.innerHTML = '';
    if(Array.isArray(data.items)) {
        data.items.forEach(item => {
            const qty = item.qty || 1;
            const price = item.price || 0;
            const row = document.createElement('div');
            row.style = 'display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;';
            row.innerHTML = `
                <div>
                    <div>${item.name || 'Produk'}</div>
                    <div style="font-size: 12px; color: #64748b;">${qty} x ${formatRupiah(price)}</div>
                </div>
                <div style="font-weight: 500;">${formatRupiah(qty * price)}</div>
            `;
            itemsContainer.appendChild(row);
        });
    }

    document.getElementById('detail-subtotal').innerText = formatRupiah(data.subtotal);
    
    if (parseFloat(data.discount) > 0) {
        document.getElementById('detail-discount-row').style.display = 'flex';
        document.getElementById('detail-discount').innerText = '-' + formatRupiah(data.discount);
    } else {
        document.getElementById('detail-discount-row').style.display = 'none';
    }

    if (parseFloat(data.tax) > 0) {
        document.getElementById('detail-tax-row').style.display = 'flex';
        document.getElementById('detail-tax').innerText = formatRupiah(data.tax);
    } else {
        document.getElementById('detail-tax-row').style.display = 'none';
    }

    document.getElementById('detail-total').innerText = formatRupiah(data.total);
    document.getElementById('detail-method').innerText = data.method;
    document.getElementById('detail-paid').innerText = formatRupiah(data.paid);

    document.getElementById('detailModal').style.display = 'flex';
}

function cetakUlang(id) {
    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerText = "Loading...";
    btn.disabled = true;

    fetch(`/kasir/cetak-ulang/${id}`)
        .then(res => res.text())
        .then(html => {
            const oldIframe = document.getElementById('print-iframe');
            if (oldIframe) oldIframe.remove();
            
            const iframe = document.createElement('iframe');
            iframe.id = 'print-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
            
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                btn.innerText = originalText;
                btn.disabled = false;
            }, 500);
        })
        .catch(err => {
            console.error(err);
            btn.innerText = originalText;
            btn.disabled = false;
            alert("Gagal mencetak struk.");
        });
}
</script>
@endsection

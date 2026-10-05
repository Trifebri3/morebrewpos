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
                    <button class="btn-action" onclick='cetakUlang({{ $inv->id }}, @json($detailData))'>Cetak Ulang</button>
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

<!-- Modal Detail Transaksi (Thermal Receipt 58mm Sesuai Aplikasi) -->
<div id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 380px; max-width: 95%; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
        <!-- Top Modal Header -->
        <div style="padding: 14px 20px; background: #212121; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span style="font-weight: 700; font-size: 14px;">Struk Transaksi (58mm)</span>
            </div>
            <button onclick="document.getElementById('detailModal').style.display='none'" style="background: none; border: none; font-size: 20px; color: rgba(255,255,255,0.7); cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <!-- Thermal Receipt Container -->
        <div style="padding: 20px 22px; max-height: 72vh; overflow-y: auto; background: #fdfdfd; font-family: 'Courier New', Courier, monospace; color: black; font-size: 11px;">
            <!-- Header Logo & Alamat 2 Baris -->
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="max-height: 40px; margin-bottom: 4px; object-fit: contain;">
                <div style="font-size: 10px; line-height: 1.25;">something, between home and<br>everywhare</div>
                <div style="font-size: 10px; line-height: 1.3; margin-top: 6px;">
                    Jl. Sasmitatmaja No.6, Paledang<br>
                    Kec. Lengkong, Kota Bandung
                </div>
                <div style="margin-top: 10px; font-weight: bold; font-size: 13px;" id="detail-invoice">INV-XXXX</div>
                <div style="font-size: 10px; color: #333;" id="detail-time">01/01/2026 00:00</div>
            </div>

            <!-- Dashed Line -->
            <div style="border-bottom: 1px dashed black; margin: 8px 0;"></div>

            <!-- Pelanggan & Tipe Order -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                    <span>Pelanggan:</span>
                    <strong id="detail-customer">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Tipe:</span>
                    <strong id="detail-type">Dine In</strong>
                </div>
            </div>

            <!-- Dashed Line -->
            <div style="border-bottom: 1px dashed black; margin: 8px 0;"></div>

            <!-- Items List -->
            <div id="detail-items" style="margin: 4px 0;"></div>

            <!-- Dashed Line -->
            <div style="border-bottom: 1px dashed black; margin: 8px 0;"></div>

            <!-- Subtotal, Diskon, Pajak, Total -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span>Subtotal:</span>
                    <span id="detail-subtotal">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;" id="detail-discount-row">
                    <span>Diskon:</span>
                    <span id="detail-discount" style="color: #dc2626;">-Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;" id="detail-tax-row">
                    <span>Pajak 11%:</span>
                    <span id="detail-tax">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px; font-weight: bold; font-size: 13px;">
                    <span>Total:</span>
                    <span id="detail-total">Rp 0</span>
                </div>
            </div>

            <!-- Dashed Line -->
            <div style="border-bottom: 1px dashed black; margin: 8px 0;"></div>

            <!-- Metode Pembayaran & Kembalian -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span>Metode:</span>
                    <strong id="detail-method" style="text-transform: uppercase;">CASH</strong>
                </div>
                <div id="detail-cash-box">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Bayar:</span>
                        <span id="detail-paid">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Kembali:</span>
                        <span id="detail-change">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Dashed Line -->
            <div style="border-bottom: 1px dashed black; margin: 8px 0;"></div>

            <!-- Footer: Terima Kasih, Wi-Fi & Instagram Berdampingan -->
            <div style="text-align: center; margin-top: 12px;">
                <div style="font-weight: bold; font-size: 11px;">TERIMA KASIH</div>
                <div style="font-size: 10px; margin-top: 2px;">Silakan datang kembali!</div>

                <!-- Solid Divider Line -->
                <div style="border-top: 1.2px solid black; margin: 10px 0 8px;"></div>

                <!-- Wi-Fi & Instagram Side-by-side -->
                @php $kData = \App\Models\Kedai::first(); @endphp
                <div style="display: flex; justify-content: space-between; align-items: flex-start; text-align: center; font-size: 8px;">
                    <div style="flex: 1; padding-right: 4px;">
                        <div style="font-weight: bold; font-size: 8.5px; margin-bottom: 2px;">Wi-Fi Area</div>
                        <div style="font-size: 7.5px;">{{ $kData && $kData->wifi_ssid ? $kData->wifi_ssid : 'moreandmore' }}</div>
                        <div style="font-size: 7px; color: #444;">pass: {{ $kData && $kData->wifi_password ? $kData->wifi_password : 'bolehmintasenyumnya?' }}</div>
                    </div>
                    <div style="width: 1px; background: #000; align-self: stretch; min-height: 26px; margin: 0 3px;"></div>
                    <div style="flex: 1; padding-left: 4px;">
                        <div style="font-weight: bold; font-size: 8.5px; margin-bottom: 2px;">Instagram</div>
                        <div style="font-size: 7.5px;">{{ '@' . ltrim(($kData && $kData->instagram ? $kData->instagram : 'morebrewcoffee'), '@') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Bottom Actions -->
        <div style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="document.getElementById('detailModal').style.display='none'" style="background: white; border: 1px solid #cbd5e1; color: #475569; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Tutup</button>
            <button id="btn-print-from-modal" style="background: #212121; border: none; color: white; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Struk Thermal
            </button>
        </div>
    </div>
</div>

<script>
function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
}

let activeDetailData = null;

function showDetail(data) {
    activeDetailData = data;
    
    document.getElementById('detail-invoice').innerText = data.invoice ? (data.invoice.startsWith('INV-') ? data.invoice : 'INV-' + data.invoice) : 'INV-XXXX';
    document.getElementById('detail-time').innerText = data.time || '-';
    document.getElementById('detail-customer').innerText = data.customer || '-';
    document.getElementById('detail-type').innerText = data.type || 'Dine In';
    
    const itemsContainer = document.getElementById('detail-items');
    itemsContainer.innerHTML = '';
    if(Array.isArray(data.items)) {
        data.items.forEach(item => {
            const qty = item.qty || 1;
            const price = item.price || 0;
            const row = document.createElement('div');
            row.style = 'margin-bottom: 6px;';
            row.innerHTML = `
                <div style="font-weight: bold; font-size: 10.5px;">${(item.name || 'ITEM').toUpperCase()}</div>
                <div style="display: flex; justify-content: space-between; font-size: 9.5px;">
                    <span>${qty} x ${formatRupiah(price)}</span>
                    <span>${formatRupiah(qty * price)}</span>
                </div>
                ${item.notes ? `<div style="font-size: 8px; font-style: italic; color: #666;">* ${item.notes}</div>` : ''}
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
    document.getElementById('detail-method').innerText = (data.method || 'CASH').toUpperCase();
    
    const isCash = (data.method || '').toLowerCase() === 'cash' || (data.method || '').toLowerCase() === 'tunai';
    const cashBox = document.getElementById('detail-cash-box');
    if (isCash) {
        cashBox.style.display = 'block';
        document.getElementById('detail-paid').innerText = formatRupiah(data.paid);
        const change = (parseFloat(data.paid) || 0) - (parseFloat(data.total) || 0);
        document.getElementById('detail-change').innerText = formatRupiah(Math.max(0, change));
    } else {
        cashBox.style.display = 'none';
    }

    document.getElementById('detailModal').style.display = 'flex';
}

document.getElementById('btn-print-from-modal').addEventListener('click', function () {
    if (activeDetailData) {
        cetakUlang(null, activeDetailData);
    }
});

function cetakUlang(id, data) {
    if (window.thermalPrinter && window.thermalPrinter.isConnected() && data) {
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "Mencetak...";
        btn.disabled = true;

        const printPayload = {
            invoiceNumber: data.invoice,
            customerName: data.customer,
            orderType: data.type === 'Take Away' ? 'take_away' : 'dine_in',
            cart: data.items,
            subtotal: parseFloat(data.subtotal),
            discountAmount: parseFloat(data.discount),
            tax: parseFloat(data.tax),
            total: parseFloat(data.total),
            paymentMethod: data.method,
            amountPaid: parseFloat(data.paid)
        };

        window.thermalPrinter.printReceipt(printPayload)
            .then(() => {
                btn.innerText = originalText;
                btn.disabled = false;
            })
            .catch(err => {
                console.error(err);
                alert("Gagal cetak: " + err.message);
                btn.innerText = originalText;
                btn.disabled = false;
            });
        return;
    }

    const btn = event.target;
    const originalText = btn ? btn.innerText : 'Cetak';
    if (btn) {
        btn.innerText = "Loading...";
        btn.disabled = true;
    }

    const targetUrl = id ? `/kasir/cetak-ulang/${id}` : `{{ route('kasir.cetak_struk') }}`;

    fetch(targetUrl, {
        method: id ? 'GET' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: id ? null : JSON.stringify({
            invoiceNumber: data.invoice,
            customerName: data.customer,
            orderType: data.type === 'Take Away' ? 'take_away' : 'dine_in',
            cart: data.items,
            subtotal: parseFloat(data.subtotal),
            discountAmount: parseFloat(data.discount),
            tax: parseFloat(data.tax),
            total: parseFloat(data.total),
            paymentMethod: data.method,
            amountPaid: parseFloat(data.paid)
        })
    })
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
            if (btn) {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }, 500);
    })
    .catch(err => {
        console.error(err);
        if (btn) {
            btn.innerText = originalText;
            btn.disabled = false;
        }
        alert("Gagal mencetak struk.");
    });
}
</script>
@endsection

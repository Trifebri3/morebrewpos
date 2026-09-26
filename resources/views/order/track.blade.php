<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan - {{ $transaksi->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); margin: 0; padding: 0; }
        
        .header { background: white; padding: 20px; text-align: center; border-bottom: 1px solid var(--border); }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; }
        .header p { margin: 4px 0 0 0; font-size: 13px; color: var(--text-muted); }

        .container { max-width: 600px; margin: 0 auto; padding: 16px; }

        .card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 16px; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-paid { background: #dcfce7; color: #15803d; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 600; }

        .item-list { margin-top: 20px; padding-top: 20px; border-top: 1px dashed var(--border); }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .item-qty { font-weight: 600; color: var(--primary); margin-right: 8px; }
        .item-price { font-weight: 600; }

        .summary { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .summary-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: var(--text-main); margin-top: 12px; }

        .btn-refresh { display: block; width: 100%; text-align: center; background: var(--text-main); color: white; border: none; border-radius: 8px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; margin-top: 24px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Status Pesanan</h1>
        <p>Invoice: <b>{{ $transaksi->invoice_number }}</b></p>
    </div>

    <div class="container">
        <div class="card">
            @if($transaksi->payment_method === 'pending')
                <div class="status-badge status-pending">Menunggu Pembayaran (Kasir)</div>
                <p style="font-size: 14px; color: var(--text-muted); margin-top: 0;">Silakan nikmati pesanan Anda dan lakukan pembayaran di kasir jika sudah selesai.</p>
            @else
                <div class="status-badge status-paid">Sudah Dibayar</div>
                <p style="font-size: 14px; color: var(--text-muted); margin-top: 0;">Terima kasih atas kunjungannya!</p>
            @endif

            <div class="info-row" style="margin-top: 16px;">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $transaksi->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pemesan / Meja</span>
                <span class="info-value">{{ $transaksi->customer_name }}</span>
            </div>

            <div class="item-list">
                @foreach(is_array($transaksi->items) ? $transaksi->items : json_decode($transaksi->items, true) as $item)
                <div class="item-row">
                    <div>
                        <span class="item-qty">{{ $item['qty'] }}x</span>
                        <span style="font-weight: 500;">{{ $item['name'] }}</span>
                    </div>
                    <span class="item-price">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="summary">
                <div class="info-row">
                    <span class="info-label">Subtotal</span>
                    <span class="info-value">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pajak (11%)</span>
                    <span class="info-value">Rp {{ number_format($transaksi->tax, 0, ',', '.') }}</span>
                </div>
                <div class="summary-total">
                    <span>Total Tagihan</span>
                    <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <a href="javascript:location.reload();" class="btn-refresh">Refresh Status</a>
    </div>

</body>
</html>

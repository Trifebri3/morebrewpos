<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #INV-{{ $data['invoiceNumber'] ?? 'XXXX' }}</title>
    <style>
        @page { margin: 0; size: 58mm auto; }
        body { font-family: 'Courier New', Courier, monospace; margin: 0; padding: 0; color: black; font-size: 12px; background: #f0f0f0; display: flex; justify-content: center; }
        .receipt { width: 58mm; max-width: 100%; margin: 20px auto; padding: 15px 10px; background: white; box-sizing: border-box; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        @media print {
            body { background: white; display: block; }
            .receipt { margin: 0; padding: 10px; box-shadow: none; }
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px dashed black; padding-bottom: 10px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; }
        .text-sm { font-size: 10px; line-height: 1.3; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="center border-bottom">
            <h2 style="margin:0 0 5px 0; font-size:18px;">MORE BREW</h2>
            <div class="text-sm">Kopi & Teman Nongkrong</div>
            <div class="text-sm" style="margin-top: 5px;">
                Jl. Sasmitatmaja No.6, Paledang<br>
                Kec. Lengkong, Kota Bandung<br>
                Jawa Barat 40261
            </div>
            
            <div style="margin-top:10px; font-weight:600;">INV-{{ $data['invoiceNumber'] ?? 'XXXX' }}</div>
            <div class="text-sm">{{ $data['date'] ?? \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="border-bottom text-sm">
            <div>Pelanggan: <span class="bold">{{ $data['customerName'] ?? '-' }}</span></div>
            <div>Tipe: <span class="bold">{{ ($data['orderType'] ?? '') === 'dine_in' ? 'Dine In' : 'Take Away' }}</span></div>
        </div>
        
        <div class="border-bottom">
            @if(!empty($data['cart']) && is_array($data['cart']))
                @foreach($data['cart'] as $item)
                    <div style="margin-top: 5px;">{{ $item['name'] ?? '' }}</div>
                    <div class="row text-sm">
                        <span>{{ $item['qty'] ?? 1 }} x Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format(($item['qty'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="border-bottom">
            <div class="row"><span>Subtotal:</span> <span>Rp {{ number_format($data['subtotal'] ?? 0, 0, ',', '.') }}</span></div>
            @if(($data['discountAmount'] ?? 0) > 0)
                <div class="row"><span>Diskon:</span> <span>-Rp {{ number_format($data['discountAmount'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
            @if(($data['tax'] ?? 0) > 0)
                <div class="row"><span>Pajak 11%:</span> <span>Rp {{ number_format($data['tax'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
            <div class="row bold" style="margin-top: 5px; font-size: 14px;"><span>Total:</span> <span>Rp {{ number_format($data['total'] ?? 0, 0, ',', '.') }}</span></div>
        </div>
        
        <div class="border-bottom">
            <div class="row"><span>Metode:</span> <span class="bold" style="text-transform: uppercase;">{{ $data['paymentMethod'] ?? 'CASH' }}</span></div>
            @if(($data['paymentMethod'] ?? 'cash') === 'cash')
                <div class="row"><span>Bayar:</span> <span>Rp {{ number_format($data['amountPaid'] ?? 0, 0, ',', '.') }}</span></div>
                <div class="row"><span>Kembali:</span> <span>Rp {{ number_format(($data['amountPaid'] ?? 0) - ($data['total'] ?? 0), 0, ',', '.') }}</span></div>
            @endif
        </div>
        
        <div class="center" style="margin-top: 15px;">
            <div class="bold">TERIMA KASIH</div>
            <div class="text-sm" style="margin-top: 5px;">Silakan datang kembali!</div>
            
            <div style="margin-top: 15px; border-top: 1px solid black; padding-top: 10px;">
                <div class="bold">Wi-Fi Area</div>
                <div class="text-sm">SSID: MORE BREW</div>
                <div class="text-sm">Pass: MORE BREW</div>
            </div>
        </div>
    </div>
</body>
</html>

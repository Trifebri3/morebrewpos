<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #INV-{{ $data['invoiceNumber'] ?? 'XXXX' }}</title>
    <style>
        @page { margin: 0; size: 58mm auto; }
        body { font-family: 'Courier New', Courier, monospace; margin: 0; padding: 0; color: black; font-size: 10px; background: #f0f0f0; display: flex; justify-content: center; }
        .receipt { width: 58mm; max-width: 100%; margin: 10px auto; padding: 8px 6px; background: white; box-sizing: border-box; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        @media print {
            body { background: white; display: block; }
            .receipt { margin: 0; padding: 6px 4px 30px 4px; box-shadow: none; width: 100%; }
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px dashed black; padding-bottom: 4px; margin-bottom: 4px; }
        .row { display: flex; justify-content: space-between; }
        .text-sm { font-size: 8.5px; line-height: 1.2; }
    </style>
</head>
<body onload="autoPrint()">
    <script>
        function autoPrint() {
            var isAndroid = /android/i.test(navigator.userAgent);
            if (isAndroid) {
                var htmlStr = document.documentElement.outerHTML;
                htmlStr = htmlStr.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                var b64 = btoa(unescape(encodeURIComponent(htmlStr)));
                var intentUrl = "intent:data:text/html;base64," + b64 + "#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;action=ru.a402d.rawbtprinter.PARSE;end;";
                window.location.href = intentUrl;
                setTimeout(function() {
                    window.print();
                }, 1500);
            } else {
                window.print();
            }
        }
    </script>
    <div class="receipt">
        @php
            $kData = \App\Models\Kedai::first();
            $rawAddr = $kData && $kData->address ? $kData->address : 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung';
            if (str_contains($rawAddr, 'Sasmitatmaja') || empty($rawAddr)) {
                $line1 = 'Jl. Sasmitatmaja No.6, Paledang';
                $line2 = 'Kec. Lengkong, Kota Bandung';
            } elseif (str_contains($rawAddr, "\n")) {
                $lines = array_filter(array_map('trim', explode("\n", $rawAddr)));
                $line1 = array_shift($lines);
                $line2 = implode(', ', $lines);
            } else {
                $parts = explode(',', $rawAddr);
                if (count($parts) >= 3) {
                    $mid = ceil(count($parts) / 2);
                    $line1 = trim(implode(',', array_slice($parts, 0, $mid)));
                    $line2 = trim(implode(',', array_slice($parts, $mid)));
                } else {
                    $line1 = $rawAddr;
                    $line2 = '';
                }
            }
        @endphp

        <!-- Section 1: Logo, Slogan, Alamat 2 Baris, Invoice Header -->
        <div class="center border-bottom">
            <div style="margin-bottom: 3px;"><img src="{{ asset('logo.png') }}" alt="Logo" style="max-height: 32px; object-fit: contain;"></div>
            <div class="text-sm">something, between home and<br>everywhare</div>
            <div class="text-sm" style="margin-top: 3px;">
                {{ $line1 }}<br>
                {{ $line2 }}
            </div>
            
            <div style="margin-top: 6px; font-weight: bold; font-size: 12px;">INV-{{ preg_replace('/^INV-?/i', '', (string)($data['invoiceNumber'] ?? 'XXXX')) }}</div>
            <div class="text-sm" style="margin-top: 1px;">{{ $data['date'] ?? \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <!-- Section 2: Pelanggan & Tipe Order (Satu Baris Hemat Ruang) -->
        @php
            $custName = $data['customerName'] ?? '-';
            $custPhone = !empty($data['customerPhone']) ? ' ('.$data['customerPhone'].')' : '';
            if (!empty($custPhone) && str_contains($custName, $data['customerPhone'])) {
                $custPhone = '';
            }
        @endphp
        <div class="border-bottom text-sm">
            <div class="row">
                <span>Plg: <span class="bold">{{ $custName }}{{ $custPhone }}</span></span>
                <span class="bold">{{ ($data['orderType'] ?? '') === 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
            </div>
        </div>
        
        <!-- Section 3: Daftar Item (Format Ringkas Hemat Ruang) -->
        <div class="border-bottom">
            @if(!empty($data['cart']) && is_array($data['cart']))
                @foreach($data['cart'] as $item)
                    <div style="margin-top: 2px; font-weight: bold; font-size: 9.5px;">{{ strtoupper($item['name'] ?? 'ITEM') }}</div>
                    <div class="row text-sm">
                        <span>{{ $item['qty'] ?? 1 }} x Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format(($item['qty'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @if(!empty($item['notes']))
                        <div style="font-size: 7.5px; font-style: italic; color: #555;">* {{ $item['notes'] }}</div>
                    @endif
                @endforeach
            @endif
        </div>
        
        <!-- Section 4: Subtotal, Diskon, Pajak & Total -->
        <div class="border-bottom text-sm">
            <div class="row"><span>Subtotal:</span> <span>Rp {{ number_format($data['subtotal'] ?? 0, 0, ',', '.') }}</span></div>
            @if(($data['discountAmount'] ?? 0) > 0)
                <div class="row" style="margin-top: 1px;"><span>Diskon:</span> <span>-Rp {{ number_format($data['discountAmount'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
            @if(($data['tax'] ?? 0) > 0)
                <div class="row" style="margin-top: 1px;"><span>{{ !empty($data['taxName']) ? $data['taxName'] : ($kData && $kData->tax_name ? $kData->tax_name : 'Pajak') }} ({{ $kData && $kData->tax_percentage ? (int)$kData->tax_percentage : 11 }}%):</span> <span>Rp {{ number_format($data['tax'] ?? 0, 0, ',', '.') }}</span></div>
            @endif
            <div class="row bold" style="margin-top: 4px; font-size: 12px;"><span>TOTAL:</span> <span>Rp {{ number_format($data['total'] ?? 0, 0, ',', '.') }}</span></div>
        </div>
        
        <!-- Section 5: Metode Pembayaran -->
        <div class="border-bottom text-sm">
            <div class="row"><span>Metode:</span> <span class="bold" style="text-transform: uppercase;">{{ $data['paymentMethod'] ?? 'CASH' }}</span></div>
            @if(strtolower($data['paymentMethod'] ?? 'cash') === 'cash' || strtolower($data['paymentMethod'] ?? 'cash') === 'tunai')
                <div class="row" style="margin-top: 1px;"><span>Bayar:</span> <span>Rp {{ number_format($data['amountPaid'] ?? 0, 0, ',', '.') }}</span></div>
                <div class="row" style="margin-top: 1px;"><span>Kembali:</span> <span>Rp {{ number_format(($data['amountPaid'] ?? 0) - ($data['total'] ?? 0), 0, ',', '.') }}</span></div>
            @endif
        </div>
        
        <!-- Section 6: Footer Terima Kasih, Wi-Fi & Instagram Berdampingan -->
        <div class="center" style="margin-top: 8px;">
            <div class="bold" style="font-size: 10px;">TERIMA KASIH</div>
            <div class="text-sm" style="margin-top: 1px;">Silakan datang kembali!</div>
            
            <div style="margin-top: 6px; border-top: 1px solid black; padding-top: 5px; display: flex; justify-content: space-between; align-items: flex-start; text-align: center;">
                <!-- Kiri: Wi-Fi Area Lebih Kecil -->
                <div style="flex: 1; padding-right: 4px;">
                    <div style="font-weight: bold; font-size: 8px; margin-bottom: 1px;">Wi-Fi Area</div>
                    <div style="font-size: 7px;">{{ $kData && $kData->wifi_ssid ? $kData->wifi_ssid : 'moreandmore' }}</div>
                    <div style="font-size: 6.5px; color: #333;">pass: {{ $kData && $kData->wifi_password ? $kData->wifi_password : 'bolehlihatsenyumnya?' }}</div>
                </div>
                
                <!-- Divider Vertikal Halus -->
                <div style="width: 1px; background: #000; align-self: stretch; min-height: 24px; margin: 0 4px;"></div>
                
                <!-- Kanan: Instagram Lebih Kecil -->
                <div style="flex: 1; padding-left: 4px;">
                    <div style="font-weight: bold; font-size: 8px; margin-bottom: 1px;">Instagram</div>
                    <div style="font-size: 7px;">{{ '@' . ltrim(($kData && $kData->instagram ? $kData->instagram : 'morebrewcoffee'), '@') }}</div>
                </div>
            </div>
        </div>
        <!-- Extra Bottom Feed Spacing (Thermal cutter / tear margin) -->
        <div style="height: 25px;"></div>
    </div>
</body>
</html>

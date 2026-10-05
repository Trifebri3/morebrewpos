class ThermalPrinter {
    constructor() {
        this.device = null;           // BLE device
        this.characteristic = null;   // BLE characteristic
        this.port = null;             // Serial port
        this.writer = null;           // Serial writer
        this.mode = null;             // 'serial' or 'bluetooth'
    }

    async connectSerial() {
        try {
            if (!navigator.serial) throw new Error("Web Serial API tidak didukung di browser ini.");
            console.log("Mencoba Web Serial API...");

            this.port = await navigator.serial.requestPort();

            if (!this.port.readable && !this.port.writable) {
                await this.port.open({ baudRate: 9600 });
            }
            if (!this.writer) {
                this.writer = this.port.writable.getWriter();
            }

            this.mode = 'serial';
            console.log("Web Serial terkoneksi!");
            return true;
        } catch (error) {
            console.error(error);
            alert("Gagal koneksi Serial: " + error.message + "\n\nPastikan port COM tidak sedang dipakai aplikasi lain (seperti driver printer Windows).");
            return false;
        }
    }

    async connectBluetooth() {
        try {
            if (!navigator.bluetooth) throw new Error("Web Bluetooth API tidak didukung browser ini.");
            console.log("Meminta koneksi Bluetooth BLE...");

            this.device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455'
                ]
            });

            await new Promise(resolve => setTimeout(resolve, 500));
            let server;
            try {
                server = await this.device.gatt.connect();
            } catch (err) {
                console.warn("Koneksi GATT pertama gagal, mencoba ulang...", err);
                await new Promise(resolve => setTimeout(resolve, 1000));
                server = await this.device.gatt.connect();
            }

            const services = await server.getPrimaryServices();
            for (const service of services) {
                const characteristics = await service.getCharacteristics();
                for (const char of characteristics) {
                    if (char.properties.write || char.properties.writeWithoutResponse) {
                        this.characteristic = char;
                        this.mode = 'bluetooth';
                        console.log("Ditemukan characteristic untuk menulis:", char.uuid);
                        return true;
                    }
                }
            }
            throw new Error("Tidak menemukan akses tulis (GATT) ke printer ini.");
        } catch (error) {
            console.error(error);
            if (error.message && error.message.includes("GATT Server is disconnected")) {
                alert("Gagal koneksi Bluetooth: Printer RPP02N (Bluetooth Classic) tidak mendukung koneksi Web Bluetooth di PC.\n\nSOLUSI UNTUK PC:\nSilakan gunakan tombol 'Serial COM (PC)' untuk cetak direct, atau gunakan mode PDF bawaan Windows untuk hasil cetak ber-LOGO.");
            } else {
                alert("Gagal koneksi Bluetooth: " + error.message);
            }
            return false;
        }
    }

    async autoConnectSerial() {
        try {
            if (navigator.serial) {
                const ports = await navigator.serial.getPorts(); // Mengambil port yang sudah pernah di-approve (izin tidak hilang saat refresh)
                if (ports.length > 0) {
                    console.log("Auto-connecting ke port Serial yang sudah disimpan...");
                    this.port = ports[0];
                    if (!this.port.readable && !this.port.writable) {
                        await this.port.open({ baudRate: 9600 });
                    }
                    if (!this.writer) {
                        this.writer = this.port.writable.getWriter();
                    }
                    this.mode = 'serial';
                    console.log("Auto-Connect Serial Berhasil!");

                    // Update tampilan tombol di header (jika ada)
                    const serialStatusBtn = document.getElementById('serial-status');
                    if (serialStatusBtn) {
                        serialStatusBtn.innerHTML = '✅ Serial Terhubung';
                        serialStatusBtn.style.color = 'green';
                    }
                    return true;
                }
            }
        } catch (e) {
            console.warn("Auto-connect serial gagal:", e);
        }
        return false;
    }

    isConnected() {
        return (this.mode === 'serial' && this.writer) ||
            (this.mode === 'bluetooth' && this.device && this.device.gatt.connected && this.characteristic);
    }

    async printReceipt(data) {
        if (!this.isConnected()) {
            throw new Error("Printer belum terhubung.");
        }

        const encoder = new TextEncoder();
        let receiptText = "";

        function formatRp(num) {
            return 'Rp ' + parseInt(num).toLocaleString('id-ID');
        }

        const date = new Date();
        const formattedDate = date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear() + ' ' + date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

        receiptText += "\x1B\x40"; // Init printer
        receiptText += "\x1B\x61\x01"; // Align center
        receiptText += "something, between home and\neverywhare\n";
        receiptText += "Jl. Sasmitatmaja No.6, Paledang\nKec. Lengkong, Kota Bandung\n\n";

        receiptText += "\x1B\x45\x01"; // Bold ON
        receiptText += "INV-" + data.invoiceNumber + "\n";
        receiptText += "\x1B\x45\x00"; // Bold OFF
        receiptText += formattedDate + "\n";

        receiptText += "--------------------------------\n";

        receiptText += "\x1B\x61\x00"; // Align left
        receiptText += "Pelanggan: " + (data.customerName || '-') + "\n";
        receiptText += "Tipe: " + (data.orderType === 'dine_in' ? 'Dine In' : 'Take Away') + "\n";

        receiptText += "--------------------------------\n";

        for (const item of data.cart) {
            let itemName = item.name || 'Item';
            if (itemName.length > 32) itemName = itemName.substring(0, 32);
            receiptText += itemName + "\n";

            let qty = (item.qty || 1).toString();
            let pricePerItem = formatRp(item.price || 0);
            let leftStr = qty + " x " + pricePerItem;
            let totalItem = formatRp((item.price || 0) * (item.qty || 1));

            let spaces = 32 - (leftStr.length + totalItem.length);
            if (spaces < 1) spaces = 1;
            receiptText += leftStr + ' '.repeat(spaces) + totalItem + "\n";
        }

        receiptText += "--------------------------------\n";

        let subtotalStr = formatRp(data.subtotal || 0);
        receiptText += "Subtotal:" + ' '.repeat(Math.max(1, 32 - 9 - subtotalStr.length)) + subtotalStr + "\n";

        if ((data.discountAmount || 0) > 0) {
            let discStr = "-" + formatRp(data.discountAmount);
            receiptText += "Diskon:" + ' '.repeat(Math.max(1, 32 - 7 - discStr.length)) + discStr + "\n";
        }

        if ((data.tax || 0) > 0) {
            let taxStr = formatRp(data.tax);
            receiptText += "Pajak 11%:" + ' '.repeat(Math.max(1, 32 - 10 - taxStr.length)) + taxStr + "\n";
        }

        receiptText += "\x1B\x45\x01"; // Bold ON
        let totalStr = formatRp(data.total || 0);
        receiptText += "Total:" + ' '.repeat(Math.max(1, 32 - 6 - totalStr.length)) + totalStr + "\n";
        receiptText += "\x1B\x45\x00"; // Bold OFF

        receiptText += "--------------------------------\n";

        let methodStr = (data.paymentMethod || data.method || 'CASH').toUpperCase();
        receiptText += "Metode:" + ' '.repeat(Math.max(1, 32 - 7 - methodStr.length)) + methodStr + "\n";

        if (methodStr === 'CASH') {
            let paidStr = formatRp(data.amountPaid || data.paid || data.total || 0);
            receiptText += "Bayar:" + ' '.repeat(Math.max(1, 32 - 6 - paidStr.length)) + paidStr + "\n";

            let change = (data.amountPaid || data.paid || data.total || 0) - (data.total || 0);
            let changeStr = formatRp(change);
            receiptText += "Kembali:" + ' '.repeat(Math.max(1, 32 - 8 - changeStr.length)) + changeStr + "\n";
        }

        receiptText += "--------------------------------\n";

        receiptText += "\x1B\x61\x01"; // Align center
        receiptText += "\x1B\x45\x01"; // Bold ON
        receiptText += "TERIMA KASIH\n";
        receiptText += "\x1B\x45\x00"; // Bold OFF
        receiptText += "Silakan datang kembali!\n\n";

        if (window.kedaiInfo) {
            let wifiSsid = window.kedaiInfo.wifiSsid || 'moreandmore';
            let wifiPass = window.kedaiInfo.wifiPassword || 'bolehlihatsenyumnya?';
            let igHandle = window.kedaiInfo.instagram || 'morebrewcoffee';
            if (igHandle.startsWith('@')) igHandle = igHandle.substring(1);

            receiptText += "--------------------------------\n";
            receiptText += "\x1B\x4D\x01"; // Font B (smaller font, 42 chars/line)
            receiptText += "\x1B\x45\x01"; // Bold ON
            receiptText += "   Wi-Fi Area          Instagram   \n";
            receiptText += "\x1B\x45\x00"; // Bold OFF

            let colLeft1 = wifiSsid;
            let colRight1 = "@" + igHandle;
            let pad1 = Math.max(1, 21 - colLeft1.length);
            receiptText += colLeft1 + " ".repeat(pad1) + colRight1 + "\n";

            let colLeft2 = "pw: " + wifiPass;
            receiptText += colLeft2 + "\n";
            receiptText += "\x1B\x4D\x00"; // Font A (normal font)
        }

        // Margin bawah ekstra (4 baris feed) agar tidak terpotong pisau cutter
        receiptText += "\n\n\n\n";

        // Feed & Cut
        receiptText += "\x1D\x56\x41\x10";

        const uint8array = encoder.encode(receiptText);

        if (this.mode === 'serial') {
            await this.writer.write(uint8array);
            console.log("Struk berhasil dicetak via Serial COM Port.");
        } else if (this.mode === 'bluetooth') {
            // Split data into 20 byte chunks (BLE limit)
            const chunkSize = 20;
            for (let i = 0; i < uint8array.length; i += chunkSize) {
                const chunk = uint8array.slice(i, i + chunkSize);
                await this.characteristic.writeValue(chunk);
                // small delay to prevent buffer overflow
                await new Promise(r => setTimeout(r, 20));
            }
            console.log("Struk berhasil dicetak via BLE GATT.");
        }
    }
}

window.thermalPrinter = new ThermalPrinter();

// Auto-connect saat web dimuat (Refresh / Ganti Halaman)
window.addEventListener('load', () => {
    window.thermalPrinter.autoConnectSerial();
});

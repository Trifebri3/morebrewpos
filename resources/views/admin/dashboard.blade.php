@extends('admin.layouts.app', $data)

@section('content')
<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Dashboard Admin Kedai</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Ringkasan performa dan operasional kedai Anda hari ini.</p>
</div>

<div style="padding: 24px 40px 40px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Card 1 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Omzet Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">Rp 1.250.000</div>
        </div>
        <!-- Card 2 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Transaksi Selesai</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">42</div>
        </div>
        <!-- Card 3 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Kasir Bertugas</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">2 Orang</div>
        </div>
        <!-- Card 4 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Stok Menipis</div>
            <div style="font-size: 28px; font-weight: 700; color: #ef4444;">3 Item</div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">Transaksi Terkini</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 13px;">
                    <th style="text-align: left; padding: 12px 0;">ID Transaksi</th>
                    <th style="text-align: left; padding: 12px 0;">Waktu</th>
                    <th style="text-align: left; padding: 12px 0;">Nominal</th>
                    <th style="text-align: left; padding: 12px 0;">Status</th>
                </tr>
                <tr>
                    <td style="padding: 12px 0; font-size: 14px;">#INV-2093</td>
                    <td style="padding: 12px 0; font-size: 14px; color: var(--text-muted);">13:24</td>
                    <td style="padding: 12px 0; font-size: 14px; font-weight: 500;">Rp 45.000</td>
                    <td style="padding: 12px 0; font-size: 14px;"><span style="color: #10b981;">Berhasil</span></td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; font-size: 14px;">#INV-2092</td>
                    <td style="padding: 12px 0; font-size: 14px; color: var(--text-muted);">13:10</td>
                    <td style="padding: 12px 0; font-size: 14px; font-weight: 500;">Rp 120.000</td>
                    <td style="padding: 12px 0; font-size: 14px;"><span style="color: #10b981;">Berhasil</span></td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; font-size: 14px;">#INV-2091</td>
                    <td style="padding: 12px 0; font-size: 14px; color: var(--text-muted);">12:55</td>
                    <td style="padding: 12px 0; font-size: 14px; font-weight: 500;">Rp 25.000</td>
                    <td style="padding: 12px 0; font-size: 14px;"><span style="color: #10b981;">Berhasil</span></td>
                </tr>
            </table>
        </div>
        
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">Aksi Cepat</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('admin.operasional.produk.index') }}" style="display: block; padding: 12px 16px; background: var(--bg-color); border-radius: 6px; text-decoration: none; color: var(--text-main); font-size: 14px; font-weight: 500;">Kelola Produk &rarr;</a>
                <a href="{{ route('admin.operasional.stok') }}" style="display: block; padding: 12px 16px; background: var(--bg-color); border-radius: 6px; text-decoration: none; color: var(--text-main); font-size: 14px; font-weight: 500;">Update Stok Harian &rarr;</a>
                <a href="{{ route('admin.operasional.pengeluaran.index') }}" style="display: block; padding: 12px 16px; background: var(--bg-color); border-radius: 6px; text-decoration: none; color: var(--text-main); font-size: 14px; font-weight: 500;">Catat Belanja Harian &rarr;</a>
                <a href="/kasir/dashboard" style="display: block; padding: 12px 16px; background: var(--text-main); color: white; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; text-align: center;">Buka Aplikasi Kasir (POS)</a>
            </div>
        </div>
    </div>
</div>
@endsection

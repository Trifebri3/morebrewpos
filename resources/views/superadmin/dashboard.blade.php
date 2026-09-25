@extends('superadmin.layouts.app', $data)

@section('content')
<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Dashboard Superadmin</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Selamat datang kembali, pantau seluruh aktivitas kedai dari sini.</p>
</div>

<div style="padding: 24px 40px 40px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Card 1 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Total Kedai Aktif</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">12</div>
        </div>
        <!-- Card 2 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Total Omzet Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">Rp 4.5M</div>
        </div>
        <!-- Card 3 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Total Transaksi</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--text-main);">1,284</div>
        </div>
        <!-- Card 4 -->
        <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">Peringatan Stok</div>
            <div style="font-size: 28px; font-weight: 700; color: #ef4444;">5 Item</div>
        </div>
    </div>
    
    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px;">
        <h3 style="font-size: 16px; margin-bottom: 16px;">Aktivitas Terbaru</h3>
        <ul style="list-style: none; color: var(--text-muted); font-size: 14px;">
            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">[Admin Kedai A] Melakukan tutup kasir dengan total Rp 1.500.000</li>
            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">[Kasir Kedai C] Transaksi #INV-0092 berhasil</li>
            <li style="padding: 12px 0;">[Sistem] Menambahkan menu baru "Kopi Susu Gula Aren" ke master produk</li>
        </ul>
    </div>
</div>
@endsection

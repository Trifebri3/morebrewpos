@extends('superadmin.layouts.app', $data)

@section('content')
<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Pengaturan Role & Hak Akses</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Informasi mendetail mengenai batasan dan hak akses setiap level pengguna di sistem.</p>
</div>

<div style="padding: 24px 40px 40px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
    
    <!-- Superadmin Card -->
    <div style="background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 24px;">
        <div style="font-weight: 700; font-size: 18px; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 12px; height: 12px; background: var(--text-main); border-radius: 50%;"></span>
            Superadmin
        </div>
        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Akses Penuh / Pengelola Pusat</div>
        
        <ul style="list-style: none; font-size: 14px; display: flex; flex-direction: column; gap: 12px;">
            <li style="display: flex; gap: 8px;"><span style="color:#10b981;">✓</span> Manajemen Data Kedai</li>
            <li style="display: flex; gap: 8px;"><span style="color:#10b981;">✓</span> Pembuatan Akun Admin & Kasir</li>
            <li style="display: flex; gap: 8px;"><span style="color:#10b981;">✓</span> Laporan & Omzet Seluruh Kedai</li>
            <li style="display: flex; gap: 8px;"><span style="color:#10b981;">✓</span> Monitoring Audit Log</li>
            <li style="display: flex; gap: 8px;"><span style="color:#10b981;">✓</span> Pengaturan Pajak & Global</li>
        </ul>
    </div>

    <!-- Admin Card -->
    <div style="background: white; border: 1px solid #dbeafe; border-radius: 8px; padding: 24px; box-shadow: 0 4px 12px rgba(30, 64, 175, 0.05);">
        <div style="font-weight: 700; font-size: 18px; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; color: #1e40af;">
            <span style="width: 12px; height: 12px; background: #3b82f6; border-radius: 50%;"></span>
            Admin Kedai
        </div>
        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Pengelola Satu Kedai</div>
        
        <ul style="list-style: none; font-size: 14px; display: flex; flex-direction: column; gap: 12px;">
            <li style="display: flex; gap: 8px;"><span style="color:#3b82f6;">✓</span> Manajemen Produk Lokal</li>
            <li style="display: flex; gap: 8px;"><span style="color:#3b82f6;">✓</span> Manajemen Stok Kedai</li>
            <li style="display: flex; gap: 8px;"><span style="color:#3b82f6;">✓</span> Pengaturan Shift & Absensi</li>
            <li style="display: flex; gap: 8px;"><span style="color:#3b82f6;">✓</span> Laporan Transaksi Kedai</li>
            <li style="display: flex; gap: 8px; color: var(--text-muted);">✗ Akses Laporan Kedai Lain</li>
        </ul>
    </div>

    <!-- Kasir Card -->
    <div style="background: white; border: 1px solid #fef3c7; border-radius: 8px; padding: 24px; box-shadow: 0 4px 12px rgba(180, 83, 9, 0.05);">
        <div style="font-weight: 700; font-size: 18px; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; color: #b45309;">
            <span style="width: 12px; height: 12px; background: #f59e0b; border-radius: 50%;"></span>
            Kasir
        </div>
        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Operator Transaksi</div>
        
        <ul style="list-style: none; font-size: 14px; display: flex; flex-direction: column; gap: 12px;">
            <li style="display: flex; gap: 8px;"><span style="color:#f59e0b;">✓</span> Akses Sistem POS</li>
            <li style="display: flex; gap: 8px;"><span style="color:#f59e0b;">✓</span> Pembuatan Invoice / Transaksi</li>
            <li style="display: flex; gap: 8px;"><span style="color:#f59e0b;">✓</span> Kelola Meja & QR Order</li>
            <li style="display: flex; gap: 8px; color: var(--text-muted);">✗ Manajemen Produk</li>
            <li style="display: flex; gap: 8px; color: var(--text-muted);">✗ Ubah Stok Manual</li>
        </ul>
    </div>

</div>
@endsection

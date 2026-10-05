@extends('superadmin.layouts.app', $data)

@section('content')
<div class="page-header" style="padding: 32px 40px 0;">
    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">Dashboard Superadmin</h1>
    <p style="color: #64748b; font-size: 13.5px; margin-top: 6px;">Ringkasan performa seluruh kedai MoreBrew secara langsung (real-time).</p>
</div>

<div style="padding: 24px 40px 40px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <!-- Card 1 -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12.5px; color: #64748b; margin-bottom: 8px; font-weight: 700; text-transform: uppercase;">Total Kedai Aktif</div>
            <div style="font-size: 28px; font-weight: 800; color: #0f172a;">{{ $data['totalKedai'] ?? 1 }}</div>
        </div>
        <!-- Card 2 -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12.5px; color: #64748b; margin-bottom: 8px; font-weight: 700; text-transform: uppercase;">Total Omzet Hari Ini</div>
            <div style="font-size: 28px; font-weight: 800; color: #0f172a; font-family: monospace;">{{ $data['totalOmzetHariIni'] ?? 'Rp 0' }}</div>
        </div>
        <!-- Card 3 -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12.5px; color: #64748b; margin-bottom: 8px; font-weight: 700; text-transform: uppercase;">Total Transaksi</div>
            <div style="font-size: 28px; font-weight: 800; color: #0f172a;">{{ number_format($data['totalTransaksi'] ?? 0) }}</div>
        </div>
        <!-- Card 4 -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12.5px; color: #64748b; margin-bottom: 8px; font-weight: 700; text-transform: uppercase;">Peringatan Stok</div>
            <div style="font-size: 28px; font-weight: 800; color: {{ ($data['stokMenipis'] ?? 0) > 0 ? '#ef4444' : '#10b981' }};">
                {{ ($data['stokMenipis'] ?? 0) > 0 ? $data['stokMenipis'] . ' Item' : 'Aman' }}
            </div>
        </div>
    </div>
    
    <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Aktivitas Penjualan Terbaru</h3>
        <ul style="list-style: none; color: #334155; font-size: 13.5px;">
            @forelse($data['latestActivities'] ?? [] as $act)
            <li style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                <span>{{ $act }}</span>
            </li>
            @empty
            <li style="padding: 16px 0; color: #94a3b8; text-align: center;">Belum ada aktivitas transaksi hari ini.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

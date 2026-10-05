@extends('admin.layouts.app', $data)

@section('content')
<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">Dashboard Admin Kedai</h1>
        <p style="color: #64748b; font-size: 13.5px; margin-top: 6px;">Ringkasan performa penjualan dan operasional kedai hari ini secara langsung (real-time).</p>
    </div>
    
    <!-- Realtime Live Status & Refresh -->
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 9999px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px #d1fae5; animation: pulse 2s infinite;"></span>
            <span style="font-size: 12px; font-weight: 700; color: #065f46; letter-spacing: 0.3px;">LIVE REALTIME</span>
        </div>
        <div style="font-size: 12px; color: #64748b;">
            Update: <strong id="stat-last-updated" style="color: #0f172a;">{{ $data['lastUpdated'] ?? date('H:i:s') . ' WIB' }}</strong>
        </div>
        <button type="button" onclick="refreshDashboard(true)" id="btn-manual-refresh" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12.5px; font-weight: 600; color: #1e293b; cursor: pointer; transition: all 0.2s;">
            <svg id="refresh-icon" width="14" height="14" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            <span>Segarkan</span>
        </button>
    </div>
</div>

<div style="padding: 24px 40px 40px;">

    <!-- 4 Primary Realtime Metric Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px;">
        
        <!-- Card 1: Omzet Hari Ini -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); position: relative; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 12.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Omzet Hari Ini</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div id="stat-omzet" style="font-size: 26px; font-weight: 800; color: #0f172a; font-family: monospace; letter-spacing: -0.5px;">{{ $data['omzetHariIniFormatted'] ?? 'Rp 0' }}</div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Bulan Ini: <strong id="stat-omzet-bulan" style="color: #0f172a;">{{ $data['omzetBulanIniFormatted'] ?? 'Rp 0' }}</strong>
            </div>
        </div>

        <!-- Card 2: Transaksi Hari Ini -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 12.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Transaksi Berhasil</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
            </div>
            <div id="stat-transaksi" style="font-size: 26px; font-weight: 800; color: #0f172a;">{{ $data['transaksiHariIni'] ?? 0 }} <span style="font-size: 15px; font-weight: 600; color: #64748b;">Trx</span></div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Tunai: <span id="stat-tunai" style="color: #0f172a; font-weight: 600;">{{ $data['tunaiHariIniFormatted'] ?? 'Rp 0' }}</span> • Non-Tunai: <span id="stat-nontunai" style="color: #0f172a; font-weight: 600;">{{ $data['nonTunaiHariIniFormatted'] ?? 'Rp 0' }}</span>
            </div>
        </div>

        <!-- Card 3: Kasir Bertugas / Shift -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 12.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Kasir & Staf</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div id="stat-kasir" style="font-size: 24px; font-weight: 800; color: #0f172a;">{{ $data['kasirBertugas'] ?? '0 Shift Aktif' }}</div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Presensi Masuk: <strong id="stat-staf-masuk" style="color: #0f172a;">{{ $data['stafMasukCount'] ?? 0 }} Orang</strong>
            </div>
        </div>

        <!-- Card 4: Peringatan Stok -->
        <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 12.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Peringatan Stok</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" fill="none" stroke="#000000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div id="stat-stok" style="font-size: 22px; font-weight: 800; color: {{ ($data['stokMenipis'] ?? 0) > 0 ? '#ef4444' : '#10b981' }};">
                {{ ($data['stokMenipis'] ?? 0) > 0 ? $data['stokMenipis'] . ' Menu Menipis' : 'Stok Aman' }}
            </div>
            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                Batas ambang: <strong>&le; 5 Pcs</strong> • Total: {{ $data['totalProduk'] ?? 0 }} Menu
            </div>
        </div>
    </div>
    
    <!-- Content Grid: Transaksi Terkini vs Quick Actions & Top Products -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        
        <!-- Left Column: Transaksi Terkini Realtime -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a;">Transaksi Terkini</h3>
                        <p style="font-size: 12.5px; color: #64748b; margin-top: 2px;">Aliran transaksi penjualan terbaru yang masuk ke sistem kedai.</p>
                    </div>
                    <a href="{{ route('admin.penjualan.transaksi') }}" style="font-size: 12.5px; font-weight: 700; color: #0f172a; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 11.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                                <th style="text-align: left; padding: 10px 12px;">Invoice</th>
                                <th style="text-align: left; padding: 10px 12px;">Pelanggan</th>
                                <th style="text-align: left; padding: 10px 12px;">Waktu</th>
                                <th style="text-align: left; padding: 10px 12px;">Metode</th>
                                <th style="text-align: right; padding: 10px 12px;">Total</th>
                                <th style="text-align: center; padding: 10px 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-tbody">
                            @forelse($data['transaksiTerkini'] ?? [] as $trx)
                            <tr style="border-bottom: 1px solid #f8fafc; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 12px; font-family: monospace; font-weight: 700; color: #0f172a;">
                                    #{{ $trx['invoice_number'] }}
                                </td>
                                <td style="padding: 12px; color: #334155; font-weight: 500;">
                                    {{ $trx['customer_name'] }}
                                    <span style="font-size: 11px; color: #94a3b8; display: block;">{{ $trx['order_type'] }}</span>
                                </td>
                                <td style="padding: 12px; color: #64748b; font-size: 12px;">
                                    {{ $trx['waktu'] }}
                                </td>
                                <td style="padding: 12px;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #f1f5f9; color: #475569;">
                                        {{ $trx['payment_method'] }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: right; font-weight: 800; font-family: monospace; color: #0f172a;">
                                    {{ $trx['nominal_rp'] }}
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($trx['is_refunded'] ?? false)
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #ffe4e6; color: #e11d48;">Refund</span>
                                    @else
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #dcfce7; color: #15803d;">Berhasil</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 32px 16px; text-align: center; color: #94a3b8;">
                                    <div style="font-weight: 600; font-size: 14px; color: #64748b;">Belum ada transaksi hari ini</div>
                                    <div style="font-size: 12px; margin-top: 4px;">Transaksi dari kasir akan muncul secara otomatis di sini secara real-time.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Products Sold Today -->
            @if(!empty($data['topProduk']) && count($data['topProduk']) > 0)
            <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 14px;">Menu Paling Laris Hari Ini</h3>
                <div id="top-products-container" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($data['topProduk'] as $tp)
                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 12.5px;">
                        <span style="font-weight: 700; color: #0f172a;">{{ $tp['name'] }}</span>
                        <span style="padding: 2px 7px; background: #0f172a; color: white; border-radius: 6px; font-size: 11px; font-weight: 800; font-family: monospace;">{{ $tp['qty'] }} terjual</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Column: Quick Actions & Status Operasional -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Quick Actions -->
            <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 14px;">Aksi Cepat & Navigasi</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="/kasir/dashboard" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 13px 16px; background: #0f172a; color: white; border-radius: 10px; text-decoration: none; font-size: 13.5px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.12); transition: all 0.2s;" onmouseover="this.style.background='#000000'" onmouseout="this.style.background='#0f172a'">
                        <svg width="16" height="16" fill="none" stroke="#ffffff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Buka Aplikasi Kasir (POS) &rarr;
                    </a>
                    <a href="{{ route('admin.operasional.produk.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 600; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span>Kelola Menu & Produk</span>
                        <span style="color: #94a3b8;">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.operasional.stok') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 600; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span>Update Stok Harian</span>
                        <span style="color: #94a3b8;">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.staff.absensi') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 600; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span>Rekapan Absensi Pegawai</span>
                        <span style="color: #94a3b8;">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.operasional.pengeluaran.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 600; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span>Catat Belanja Kedai</span>
                        <span style="color: #94a3b8;">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.kedai.pengaturan') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 600; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                        <span>Pengaturan Kedai & Pajak</span>
                        <span style="color: #94a3b8;">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Ringkasan Operasional Hari Ini -->
            <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 14px;">Operasional Kedai</h3>
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Belanja / Pengeluaran Hari Ini</span>
                        <strong id="stat-pengeluaran" style="color: #0f172a; font-family: monospace;">{{ $data['pengeluaranHariIniFormatted'] ?? 'Rp 0' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Tanggal Hari Ini</span>
                        <strong style="color: #0f172a;">{{ $data['todayDate'] ?? date('d M Y') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Status Sistem POS</span>
                        <span style="display: inline-flex; align-items: center; gap: 6px; color: #15803d; font-weight: 700; font-size: 12px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                            Normal / Terhubung
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Live Real-Time Dashboard Polling (Every 10 seconds)
    let isFetching = false;

    async function refreshDashboard(isManual = false) {
        if (isFetching) return;
        isFetching = true;

        const refreshIcon = document.getElementById('refresh-icon');
        if (refreshIcon) {
            refreshIcon.style.transition = 'transform 0.5s ease';
            refreshIcon.style.transform = 'rotate(360deg)';
        }

        try {
            const response = await fetch("{{ route('admin.dashboard.realtime') }}");
            if (response.ok) {
                const data = await response.json();

                // 1. Update KPI numbers
                const elOmzet = document.getElementById('stat-omzet');
                if (elOmzet) elOmzet.innerText = data.omzetHariIniFormatted;

                const elOmzetBulan = document.getElementById('stat-omzet-bulan');
                if (elOmzetBulan) elOmzetBulan.innerText = data.omzetBulanIniFormatted;

                const elTransaksi = document.getElementById('stat-transaksi');
                if (elTransaksi) elTransaksi.innerHTML = `${data.transaksiHariIni} <span style="font-size: 15px; font-weight: 600; color: #64748b;">Trx</span>`;

                const elTunai = document.getElementById('stat-tunai');
                if (elTunai) elTunai.innerText = data.tunaiHariIniFormatted;

                const elNonTunai = document.getElementById('stat-nontunai');
                if (elNonTunai) elNonTunai.innerText = data.nonTunaiHariIniFormatted;

                const elKasir = document.getElementById('stat-kasir');
                if (elKasir) elKasir.innerText = data.kasirBertugas;

                const elStafMasuk = document.getElementById('stat-staf-masuk');
                if (elStafMasuk) elStafMasuk.innerText = `${data.stafMasukCount} Orang`;

                const elStok = document.getElementById('stat-stok');
                if (elStok) {
                    if (data.stokMenipis > 0) {
                        elStok.innerText = `${data.stokMenipis} Menu Menipis`;
                        elStok.style.color = '#ef4444';
                    } else {
                        elStok.innerText = 'Stok Aman';
                        elStok.style.color = '#10b981';
                    }
                }

                const elPengeluaran = document.getElementById('stat-pengeluaran');
                if (elPengeluaran) elPengeluaran.innerText = data.pengeluaranHariIniFormatted;

                const elLastUpdated = document.getElementById('stat-last-updated');
                if (elLastUpdated) elLastUpdated.innerText = data.lastUpdated;

                // 2. Update Transactions Table
                const tbody = document.getElementById('transactions-tbody');
                if (tbody && data.transaksiTerkini) {
                    if (data.transaksiTerkini.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" style="padding: 32px 16px; text-align: center; color: #94a3b8;">
                                    <div style="font-weight: 600; font-size: 14px; color: #64748b;">Belum ada transaksi hari ini</div>
                                    <div style="font-size: 12px; margin-top: 4px;">Transaksi dari kasir akan muncul secara otomatis di sini secara real-time.</div>
                                </td>
                            </tr>
                        `;
                    } else {
                        let html = '';
                        data.transaksiTerkini.forEach(trx => {
                            const badgeStatus = trx.is_refunded
                                ? `<span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #ffe4e6; color: #e11d48;">Refund</span>`
                                : `<span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #dcfce7; color: #15803d;">Berhasil</span>`;

                            html += `
                                <tr style="border-bottom: 1px solid #f8fafc; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 12px; font-family: monospace; font-weight: 700; color: #0f172a;">
                                        #${trx.invoice_number}
                                    </td>
                                    <td style="padding: 12px; color: #334155; font-weight: 500;">
                                        ${trx.customer_name}
                                        <span style="font-size: 11px; color: #94a3b8; display: block;">${trx.order_type}</span>
                                    </td>
                                    <td style="padding: 12px; color: #64748b; font-size: 12px;">
                                        ${trx.waktu}
                                    </td>
                                    <td style="padding: 12px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #f1f5f9; color: #475569;">
                                            ${trx.payment_method}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: right; font-weight: 800; font-family: monospace; color: #0f172a;">
                                        ${trx.nominal_rp}
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        ${badgeStatus}
                                    </td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    }
                }
            }
        } catch (e) {
            console.error("Realtime dashboard refresh error:", e);
        } finally {
            isFetching = false;
            setTimeout(() => {
                if (refreshIcon) {
                    refreshIcon.style.transform = 'rotate(0deg)';
                }
            }, 500);
        }
    }

    // Auto-refresh every 10 seconds
    setInterval(() => {
        refreshDashboard(false);
    }, 10000);
</script>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.15); }
    }
</style>
@endsection

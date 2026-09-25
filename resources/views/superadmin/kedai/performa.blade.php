@extends('superadmin.layouts.app', $data)

@section('content')
<style>
    .grid-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; padding: 0 40px 40px; }
    .performa-card { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 24px; transition: transform 0.2s; }
    .performa-card:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .performa-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .kedai-name { font-size: 18px; font-weight: 600; color: var(--text-main); }
    .kedai-status { font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    
    .metric-group { margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .metric { display: flex; flex-direction: column; }
    .metric-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; }
    .metric-value { font-size: 16px; font-weight: 600; color: var(--text-main); }
    .metric-omzet { font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 8px; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Performa Kedai</h1>
    <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Ringkasan performa penjualan dan statistik seluruh cabang kedai.</p>
</div>

<div class="grid-cards">
    @forelse($performaData as $data)
    <div class="performa-card">
        <div class="performa-header">
            <div>
                <div class="kedai-name">{{ $data['kedai']->name }}</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">{{ $data['kedai']->address ?? 'Tidak ada alamat' }}</div>
            </div>
            <div class="kedai-status {{ $data['kedai']->is_active ? 'status-active' : 'status-inactive' }}">
                {{ $data['kedai']->is_active ? 'Aktif' : 'Nonaktif' }}
            </div>
        </div>
        
        <div>
            <div class="metric-label">Total Omzet (Bulan ini)</div>
            <div class="metric-omzet">Rp {{ number_format($data['omzet'], 0, ',', '.') }}</div>
        </div>
        
        <div class="metric-group">
            <div class="metric">
                <div class="metric-label">Total Transaksi</div>
                <div class="metric-value">{{ $data['transactions'] }} trx</div>
            </div>
            <div class="metric">
                <div class="metric-label">Rating Rata-rata</div>
                <div class="metric-value">⭐ {{ $data['rating'] }} / 5.0</div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 40px; background: white; border: 1px solid var(--border-color); border-radius: 8px;">
        Belum ada data kedai untuk ditampilkan performanya. <br>
        <a href="{{ route('superadmin.kedai.index') }}" style="color: var(--text-main); text-decoration: underline; margin-top: 8px; display: inline-block;">Kelola Data Kedai</a>
    </div>
    @endforelse
</div>
@endsection

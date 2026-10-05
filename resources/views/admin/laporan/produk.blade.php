@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 2px 6px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 17px; font-weight: 700; color: var(--text-main); margin: 0; }

    .table-container { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 14px 20px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { font-size: 12px; font-weight: 600; color: var(--text-muted); background: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px; }
    td { font-size: 13.5px; color: var(--text-main); vertical-align: middle; }

    .btn-export { background: #166534; color: white; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: background 0.2s; }
    .btn-export:hover { background: #14532d; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-aktif { background: #dcfce7; color: #166534; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; }
</style>

<div style="padding: 24px 32px 40px;">
    <!-- Title & Export -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 6px 0;">Laporan Performa Produk & Menu</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">Analisis kuantitas terjual, sisa stok, dan total omzet per produk.</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan.produk.export') }}" class="btn-export">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel (.xlsx)
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #2563eb;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Item Menu</div>
            <div style="font-size: 26px; font-weight: 800; color: #1e293b;">{{ $totalProduk ?? 0 }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Produk</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #16a34a;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Kuantitas Terjual</div>
            <div style="font-size: 26px; font-weight: 800; color: #166534;">{{ number_format($totalTerjual ?? 0, 0, ',', '.') }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Cup / Pcs</span></div>
        </div>
        <div class="card" style="padding: 20px; margin-bottom: 0; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Omzet Produk</div>
            <div style="font-size: 26px; font-weight: 800; color: #b45309;">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Table of Products -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Produk & Penjualan</h2>
            <span style="font-size: 13px; color: var(--text-muted);">Urutan berdasarkan nama produk</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Menu / Produk</th>
                        <th>Kategori</th>
                        <th>Harga Satuan</th>
                        <th>Sisa Stok</th>
                        <th>Qty Terjual</th>
                        <th>Estimasi Omzet</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $p)
                    <tr>
                        <td style="font-family: monospace; font-size: 12px; font-weight: 600; color: #475569;">{{ $p->sku ?: '-' }}</td>
                        <td>
                            <strong style="color: #111827; font-size: 14px;">{{ $p->name }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; padding: 4px 8px; background: #f1f5f9; border-radius: 6px; font-weight: 600; color: #475569;">
                                {{ $p->category ?: 'Umum' }}
                            </span>
                        </td>
                        <td style="font-weight: 600;">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>
                            <span style="font-weight: 700; color: {{ $p->stock <= 10 ? '#dc2626' : '#111827' }};">
                                {{ $p->stock }}
                            </span>
                        </td>
                        <td>
                            <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                {{ $p->terjual ?? 0 }} terjual
                            </span>
                        </td>
                        <td style="font-weight: 700; color: #166534;">
                            Rp {{ number_format($p->omzet ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge {{ $p->is_active ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px; color: #64748b;">
                            Belum ada data produk terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app', $data)

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary { background: white; color: var(--text-main); padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover { background: var(--bg-color); }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .form-control-stok { padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 14px; width: 100px; text-align: center; font-weight: bold; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Manajemen Stok Harian</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Atur stok harian (kuota) untuk setiap produk. Stok akan berkurang otomatis saat terjadi penjualan.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'harian']) }}" class="btn-secondary">Laporan Harian</a>
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'bulanan']) }}" class="btn-secondary">Laporan Bulanan</a>
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'pertanggal']) }}" class="btn-secondary">Lap. Pertanggal</a>
        <a href="{{ route('admin.laporan.penjualan', ['type' => 'lengkap']) }}" class="btn-secondary">Rekapan Lengkap</a>
    </div>
</div>

<div style="padding: 24px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.operasional.stok.update') }}" method="POST">
        @csrf
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Sisa Stok Saat Ini</th>
                    <th>Update Stok Harian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $produk)
                    <tr>
                        <td style="color: var(--text-muted);">{{ $produk->category ?: '-' }}</td>
                        <td style="font-weight: 500;">{{ $produk->name }}</td>
                        <td>Rp {{ number_format($produk->price, 0, ',', '.') }}</td>
                        <td style="font-weight: bold; color: {{ $produk->stock <= 5 ? '#dc2626' : 'var(--text-main)' }};">
                            {{ $produk->stock }}
                        </td>
                        <td>
                            <input type="number" name="stocks[{{ $produk->id }}]" value="{{ $produk->stock }}" min="0" class="form-control-stok">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Belum ada data produk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($produks->count() > 0)
        <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-primary" style="padding: 12px 24px;">Simpan Stok Harian</button>
        </div>
        @endif
    </form>
</div>
@endsection

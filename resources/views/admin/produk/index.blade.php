@extends('admin.layouts.app', $data)

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary { background: white; color: var(--text-main); padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover { background: var(--bg-color); }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; }
    .btn-action:hover { background: var(--bg-color); }
    .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .file-input-wrapper { position: relative; overflow: hidden; display: inline-block; }
    .file-input-wrapper input[type=file] { font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Daftar Produk</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola produk, harga, dan stok yang dijual di kedai Anda.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.operasional.produk.export') }}" class="btn-secondary">Export Excel</a>
        
        <form action="{{ route('admin.operasional.produk.import') }}" method="POST" enctype="multipart/form-data" class="file-input-wrapper" style="margin: 0;">
            @csrf
            <button type="button" class="btn-secondary">Import Excel</button>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" onchange="this.form.submit()">
        </form>

        <a href="{{ route('admin.operasional.produk.create') }}" class="btn-primary">Tambah Produk</a>
    </div>
</div>

<div style="padding: 24px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            File yang diunggah tidak valid atau format salah.
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produks as $item)
                <tr>
                    <td style="color: var(--text-muted);">{{ $item->sku ?: '-' }}</td>
                    <td style="font-weight: 500;">{{ $item->name }}</td>
                    <td>{{ $item->category ?: '-' }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>
                        <span class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.operasional.produk.edit', $item->id) }}" class="btn-action">Edit</a>
                        <form action="{{ route('admin.operasional.produk.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action" style="color: #ef4444; border-color: #fca5a5; cursor: pointer;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada data produk.<br>
                        Silakan tambah produk baru atau import dari Excel.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

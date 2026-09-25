@extends('admin.layouts.app', $data)

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; }
    .btn-action:hover { background: var(--bg-color); }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Daftar Kategori</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola kategori untuk mengelompokkan menu di kasir.</p>
    </div>
    <a href="{{ route('admin.operasional.kategori.create') }}" class="btn-primary">Tambah Kategori</a>
</div>

<div style="padding: 24px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Prefix SKU</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategoris as $item)
                <tr>
                    <td style="font-weight: 500;">{{ $item->name }}</td>
                    <td style="font-family: monospace; color: var(--text-muted); background: #f8fafc; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 10px;">{{ $item->prefix }}</td>
                    <td>
                        <a href="{{ route('admin.operasional.kategori.edit', $item->id) }}" class="btn-action">Edit</a>
                        <form action="{{ route('admin.operasional.kategori.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action" style="color: #ef4444; border-color: #fca5a5; cursor: pointer; background: transparent;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada data kategori.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

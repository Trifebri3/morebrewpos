@extends('superadmin.layouts.app', $data)

@section('content')
<style>
    .table-container { background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    th { font-weight: 600; color: var(--text-muted); background: var(--bg-color); }
    .btn-action { padding: 6px 12px; border-radius: 4px; border: 1px solid var(--border-color); text-decoration: none; color: var(--text-main); font-size: 12px; margin-right: 4px; display: inline-block;}
    .btn-action:hover { background: var(--bg-color); }
    .btn-primary-sm { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; }
    .status-active { color: #10b981; font-weight: 500; }
    .status-inactive { color: #ef4444; font-weight: 500; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Data Kedai</h1>
    <a href="{{ route('superadmin.kedai.create') }}" class="btn-primary-sm">+ Tambah Kedai</a>
</div>

<div style="padding: 0 40px 40px;">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama Kedai</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kedais as $kedai)
                <tr>
                    <td style="font-weight: 500;">{{ $kedai->name }}</td>
                    <td style="color: var(--text-muted);">{{ $kedai->address ?? '-' }}</td>
                    <td style="color: var(--text-muted);">{{ $kedai->phone ?? '-' }}</td>
                    <td>
                        @if($kedai->is_active)
                            <span class="status-active">Aktif</span>
                        @else
                            <span class="status-inactive">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.kedai.edit', $kedai->id) }}" class="btn-action">Edit</a>
                        <form action="{{ route('superadmin.kedai.destroy', $kedai->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus kedai ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action" style="color: #ef4444; cursor: pointer; border-color: #fca5a5;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada data kedai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

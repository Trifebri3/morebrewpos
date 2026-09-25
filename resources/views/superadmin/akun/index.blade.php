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
    .role-label { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .role-admin { background: #dbeafe; color: #1e40af; }
    .role-kasir { background: #fef3c7; color: #b45309; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Akun Admin & Kasir</h1>
    <a href="{{ route('superadmin.akun.create') }}" class="btn-primary-sm">+ Tambah Akun</a>
</div>

<div style="padding: 0 40px 40px;">
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran (Role)</th>
                    <th>Penempatan Kedai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="font-weight: 500;">{{ $user->name }}</td>
                    <td style="color: var(--text-muted);">{{ $user->email }}</td>
                    <td>
                        <span class="role-label role-{{ $user->role }}">{{ $user->role }}</span>
                    </td>
                    <td>{{ $user->kedai ? $user->kedai->name : '-' }}</td>
                    <td>
                        <a href="{{ route('superadmin.akun.edit', $user->id) }}" class="btn-action">Edit</a>
                        <a href="{{ route('superadmin.akun.impersonate', $user->id) }}" class="btn-action" style="background: var(--text-main); color: white; border-color: var(--text-main);" onsubmit="return confirm('Login sebagai user ini?');">Login Sebagai</a>
                        <form action="{{ route('superadmin.akun.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action" style="color: #ef4444; cursor: pointer; border-color: #fca5a5;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">Belum ada data akun admin/kasir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

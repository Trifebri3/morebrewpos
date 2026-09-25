@extends('superadmin.layouts.app', $data)

@section('content')
<style>
    .form-container { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 32px; max-width: 600px; margin: 0 40px; }
    .form-group { margin-bottom: 24px; }
    .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .form-group input[type="text"], .form-group textarea { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--text-muted); }
    .btn-submit { background: var(--text-main); color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; cursor: pointer; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <h1>Tambah Kedai Baru</h1>
    <a href="{{ route('superadmin.kedai.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px;">Kembali</a>
</div>

<div class="form-container">
    <form action="{{ route('superadmin.kedai.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama Kedai</label>
            <input type="text" name="name" required placeholder="Contoh: Kedai Utama Jakarta">
        </div>
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="phone" placeholder="Contoh: 08123456789">
        </div>
        <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap..."></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Simpan Kedai</button>
    </form>
</div>
@endsection

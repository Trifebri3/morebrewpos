@extends('superadmin.layouts.app', $data)

@section('content')
<style>
    .form-container { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 32px; max-width: 600px; }
    .form-group { margin-bottom: 24px; }
    .form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-main); }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s; }
    .form-control:focus { border-color: var(--text-muted); }
    .btn-submit { background: var(--text-main); color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .btn-submit:hover { background: var(--primary-hover); }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <div>
        <h1>Tambah Akun Baru</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Buat akun Admin atau Kasir baru dan tugaskan ke kedai tertentu.</p>
    </div>
    <a href="{{ route('superadmin.akun.index') }}" style="color: var(--text-main); text-decoration: none; font-size: 14px; font-weight: 500;">&larr; Kembali</a>
</div>

<div style="padding: 0 40px 40px;">
    <div class="form-container">
        <form action="{{ route('superadmin.akun.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Contoh: budi@example.com">
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Peran (Role)</label>
                <select name="role" class="form-control" required>
                    <option value="" disabled selected>Pilih Peran</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Kedai</option>
                    <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
                @error('role') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Tugaskan ke Kedai</label>
                <select name="kedai_id" class="form-control" required>
                    <option value="" disabled selected>Pilih Kedai</option>
                    @foreach($kedais as $kedai)
                        <option value="{{ $kedai->id }}" {{ old('kedai_id') == $kedai->id ? 'selected' : '' }}>{{ $kedai->name }} - {{ $kedai->address }}</option>
                    @endforeach
                </select>
                @error('kedai_id') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-submit">Simpan Akun</button>
        </form>
    </div>
</div>
@endsection

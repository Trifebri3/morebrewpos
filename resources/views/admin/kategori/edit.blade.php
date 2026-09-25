@extends('admin.layouts.app', $data)

@section('content')
<style>
    .form-container { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 32px; max-width: 600px; }
    .form-group { margin-bottom: 24px; }
    .form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-main); }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; }
    .btn-submit { background: var(--text-main); color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <div>
        <h1>Edit Kategori</h1>
    </div>
    <a href="{{ route('admin.operasional.kategori.index') }}" style="color: var(--text-main); text-decoration: none; font-size: 14px; font-weight: 500;">&larr; Kembali</a>
</div>

<div style="padding: 24px 40px 40px;">
    <div class="form-container">
        <form action="{{ route('admin.operasional.kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $kategori->name) }}" required>
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Prefix SKU</label>
                <input type="text" name="prefix" class="form-control" value="{{ old('prefix', $kategori->prefix) }}" required>
                @error('prefix') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

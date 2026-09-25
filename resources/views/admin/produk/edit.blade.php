@extends('admin.layouts.app', $data)

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
    .flex-row { display: flex; gap: 16px; }
    .flex-row .form-group { flex: 1; }
</style>

<div class="page-header" style="padding: 32px 40px 0;">
    <div>
        <h1>Edit Produk</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Perbarui detail produk, harga, atau stok.</p>
    </div>
    <a href="{{ route('admin.operasional.produk.index') }}" style="color: var(--text-main); text-decoration: none; font-size: 14px; font-weight: 500;">&larr; Kembali</a>
</div>

<div style="padding: 24px 40px 40px;">
    <div class="form-container">
        <form action="{{ route('admin.operasional.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Foto Produk (Opsional)</label>
                @if($produk->image)
                    <img src="{{ Storage::url($produk->image) }}" alt="Preview" style="height: 60px; border-radius: 6px; margin-bottom: 8px; object-fit: cover; display: block;">
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Biarkan kosong jika tidak ingin mengubah foto.</span>
                @error('image') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $produk->name) }}" required>
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex-row">
                <div class="form-group">
                    <label class="form-label">SKU (Kode Produk)</label>
                    <input type="text" class="form-control" value="{{ $produk->sku }}" disabled style="background: #f3f4f6; color: var(--text-muted); cursor: not-allowed;">
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">SKU tidak dapat diubah setelah dibuat.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-control" required>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->name }}" {{ old('category', $produk->category) == $kat->name ? 'selected' : '' }}>{{ $kat->name }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="flex-row">
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', (int)$produk->price) }}" required min="0">
                    @error('price') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $produk->stock) }}" required min="0">
                    @error('stock') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 500;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $produk->is_active) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--text-main);">
                    Produk Aktif (Tampil di POS Kasir)
                </label>
            </div>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

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
    <h1>Edit Kedai</h1>
    <a href="{{ route('superadmin.kedai.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px;">Kembali</a>
</div>

<div class="form-container">
    <form action="{{ route('superadmin.kedai.update', $kedai->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nama Kedai</label>
            <input type="text" name="name" required value="{{ $kedai->name }}">
        </div>
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="phone" value="{{ $kedai->phone }}">
        </div>
        <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea name="address" rows="3">{{ $kedai->address }}</textarea>
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $kedai->is_active ? 'checked' : '' }}>
            <label for="is_active" style="margin: 0; font-weight: 400; cursor: pointer;">Kedai Aktif</label>
        </div>

        <h3 style="margin-top: 32px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Pengaturan Struk (Receipt)</h3>
        
        <div class="form-group">
            <label>Wi-Fi SSID</label>
            <input type="text" name="wifi_ssid" value="{{ $kedai->wifi_ssid }}" placeholder="Contoh: moreandmore">
        </div>
        <div class="form-group">
            <label>Wi-Fi Password</label>
            <input type="text" name="wifi_password" value="{{ $kedai->wifi_password }}" placeholder="Contoh: bolehlihatsenyumnya?">
        </div>
        <div class="form-group">
            <label>Instagram</label>
            <input type="text" name="instagram" value="{{ $kedai->instagram }}" placeholder="Contoh: @morebrewcoffee">
        </div>
        
        <button type="submit" class="btn-submit">Update Kedai</button>
    </form>
</div>
@endsection

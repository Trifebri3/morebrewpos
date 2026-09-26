@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
    .card-header { padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; }
    .card-subtitle { font-size: 14px; color: var(--text-muted); margin-top: 4px; }
    .card-body { padding: 24px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
    .input-control { width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; }
    .input-control:focus { border-color: var(--text-main); }
    
    .btn-primary { background: var(--text-main); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    
    .alert-success { background: #dcfce7; color: #166534; padding: 16px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 24px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 12px; }
    
    .divider { height: 1px; background: var(--border-color); margin: 32px 0; }
</style>

@if (session('success'))
<div class="alert-success">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.kedai.pengaturan.update') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Pengaturan Kedai</h2>
                <p class="card-subtitle">Atur profil dan pengaturan lokasi untuk keperluan absensi karyawan.</p>
            </div>
        </div>
        
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Kedai</label>
                <input type="text" name="name" id="name" value="{{ old('name', $kedai->name ?? '') }}" class="input-control" required placeholder="Masukkan nama kedai...">
            </div>

            <div class="divider"></div>
            
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0;">Zona Absensi (Geofencing)</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Masukkan titik pusat koordinat kedai dan radius (dalam meter) agar absen hanya valid di area tersebut.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $kedai->latitude ?? '') }}" class="input-control" placeholder="Contoh: -6.9174639">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $kedai->longitude ?? '') }}" class="input-control" placeholder="Contoh: 107.6191228">
                </div>
            </div>

            <div class="form-group" style="max-width: 300px;">
                <label for="radius_meter">Radius Maksimal Absensi (Meter)</label>
                <input type="number" name="radius_meter" id="radius_meter" value="{{ old('radius_meter', $kedai->radius_meter ?? 50) }}" min="10" class="input-control" required>
            </div>

            <div class="divider"></div>
            
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0;">Fitur Absensi QR di Kasir</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Aktifkan fitur ini jika Anda ingin tombol Absen QR muncul di layar Kasir (POS).</p>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="is_qr_absen_enabled" value="1" {{ old('is_qr_absen_enabled', $kedai->is_qr_absen_enabled ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--text-main);">
                    <span style="font-size: 15px; font-weight: 600;">Tampilkan Tombol Absen di POS Kasir</span>
                </label>
            </div>
        </div>
        
        <div style="padding: 24px; background: #f8fafc; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Pengaturan
            </button>
        </div>
    </div>
</form>
@endsection

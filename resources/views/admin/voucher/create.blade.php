@extends('admin.layouts.app', $data ?? ['title' => 'Tambah Voucher'])

@section('content')
<style>
    .form-container { background: white; border-radius: 8px; border: 1px solid var(--border-color); padding: 32px; max-width: 800px; }
    .form-group { margin-bottom: 24px; }
    .form-row { display: flex; gap: 24px; margin-bottom: 24px; }
    .form-row .form-group { flex: 1; margin-bottom: 0; }
    .form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-main); }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; }
    .btn-submit { background: var(--text-main); color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Tambah Voucher</h1>
    </div>
    <a href="{{ route('admin.penjualan.voucher.index') }}" style="color: var(--text-main); text-decoration: none; font-size: 14px; font-weight: 500;">&larr; Kembali</a>
</div>

<div style="padding: 24px 40px 40px;">
    <div class="form-container">
        <form action="{{ route('admin.penjualan.voucher.store') }}" method="POST">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kode Voucher</label>
                    <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" required placeholder="PROMO2026">
                    @error('kode') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Promo</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Diskon Akhir Tahun">
                    @error('nama') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe Diskon</label>
                    <select name="tipe_diskon" class="form-control" required>
                        <option value="persen" {{ old('tipe_diskon') == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="nominal" {{ old('tipe_diskon') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                    @error('tipe_diskon') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Diskon</label>
                    <input type="number" name="nilai_diskon" class="form-control" value="{{ old('nilai_diskon') }}" required min="0" step="any">
                    @error('nilai_diskon') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Minimal Belanja (Opsional)</label>
                    <input type="number" name="minimal_belanja" class="form-control" value="{{ old('minimal_belanja') }}" min="0" placeholder="0">
                    @error('minimal_belanja') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Kuota (Opsional)</label>
                    <input type="number" name="kuota" class="form-control" value="{{ old('kuota') }}" min="1" placeholder="Kosongkan jika tanpa batas">
                    @error('kuota') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai (Opsional)</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                    @error('tanggal_mulai') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai (Opsional)</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                    @error('tanggal_selesai') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Hari Berlaku (Opsional)</label>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <label style="font-size: 14px; display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="hari_berlaku[]" value="{{ $hari }}" {{ is_array(old('hari_berlaku')) && in_array($hari, old('hari_berlaku')) ? 'checked' : '' }}>
                            {{ $hari }}
                        </label>
                    @endforeach
                </div>
                <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Biarkan kosong jika berlaku setiap hari.</span>
                @error('hari_berlaku') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Mulai (Opsional)</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}">
                    @error('jam_mulai') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai (Opsional)</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}">
                    @error('jam_selesai') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                <label for="status" style="font-size: 14px;">Aktifkan Voucher</label>
                @error('status') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-submit">Simpan Voucher</button>
        </form>
    </div>
</div>
@endsection

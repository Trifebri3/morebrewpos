@extends('superadmin.layouts.app', $data)

@section('content')
<!-- Leaflet CSS & JS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .settings-container {
        padding: 32px 40px 60px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .header-banner h1 {
        font-size: 26px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .header-banner p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 28px;
    }

    .card-header {
        padding: 20px 28px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header span {
        font-size: 13px;
        color: #64748b;
        font-weight: normal;
    }

    .card-body {
        padding: 28px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group .helper-text {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .input-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .input-control:focus {
        border-color: #212121;
        box-shadow: 0 0 0 3px rgba(33, 33, 33, 0.1);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .grid-2, .grid-3 {
            grid-template-columns: 1fr;
        }
    }

    /* Logo Uploader & Preview */
    .logo-uploader-box {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 18px 24px;
        background: #fafafa;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }

    .logo-preview-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        flex-shrink: 0;
    }

    .logo-preview-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Interactive Map Box */
    #map-preview {
        width: 100%;
        height: 380px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        z-index: 10;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
    }

    .map-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .search-map-box {
        display: flex;
        gap: 8px;
        flex: 1;
        min-width: 260px;
    }

    .btn-action-map {
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .btn-gps {
        background: #10b981;
        color: white;
    }

    .btn-gps:hover {
        background: #059669;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-search {
        background: #334155;
        color: white;
    }

    .btn-search:hover {
        background: #1e293b;
    }

    .badge-coords {
        background: #f1f5f9;
        color: #334155;
        padding: 6px 12px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-submit {
        background: #212121;
        color: #ffffff;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background: #000000;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>

<div class="settings-container">
    <div class="header-banner">
        <div>
            <h1>Edit Kedai & Titik GPS</h1>
            <p>Pengaturan profil kedai, logo struk, alamat, dan koordinat GPS geofencing untuk Superadmin.</p>
        </div>
        <a href="{{ route('superadmin.kedai.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
            &larr; Kembali ke Daftar Kedai
        </a>
    </div>

    <form action="{{ route('superadmin.kedai.update', $kedai->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Card 1: Logo & Profil Kedai -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Profil Kedai & Logo Resmi
                </h2>
                <span>Mempengaruhi format struk thermal dan kop cetak</span>
            </div>
            <div class="card-body">
                <!-- Logo Uploader -->
                <div class="form-group">
                    <label>Logo Resmi (Muncul di Struk & Website)</label>
                    <div class="logo-uploader-box">
                        <div class="logo-preview-wrapper">
                            <img id="logo-preview" src="{{ asset('logo.png') }}?v={{ time() }}" alt="Logo MoreBrew" onerror="this.src='https://placehold.co/80x80/212121/ffffff?text=Logo'">
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="logo" id="logo-input" accept="image/*" class="input-control" style="padding: 8px 12px; background: white;">
                            <div class="helper-text">Format yang disarankan: PNG transparan (resolusi persegi min. 200x200px, max 2MB).</div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="name">Nama Kedai</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $kedai->name) }}" class="input-control" required placeholder="Nama Kedai">
                    </div>
                    <div class="form-group">
                        <label for="phone">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $kedai->phone) }}" class="input-control" placeholder="Contoh: 0812-3456-7890">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap Toko</label>
                    <textarea name="address" id="address" rows="3" class="input-control" placeholder="Alamat lengkap...">{{ old('address', $kedai->address) }}</textarea>
                    <div class="helper-text">Alamat pada struk kasir otomatis terbagi menjadi 2 baris proporsional.</div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $kedai->is_active ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #212121;">
                        <span style="font-size: 14px; font-weight: 500; color: #1e293b;">Kedai Aktif Beroperasi</span>
                    </label>
                </div>

                <div class="grid-3" style="margin-top: 14px; padding-top: 18px; border-top: 1px solid #f1f5f9;">
                    <div class="form-group">
                        <label for="wifi_ssid">Wi-Fi SSID</label>
                        <input type="text" name="wifi_ssid" id="wifi_ssid" value="{{ old('wifi_ssid', $kedai->wifi_ssid) }}" class="input-control" placeholder="Contoh: moreandmore">
                    </div>
                    <div class="form-group">
                        <label for="wifi_password">Password Wi-Fi</label>
                        <input type="text" name="wifi_password" id="wifi_password" value="{{ old('wifi_password', $kedai->wifi_password) }}" class="input-control" placeholder="Contoh: bolehmintasenyumnya?">
                    </div>
                    <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $kedai->instagram) }}" class="input-control" placeholder="Contoh: @morebrewcoffee">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Interactive Leaflet Map for GPS -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Peta Interaktif Titik Koordinat GPS & Radius
                </h2>
                <span>Geser penanda pin pada peta untuk menentukan posisi akurat</span>
            </div>
            <div class="card-body">
                <!-- Map Toolbar -->
                <div class="map-toolbar">
                    <div class="search-map-box">
                        <input type="text" id="map-search-input" placeholder="Cari nama jalan atau lokasi di peta..." class="input-control" style="padding: 9px 14px;">
                        <button type="button" id="btn-search-location" class="btn-action-map btn-search">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Cari
                        </button>
                    </div>

                    <div>
                        <button type="button" id="btn-get-current-gps" class="btn-action-map btn-gps">
                            📍 Ambil Lokasi GPS Saya Saat Ini
                        </button>
                    </div>
                </div>

                <!-- Leaflet Map Container -->
                <div id="map-preview"></div>
                
                <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div style="font-size: 12px; color: #64748b;">
                        💡 Klik atau geser penanda pin biru pada peta untuk memperbarui koordinat secara instan.
                    </div>
                    <div class="badge-coords" id="coords-badge">
                        <span>Koordinat Aktif: </span>
                        <strong id="badge-lat-lon">-6.921477, 107.616654</strong>
                    </div>
                </div>

                <!-- Coordinate Fields -->
                <div class="grid-3" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $kedai->latitude ?? '-6.9214771') }}" class="input-control" placeholder="-6.9214771" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $kedai->longitude ?? '107.6166542') }}" class="input-control" placeholder="107.6166542" required>
                    </div>
                    <div class="form-group">
                        <label for="radius_meter">Radius Maksimal Absen (Meter)</label>
                        <input type="number" name="radius_meter" id="radius_meter" value="{{ old('radius_meter', $kedai->radius_meter ?? 50) }}" min="5" max="5000" class="input-control" required>
                        <div class="helper-text">Batas radius karyawan diizinkan melakukan absen.</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
            <button type="submit" class="btn-submit">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Perbarui Data Kedai
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoInput = document.getElementById('logo-input');
    const logoPreview = document.getElementById('logo-preview');
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meter');
    const badgeLatLon = document.getElementById('badge-lat-lon');

    let initialLat = parseFloat(latInput.value) || -6.9214771;
    let initialLon = parseFloat(lonInput.value) || 107.6166542;
    let initialRadius = parseInt(radiusInput.value) || 50;

    const map = L.map('map-preview').setView([initialLat, initialLon], 16);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);

    let marker = L.marker([initialLat, initialLon], {
        draggable: true,
        title: "Titik Lokasi Kedai"
    }).addTo(map);

    let circle = L.circle([initialLat, initialLon], {
        color: '#2563eb',
        fillColor: '#3b82f6',
        fillOpacity: 0.2,
        radius: initialRadius
    }).addTo(map);

    function updateCoordinates(lat, lon) {
        lat = parseFloat(lat).toFixed(7);
        lon = parseFloat(lon).toFixed(7);

        latInput.value = lat;
        lonInput.value = lon;
        if (badgeLatLon) {
            badgeLatLon.innerText = `${lat}, ${lon}`;
        }

        marker.setLatLng([lat, lon]);
        circle.setLatLng([lat, lon]);
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    function onManualCoordChange() {
        const lat = parseFloat(latInput.value);
        const lon = parseFloat(lonInput.value);
        if (!isNaN(lat) && !isNaN(lon)) {
            marker.setLatLng([lat, lon]);
            circle.setLatLng([lat, lon]);
            map.panTo([lat, lon]);
            if (badgeLatLon) badgeLatLon.innerText = `${lat.toFixed(7)}, ${lon.toFixed(7)}`;
        }
    }
    latInput.addEventListener('input', onManualCoordChange);
    lonInput.addEventListener('input', onManualCoordChange);

    radiusInput.addEventListener('input', function () {
        const rad = parseInt(this.value) || 50;
        circle.setRadius(rad);
    });

    const btnGps = document.getElementById('btn-get-current-gps');
    if (btnGps) {
        btnGps.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
                return;
            }

            const originalHtml = btnGps.innerHTML;
            btnGps.innerHTML = '<span>📡 Mencari Sinyal GPS...</span>';
            btnGps.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;

                    updateCoordinates(userLat, userLon);
                    map.flyTo([userLat, userLon], 18);

                    btnGps.innerHTML = '<span>✅ Lokasi Ditemukan!</span>';
                    setTimeout(() => {
                        btnGps.innerHTML = originalHtml;
                        btnGps.disabled = false;
                    }, 2000);
                },
                function (error) {
                    alert('Gagal mendeteksi lokasi: ' + error.message);
                    btnGps.innerHTML = originalHtml;
                    btnGps.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    const btnSearch = document.getElementById('btn-search-location');
    const searchInput = document.getElementById('map-search-input');

    function searchAddress() {
        const query = searchInput.value.trim();
        if (!query) return;

        btnSearch.innerText = 'Mencari...';
        btnSearch.disabled = true;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;

                if (data && data.length > 0) {
                    const first = data[0];
                    const lat = parseFloat(first.lat);
                    const lon = parseFloat(first.lon);

                    updateCoordinates(lat, lon);
                    map.flyTo([lat, lon], 17);
                } else {
                    alert('Lokasi tidak ditemukan. Coba ketik kata kunci yang lebih spesifik.');
                }
            })
            .catch(err => {
                console.error(err);
                btnSearch.innerText = 'Cari';
                btnSearch.disabled = false;
                alert('Terjadi kesalahan koneksi saat mencari alamat.');
            });
    }

    if (btnSearch && searchInput) {
        btnSearch.addEventListener('click', searchAddress);
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchAddress();
            }
        });
    }

    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
</script>
@endsection

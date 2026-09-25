<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi - {{ $kedai->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-center text-white">
            <h1 class="text-2xl font-bold">Portal Absensi</h1>
            <p class="text-indigo-200 mt-1">{{ $kedai->name }}</p>
        </div>
        
        <div class="p-6">
            <!-- Lokasi Status -->
            <div id="location-status" class="mb-6 p-3 rounded-lg flex items-start space-x-3 bg-yellow-50 text-yellow-700 border border-yellow-200">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-sm">
                    <p class="font-semibold">Mencari Lokasi GPS...</p>
                    <p id="location-text" class="text-xs mt-1">Sistem sedang mendeteksi keberadaan Anda.</p>
                </div>
            </div>

            <form id="absen-form" class="space-y-5 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Karyawan / Email</label>
                    <input type="text" id="identifier" class="w-full border-gray-300 rounded-lg shadow-sm p-3 border focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan ID Anda..." required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Absen</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 bg-white">
                            <input type="radio" name="type" value="Masuk" class="sr-only peer" required>
                            <span class="text-sm font-medium text-gray-900 peer-checked:text-indigo-600">Masuk</span>
                            <div class="absolute inset-0 border-2 rounded-lg pointer-events-none peer-checked:border-indigo-600"></div>
                        </label>
                        <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 bg-white">
                            <input type="radio" name="type" value="Keluar" class="sr-only peer">
                            <span class="text-sm font-medium text-gray-900 peer-checked:text-indigo-600">Keluar</span>
                            <div class="absolute inset-0 border-2 rounded-lg pointer-events-none peer-checked:border-indigo-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Kamera -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verifikasi Wajah</label>
                    <div class="relative rounded-lg overflow-hidden bg-gray-100 border border-gray-300 aspect-video flex items-center justify-center">
                        <video id="video" class="absolute w-full h-full object-cover" autoplay playsinline></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <p id="camera-loading" class="text-sm text-gray-500">Membuka kamera...</p>
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50">
                    Ambil Foto & Absen
                </button>
            </form>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const form = document.getElementById('absen-form');
        const locationStatus = document.getElementById('location-status');
        const locationText = document.getElementById('location-text');
        const btnSubmit = document.getElementById('btn-submit');
        
        let userLat = null;
        let userLon = null;
        let stream = null;

        const kedaiLat = {{ $kedai->latitude ?? 'null' }};
        const kedaiLon = {{ $kedai->longitude ?? 'null' }};
        const maxRadius = {{ $kedai->radius_meter ?? 50 }};

        // 1. Get GPS Location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLon = position.coords.longitude;
                    
                    if (kedaiLat && kedaiLon) {
                        const dist = calculateDistance(kedaiLat, kedaiLon, userLat, userLon);
                        if (dist > maxRadius) {
                            showLocationError(`Anda berada di luar zona absen. Jarak: ${Math.round(dist)}m dari titik absensi (Maks: ${maxRadius}m).`);
                            return;
                        }
                    }
                    
                    showLocationSuccess("Lokasi valid! Anda berada di dalam zona kedai.");
                    startCamera();
                },
                (error) => {
                    showLocationError("Gagal mendapatkan lokasi. Pastikan GPS aktif dan izinkan browser mengakses lokasi.");
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            showLocationError("Browser Anda tidak mendukung GPS Geolocation.");
        }

        // 2. Start Camera
        async function startCamera() {
            form.classList.remove('hidden');
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
                video.srcObject = stream;
                document.getElementById('camera-loading').classList.add('hidden');
            } catch (err) {
                alert("Gagal mengakses kamera. Mohon berikan izin kamera.");
            }
        }

        // 3. Handle Form Submit (Snap Photo & AJAX)
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!stream) {
                alert("Kamera belum aktif!"); return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerText = "Memproses...";

            // Snap photo
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const photoData = canvas.toDataURL('image/png');

            const identifier = document.getElementById('identifier').value;
            const type = document.querySelector('input[name="type"]:checked').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch("{{ url('/absen') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        identifier: identifier,
                        type: type,
                        latitude: userLat,
                        longitude: userLon,
                        photo: photoData
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    let msg = result.message;
                    if(result.status === 'luar_zona') msg += " (Peringatan: Tercatat di Luar Zona)";
                    alert(msg);
                    form.reset();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                alert("Terjadi kesalahan jaringan.");
            }

            btnSubmit.disabled = false;
            btnSubmit.innerText = "Ambil Foto & Absen";
        });

        // Utils
        function showLocationError(msg) {
            locationStatus.className = "mb-6 p-3 rounded-lg flex items-start space-x-3 bg-red-50 text-red-700 border border-red-200";
            locationStatus.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-bold">Akses Ditolak</p>
                    <p class="mt-1">${msg}</p>
                </div>
            `;
        }

        function showLocationSuccess(msg) {
            locationStatus.className = "mb-6 p-3 rounded-lg flex items-start space-x-3 bg-green-50 text-green-700 border border-green-200";
            locationStatus.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div class="text-sm">
                    <p class="font-bold">Lokasi Sesuai</p>
                    <p class="mt-1">${msg}</p>
                </div>
            `;
        }

        // Haversine formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // metres
            const p1 = lat1 * Math.PI/180;
            const p2 = lat2 * Math.PI/180;
            const dp = (lat2-lat1) * Math.PI/180;
            const dl = (lon2-lon1) * Math.PI/180;

            const a = Math.sin(dp/2) * Math.sin(dp/2) +
                    Math.cos(p1) * Math.cos(p2) *
                    Math.sin(dl/2) * Math.sin(dl/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
    </script>
</body>
</html>

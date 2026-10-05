<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Presensi Karyawan - {{ preg_replace('/^kedai\s+/i', '', $kedai->name ?? 'MOREBREWW') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Seamless Logo - No container box */
        .seamless-logo-img {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.08));
        }
        /* Camera viewfinder overlay */
        .camera-overlay-frame {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .face-oval {
            width: 170px;
            height: 220px;
            border: 2px dashed rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.25);
        }
        /* Active Radio Option */
        .type-card.active-in {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
            color: #065f46 !important;
            box-shadow: 0 0 0 2px #10b981;
        }
        .type-card.active-out {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            box-shadow: 0 0 0 2px #ef4444;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-3 sm:p-6 text-slate-800">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
        
        <!-- Header: Clean White & Seamless Floating Logo (NO BOX CONTAINER) -->
        <div class="pt-8 pb-4 px-6 text-center bg-white border-b border-slate-100">
            @php
                $logoUrl = asset('logo.png');
                if (!empty($kedai->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($kedai->logo)) {
                    $logoUrl = asset('storage/' . $kedai->logo);
                }
            @endphp
            <div class="flex justify-center mb-3">
                <img src="{{ $logoUrl }}" alt="Logo Kedai" class="h-12 max-w-[190px] object-contain seamless-logo-img" onerror="this.src='{{ asset('logo.png') }}'">
            </div>
            
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Portal Presensi Staf</h1>
            <p class="text-slate-500 text-xs font-semibold tracking-wide uppercase mt-0.5">{{ preg_replace('/^kedai\s+/i', '', $kedai->name ?? 'MOREBREWW') }}</p>

            <!-- Live Digital Clock & Date -->
            <div class="mt-4 py-2 px-4 bg-slate-50 rounded-2xl border border-slate-200/80 inline-flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <svg width="14" height="14" fill="none" stroke="#000000" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <div class="h-3 w-[1px] bg-slate-300"></div>
                <div id="live-clock" class="font-mono text-sm font-bold text-slate-900 tracking-wider">--:--:-- WIB</div>
            </div>
        </div>
        
        <div class="p-6 sm:p-7 space-y-6">

            <!-- Lokasi GPS Status Banner -->
            <div id="location-status" class="p-4 rounded-2xl flex items-start gap-3.5 bg-amber-50 text-amber-900 border border-amber-200/80 transition-all">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-sm">
                    <p class="font-bold">Mendeteksi Lokasi GPS...</p>
                    <p id="location-text" class="text-xs text-amber-700/90 mt-0.5">Memastikan Anda berada di dalam zona kedai.</p>
                </div>
            </div>

            <!-- Form Absensi Utama -->
            <form id="absen-form" class="space-y-6 hidden">
                
                <!-- 1. IDENTIFIKASI KARYAWAN (NAMA ATAU ID) -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">1. Masukkan Nama atau ID Pegawai</label>
                        <span id="validation-pill" class="text-[11px] font-bold text-slate-400">Verifikasi Wajib</span>
                    </div>

                    <!-- Input Nama atau ID Tunggal -->
                    <div class="relative">
                        <input type="text" 
                               id="identifier" 
                               class="w-full bg-white border-2 border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-normal outline-none focus:border-black transition" 
                               placeholder="Ketik Nama (cth: Bila, ajay) atau ID (cth: 5)..." 
                               autocomplete="off"
                               required>
                        
                        <div id="identifier-spinner" class="absolute right-4 top-4 hidden">
                            <svg class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>

                    <!-- KOTAK STATUS: JIKA VALID (HIJAU ✓) -->
                    <div id="user-valid-box" class="mt-3 p-3.5 rounded-2xl bg-emerald-50 border-2 border-emerald-500/80 text-emerald-950 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white text-sm font-extrabold flex items-center justify-center shadow-sm" id="avatar-initial">-</div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900" id="user-name-text">-</div>
                                    <div class="text-xs text-emerald-700 font-semibold" id="user-role-text">-</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white text-[11px] font-bold flex items-center gap-1 shadow-sm">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="3"/></svg>
                                Valid
                            </span>
                        </div>
                    </div>

                    <!-- KOTAK STATUS: JIKA DITOLAK (MERAH ✕) -->
                    <div id="user-invalid-box" class="mt-3 p-3.5 rounded-2xl bg-rose-50 border-2 border-rose-500/80 text-rose-950 hidden animate-shake">
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-rose-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                                ✕
                            </div>
                            <div class="text-xs">
                                <p class="font-extrabold text-rose-900 text-[13px]">Karyawan Ditolak / Tidak Ditemukan!</p>
                                <p class="text-rose-700 mt-0.5 leading-relaxed" id="user-invalid-message">
                                    Nama atau ID tidak terdaftar di sistem kedai. Absensi tidak dapat diproses.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SESI PRESENSI HARIAN (MASUK & KELUAR) -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Sesi Presensi Hari Ini</label>
                        <span id="shift-recommendation" class="text-[11px] font-bold text-slate-500">Pilih Masuk / Keluar</span>
                    </div>

                    <!-- Dual Cards: Masuk & Keluar -->
                    <div class="grid grid-cols-2 gap-3">
                        
                        <!-- CARD MASUK (Awal Datang) -->
                        <div id="card-type-masuk" onclick="selectType('Masuk')" class="type-card relative flex flex-col justify-between p-3.5 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-slate-300 transition bg-white select-none">
                            <input type="radio" name="type" value="Masuk" id="radio-masuk" class="sr-only" required>
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-500 flex items-center gap-1.5">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    Awal Kerja
                                </span>
                                <div id="dot-masuk" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-transparent"></div>
                                </div>
                            </div>

                            <div>
                                <div class="text-base font-extrabold text-slate-900 leading-tight">Absen Masuk</div>
                                <div id="status-masuk-subtext" class="text-[11px] text-slate-500 mt-1">Saat tiba di kedai</div>
                            </div>

                            <!-- Badge status jika sudah absen -->
                            <div id="badge-masuk-done" class="mt-2.5 py-1 px-2 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-bold hidden flex items-center gap-1">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="3"/></svg>
                                <span id="time-masuk-text">Tercatat</span>
                            </div>
                        </div>

                        <!-- CARD KELUAR (Pulang Kerja) -->
                        <div id="card-type-keluar" onclick="selectType('Keluar')" class="type-card relative flex flex-col justify-between p-3.5 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-slate-300 transition bg-white select-none">
                            <input type="radio" name="type" value="Keluar" id="radio-keluar" class="sr-only">
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-500 flex items-center gap-1.5">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Selesai Kerja
                                </span>
                                <div id="dot-keluar" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-transparent"></div>
                                </div>
                            </div>

                            <div>
                                <div class="text-base font-extrabold text-slate-900 leading-tight">Absen Keluar</div>
                                <div id="status-keluar-subtext" class="text-[11px] text-slate-500 mt-1">Saat selesai shift</div>
                            </div>

                            <!-- Badge status jika sudah absen -->
                            <div id="badge-keluar-done" class="mt-2.5 py-1 px-2 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-bold hidden flex items-center gap-1">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="3"/></svg>
                                <span id="time-keluar-text">Tercatat</span>
                            </div>
                        </div>

                    </div>

                    <!-- All Done Banner -->
                    <div id="all-completed-banner" class="mt-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 hidden flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="3"/></svg>
                        </div>
                        <div class="text-xs">
                            <p class="font-extrabold text-emerald-950">Presensi Hari Ini Lengkap!</p>
                            <p class="text-emerald-700 mt-0.5">Anda sudah menyelesaikan Absen Masuk & Keluar hari ini.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. VERIFIKASI WAJAH / KAMERA -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">3. Verifikasi Foto Wajah</label>
                        <button type="button" onclick="flipCamera()" class="text-[11px] font-semibold text-slate-600 hover:text-black flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Ganti Kamera
                        </button>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-200 aspect-[4/3] flex items-center justify-center shadow-inner">
                        <video id="video" class="absolute w-full h-full object-cover" autoplay playsinline muted></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        
                        <!-- Face Guide Silhouette Overlay -->
                        <div class="camera-overlay-frame" id="camera-overlay">
                            <div class="face-oval"></div>
                        </div>

                        <!-- Loading State -->
                        <div id="camera-loading" class="flex flex-col items-center gap-2 text-slate-400 z-10 text-xs">
                            <svg class="w-6 h-6 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengaktifkan Kamera...</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 text-center mt-1.5">Posisikan wajah Anda tepat di dalam bingkai oval.</p>
                </div>

                <!-- TOMBOL SUBMIT BESAR & TAKTIL (DIKUNCI JIKA TIDAK VALID) -->
                <button type="submit" id="btn-submit" disabled class="w-full py-4 px-6 rounded-2xl shadow-lg text-sm font-extrabold text-white bg-slate-300 cursor-not-allowed transition-all flex items-center justify-center gap-2.5">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span id="btn-submit-label">Masukkan Nama / ID Karyawan Dahulu</span>
                </button>
            </form>
        </div>

        <div class="py-3.5 bg-slate-50 border-t border-slate-100 text-center">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide uppercase">Sistem Absensi Pintar • MoreBrew POS</span>
        </div>
    </div>

    <!-- NOTIFIKASI POPUP HASIL ABSEN (MODAL IN-APP) -->
    <div id="result-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl animate-in fade-in zoom-in-95 duration-200">
            <div id="modal-icon-container" class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <svg id="modal-icon" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="3"/></svg>
            </div>
            <h3 id="modal-title" class="text-lg font-extrabold text-slate-900 mb-1">Berhasil Absen!</h3>
            <p id="modal-message" class="text-sm text-slate-600 mb-6">Data presensi Anda telah tercatat dengan aman di server.</p>
            <button type="button" onclick="closeResultModal()" class="w-full py-3 px-4 bg-black text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition">
                Selesai / Mengerti
            </button>
        </div>
    </div>

    <script>
        // 1. Live Digital Clock
        function updateLiveClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('live-clock').innerText = `${h}:${m}:${s} WIB`;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const form = document.getElementById('absen-form');
        const locationStatus = document.getElementById('location-status');
        const locationText = document.getElementById('location-text');
        const btnSubmit = document.getElementById('btn-submit');
        const btnSubmitLabel = document.getElementById('btn-submit-label');
        const identifierInput = document.getElementById('identifier');
        const validationPill = document.getElementById('validation-pill');
        
        let userLat = null;
        let userLon = null;
        let stream = null;
        let currentFacingMode = "user";
        let isEmployeeValid = false;
        let validatedIdentifier = '';

        const kedaiLat = {{ $kedai->latitude ?? 'null' }};
        const kedaiLon = {{ $kedai->longitude ?? 'null' }};
        const maxRadius = {{ $kedai->radius_meter ?? 50 }};

        // 2. Geolocation Check
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLon = position.coords.longitude;
                    
                    if (kedaiLat && kedaiLon) {
                        const dist = calculateDistance(kedaiLat, kedaiLon, userLat, userLon);
                        if (dist > maxRadius) {
                            showLocationError(`Anda berada di luar zona kedai (${Math.round(dist)}m dari titik pusat, maks ${maxRadius}m).`);
                            return;
                        }
                    }
                    
                    showLocationSuccess("Lokasi valid! Anda berada di dalam zona kedai.");
                    startCamera();
                },
                (error) => {
                    showLocationError("Gagal mendeteksi lokasi. Harap aktifkan GPS dan izinkan browser mengakses lokasi.");
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            showLocationError("Browser ini tidak mendukung GPS Geolocation.");
        }

        // 3. Selection of Type: Masuk / Keluar
        let selectedAttendanceType = 'Masuk';

        function selectType(type) {
            selectedAttendanceType = type;
            const radioMasuk = document.getElementById('radio-masuk');
            const radioKeluar = document.getElementById('radio-keluar');
            const cardMasuk = document.getElementById('card-type-masuk');
            const cardKeluar = document.getElementById('card-type-keluar');
            const dotMasuk = document.getElementById('dot-masuk');
            const dotKeluar = document.getElementById('dot-keluar');

            if (type === 'Masuk') {
                radioMasuk.checked = true;
                radioKeluar.checked = false;
                cardMasuk.classList.add('active-in');
                cardKeluar.classList.remove('active-out');
                dotMasuk.innerHTML = '<div class="w-2 h-2 rounded-full bg-emerald-600"></div>';
                dotMasuk.className = 'w-3.5 h-3.5 rounded-full border-2 border-emerald-600 flex items-center justify-center';
                dotKeluar.innerHTML = '';
                dotKeluar.className = 'w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex items-center justify-center';
                
                if (isEmployeeValid) {
                    btnSubmitLabel.innerText = "Ambil Foto & Absen Masuk";
                    btnSubmit.className = "w-full py-4 px-6 rounded-2xl shadow-lg text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer";
                    btnSubmit.disabled = false;
                }
            } else {
                radioKeluar.checked = true;
                radioMasuk.checked = false;
                cardKeluar.classList.add('active-out');
                cardMasuk.classList.remove('active-in');
                dotKeluar.innerHTML = '<div class="w-2 h-2 rounded-full bg-rose-600"></div>';
                dotKeluar.className = 'w-3.5 h-3.5 rounded-full border-2 border-rose-600 flex items-center justify-center';
                dotMasuk.innerHTML = '';
                dotMasuk.className = 'w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex items-center justify-center';
                
                if (isEmployeeValid) {
                    btnSubmitLabel.innerText = "Ambil Foto & Absen Keluar";
                    btnSubmit.className = "w-full py-4 px-6 rounded-2xl shadow-lg text-sm font-extrabold text-white bg-rose-600 hover:bg-rose-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer";
                    btnSubmit.disabled = false;
                }
            }
        }

        // Initialize default selection
        selectType('Masuk');

        // 4. Employee Input & Real-time Live Validation (Valid vs Ditolak)
        let checkTimeout;
        identifierInput.addEventListener('input', function() {
            clearTimeout(checkTimeout);
            const val = this.value.trim();
            if (val.length === 0) {
                resetValidationState();
                return;
            }
            document.getElementById('identifier-spinner').classList.remove('hidden');
            checkTimeout = setTimeout(() => {
                checkEmployeeStatus(val);
            }, 300);
        });

        async function checkEmployeeStatus(identifier) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch("{{ url('/absen/check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ identifier: identifier })
                });
                const result = await response.json();
                document.getElementById('identifier-spinner').classList.add('hidden');

                const validBox = document.getElementById('user-valid-box');
                const invalidBox = document.getElementById('user-invalid-box');

                if (result.success) {
                    // KONDISI VALID (HIJAU ✓)
                    isEmployeeValid = true;
                    validatedIdentifier = identifier;

                    // Update UI state to Valid
                    identifierInput.classList.remove('border-rose-500', 'border-slate-200');
                    identifierInput.classList.add('border-emerald-500');

                    validationPill.className = "text-[11px] font-bold text-emerald-600";
                    validationPill.innerText = "✓ Identitas Valid";

                    invalidBox.classList.add('hidden');
                    validBox.classList.remove('hidden');

                    document.getElementById('avatar-initial').innerText = result.name.charAt(0).toUpperCase();
                    document.getElementById('user-name-text').innerText = result.name;
                    document.getElementById('user-role-text').innerText = `${result.position || result.role} • ID #${result.user_id}`;

                    // Update Attendance Journey
                    const badgeMasuk = document.getElementById('badge-masuk-done');
                    const badgeKeluar = document.getElementById('badge-keluar-done');
                    const subtextMasuk = document.getElementById('status-masuk-subtext');
                    const subtextKeluar = document.getElementById('status-keluar-subtext');
                    const allDoneBanner = document.getElementById('all-completed-banner');

                    if (result.has_masuk) {
                        badgeMasuk.classList.remove('hidden');
                        document.getElementById('time-masuk-text').innerText = `Masuk: ${result.masuk_time} WIB`;
                        subtextMasuk.innerText = `Sudah diabsen jam ${result.masuk_time}`;
                    } else {
                        badgeMasuk.classList.add('hidden');
                        subtextMasuk.innerText = "Belum absen masuk";
                    }

                    if (result.has_keluar) {
                        badgeKeluar.classList.remove('hidden');
                        document.getElementById('time-keluar-text').innerText = `Keluar: ${result.keluar_time} WIB`;
                        subtextKeluar.innerText = `Sudah diabsen jam ${result.keluar_time}`;
                    } else {
                        badgeKeluar.classList.add('hidden');
                        subtextKeluar.innerText = result.has_masuk ? "Siap absen pulang" : "Selesaikan masuk dulu";
                    }

                    // Smart Auto-Selection
                    if (result.suggested_type === 'Keluar') {
                        selectType('Keluar');
                        document.getElementById('shift-recommendation').innerText = 'Langkah 2: Absen Pulang';
                        allDoneBanner.classList.add('hidden');
                    } else if (result.suggested_type === 'Selesai') {
                        allDoneBanner.classList.remove('hidden');
                        document.getElementById('shift-recommendation').innerText = 'Presensi Lengkap';
                        btnSubmit.disabled = true;
                        btnSubmitLabel.innerText = "Presensi Hari Ini Sudah Lengkap";
                        btnSubmit.className = "w-full py-4 px-6 rounded-2xl text-sm font-bold text-slate-400 bg-slate-200 cursor-not-allowed flex items-center justify-center gap-2";
                    } else {
                        selectType('Masuk');
                        document.getElementById('shift-recommendation').innerText = 'Langkah 1: Absen Masuk';
                        allDoneBanner.classList.add('hidden');
                    }
                } else {
                    // KONDISI TIDAK VALID / DITOLAK (MERAH ✕)
                    isEmployeeValid = false;
                    validatedIdentifier = '';

                    identifierInput.classList.remove('border-emerald-500', 'border-slate-200');
                    identifierInput.classList.add('border-rose-500');

                    validationPill.className = "text-[11px] font-bold text-rose-600";
                    validationPill.innerText = "✕ Ditolak";

                    validBox.classList.add('hidden');
                    invalidBox.classList.remove('hidden');
                    document.getElementById('user-invalid-message').innerText = `Nama atau ID "${identifier}" tidak terdaftar di sistem. Absensi ditolak!`;

                    // Lock submit button
                    btnSubmit.disabled = true;
                    btnSubmitLabel.innerText = "⛔ Identitas Ditolak - Absensi Dikunci";
                    btnSubmit.className = "w-full py-4 px-6 rounded-2xl text-sm font-bold text-slate-400 bg-slate-200 cursor-not-allowed flex items-center justify-center gap-2";

                    document.getElementById('shift-recommendation').innerText = 'Identitas Ditolak';
                }
            } catch (e) {
                console.error("Check status error:", e);
                document.getElementById('identifier-spinner').classList.add('hidden');
            }
        }

        function resetValidationState() {
            isEmployeeValid = false;
            validatedIdentifier = '';

            identifierInput.classList.remove('border-emerald-500', 'border-rose-500');
            identifierInput.classList.add('border-slate-200');

            validationPill.className = "text-[11px] font-bold text-slate-400";
            validationPill.innerText = "Verifikasi Wajib";

            document.getElementById('user-valid-box').classList.add('hidden');
            document.getElementById('user-invalid-box').classList.add('hidden');
            document.getElementById('all-completed-banner').classList.add('hidden');
            document.getElementById('badge-masuk-done').classList.add('hidden');
            document.getElementById('badge-keluar-done').classList.add('hidden');
            document.getElementById('status-masuk-subtext').innerText = "Saat tiba di kedai";
            document.getElementById('status-keluar-subtext').innerText = "Saat selesai shift";
            document.getElementById('shift-recommendation').innerText = 'Pilih Masuk / Keluar';

            btnSubmit.disabled = true;
            btnSubmitLabel.innerText = "Masukkan Nama / ID Karyawan Dahulu";
            btnSubmit.className = "w-full py-4 px-6 rounded-2xl shadow-lg text-sm font-extrabold text-white bg-slate-300 cursor-not-allowed transition-all flex items-center justify-center gap-2.5";
        }

        // 5. Camera Management
        async function startCamera() {
            form.classList.remove('hidden');
            try {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: currentFacingMode },
                    audio: false
                });
                video.srcObject = stream;
                document.getElementById('camera-loading').classList.add('hidden');
            } catch (err) {
                document.getElementById('camera-loading').innerHTML = `
                    <span class="text-rose-400 font-bold">Kamera Tidak Tersedia</span>
                    <span class="text-[11px] text-slate-400">Harap izinkan akses kamera di browser Anda.</span>
                `;
            }
        }

        function flipCamera() {
            currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
            document.getElementById('camera-loading').classList.remove('hidden');
            startCamera();
        }

        // 6. Form Submit (Strict Security Check)
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!isEmployeeValid) {
                showResultModal(false, "Absensi Ditolak", "Identitas karyawan belum terverifikasi atau tidak terdaftar di sistem kedai.");
                return;
            }
            
            if (!stream) {
                showResultModal(false, "Kamera Belum Aktif", "Pastikan kamera perangkat Anda aktif dan izinkan browser mengakses kamera.");
                return;
            }

            btnSubmit.disabled = true;
            const originalLabel = btnSubmitLabel.innerText;
            btnSubmitLabel.innerText = "Mengambil Foto & Memproses...";

            // Snap photo
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const photoData = canvas.toDataURL('image/png');

            const identifier = identifierInput.value;
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
                        type: selectedAttendanceType,
                        latitude: userLat,
                        longitude: userLon,
                        photo: photoData
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    let msg = result.message;
                    if (result.status === 'luar_zona') {
                        msg += " (Peringatan: Tercatat di Luar Zona)";
                    }
                    showResultModal(true, `Berhasil Absen ${selectedAttendanceType}!`, msg);
                    
                    // Refresh employee status
                    checkEmployeeStatus(identifier);
                } else {
                    showResultModal(false, "Gagal Melakukan Absensi", result.message || "Terjadi kendala saat memproses presensi.");
                }
            } catch (error) {
                showResultModal(false, "Kesalahan Jaringan", "Tidak dapat terhubung ke server. Periksa koneksi internet Anda.");
            }

            btnSubmit.disabled = false;
            btnSubmitLabel.innerText = originalLabel;
        });

        // 7. In-App Modal Notifications
        function showResultModal(isSuccess, title, message) {
            const modal = document.getElementById('result-modal');
            const iconContainer = document.getElementById('modal-icon-container');
            const icon = document.getElementById('modal-icon');
            const titleEl = document.getElementById('modal-title');
            const msgEl = document.getElementById('modal-message');

            titleEl.innerText = title;
            msgEl.innerText = message;

            if (isSuccess) {
                iconContainer.className = "w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4";
                icon.innerHTML = '<polyline points="20 6 9 17 4 12" stroke-width="3"/>';
            } else {
                iconContainer.className = "w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4";
                icon.innerHTML = '<circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResultModal() {
            const modal = document.getElementById('result-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Location helpers
        function showLocationError(msg) {
            locationStatus.className = "p-4 rounded-2xl flex items-start gap-3.5 bg-rose-50 text-rose-900 border border-rose-200/80";
            locationStatus.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-bold">Akses Lokasi Ditolak</p>
                    <p class="text-xs text-rose-700/90 mt-0.5">${msg}</p>
                </div>
            `;
        }

        function showLocationSuccess(msg) {
            locationStatus.className = "p-4 rounded-2xl flex items-start gap-3.5 bg-emerald-50 text-emerald-900 border border-emerald-200/80";
            locationStatus.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <div class="text-sm">
                    <p class="font-bold">Lokasi Kedai Sesuai</p>
                    <p class="text-xs text-emerald-700/90 mt-0.5">${msg}</p>
                </div>
            `;
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
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

@extends('admin.layouts.app')

@section('title', 'Pengaturan Kedai')
@section('header', 'Pengaturan Kedai')

@section('content')
<div class="mb-6">
    <p class="text-gray-600">Atur profil dan titik koordinat GPS Kedai untuk keperluan Absensi Karyawan.</p>
</div>

@if (session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
    <p>{{ session('success') }}</p>
</div>
@endif

<form action="{{ route('admin.kedai.pengaturan.update') }}" method="POST">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="col-span-2 md:col-span-1">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kedai</label>
                <input type="text" name="name" id="name" value="{{ old('name', $kedai->name ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" required>
            </div>

            <div class="col-span-2">
                <hr class="my-4">
                <h3 class="text-lg font-bold mb-2">Pengaturan Zona Absensi (Geofencing)</h3>
                <p class="text-sm text-gray-500 mb-4">Masukkan titik pusat koordinat kedai dan radius (dalam meter) agar absen valid hanya bisa dilakukan di area tersebut.</p>
            </div>

            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude Kedai</label>
                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $kedai->latitude ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Contoh: -6.9174639">
            </div>

            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude Kedai</label>
                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $kedai->longitude ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Contoh: 107.6191228">
            </div>

            <div>
                <label for="radius_meter" class="block text-sm font-medium text-gray-700">Radius Absensi (Meter)</label>
                <input type="number" name="radius_meter" id="radius_meter" value="{{ old('radius_meter', $kedai->radius_meter ?? 50) }}" min="10" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" required>
            </div>

        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition">
            Simpan Pengaturan
        </button>
    </div>
</form>
@endsection

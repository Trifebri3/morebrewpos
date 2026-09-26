<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function cek(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
            'subtotal' => 'required|numeric'
        ]);

        $kedai_id = auth()->user()->kedai_id;
        $voucher = Voucher::where('kode', $request->kode)
                          ->where('kedai_id', $kedai_id)
                          ->where('status', true)
                          ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher tidak ditemukan atau tidak aktif.']);
        }

        // Cek Kuota
        if ($voucher->kuota !== null && $voucher->kuota <= 0) {
            return response()->json(['success' => false, 'message' => 'Kuota voucher sudah habis.']);
        }

        // Cek Minimal Belanja
        if ($voucher->minimal_belanja && $request->subtotal < $voucher->minimal_belanja) {
            return response()->json(['success' => false, 'message' => 'Minimal belanja untuk voucher ini adalah Rp ' . number_format($voucher->minimal_belanja, 0, ',', '.')]);
        }

        $now = Carbon::now();

        // Cek Tanggal
        if ($voucher->tanggal_mulai && $now->startOfDay()->lt(Carbon::parse($voucher->tanggal_mulai)->startOfDay())) {
            return response()->json(['success' => false, 'message' => 'Voucher belum berlaku.']);
        }

        if ($voucher->tanggal_selesai && $now->startOfDay()->gt(Carbon::parse($voucher->tanggal_selesai)->startOfDay())) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah kadaluarsa.']);
        }

        // Cek Hari
        if ($voucher->hari_berlaku && count($voucher->hari_berlaku) > 0) {
            $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hariIni = $hariIndo[$now->dayOfWeek];
            if (!in_array($hariIni, $voucher->hari_berlaku)) {
                return response()->json(['success' => false, 'message' => 'Voucher tidak berlaku untuk hari ini (' . $hariIni . ').']);
            }
        }

        // Cek Jam
        if ($voucher->jam_mulai) {
            $jamMulai = Carbon::parse($voucher->jam_mulai)->format('H:i');
            $jamIni = $now->format('H:i');
            if ($jamIni < $jamMulai) {
                return response()->json(['success' => false, 'message' => 'Voucher hanya berlaku mulai jam ' . $jamMulai]);
            }
        }

        if ($voucher->jam_selesai) {
            $jamSelesai = Carbon::parse($voucher->jam_selesai)->format('H:i');
            $jamIni = $now->format('H:i');
            if ($jamIni > $jamSelesai) {
                return response()->json(['success' => false, 'message' => 'Voucher hanya berlaku sampai jam ' . $jamSelesai]);
            }
        }

        // Hitung Diskon
        $discountAmount = 0;
        if ($voucher->tipe_diskon == 'persen') {
            $discountAmount = $request->subtotal * ($voucher->nilai_diskon / 100);
        } else {
            $discountAmount = $voucher->nilai_diskon;
        }

        // Pastikan diskon tidak lebih dari subtotal
        if ($discountAmount > $request->subtotal) {
            $discountAmount = $request->subtotal;
        }

        return response()->json([
            'success' => true, 
            'message' => 'Voucher ' . $voucher->nama . ' berhasil diterapkan!',
            'discount_amount' => $discountAmount,
            'voucher_id' => $voucher->id
        ]);
    }
}

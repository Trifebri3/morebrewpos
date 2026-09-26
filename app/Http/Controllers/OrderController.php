<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $hash = $request->query('meja');
        if (!$hash) abort(404, 'Kode meja tidak valid.');

        $meja = Meja::where('qr_hash', $hash)->first();
        if (!$meja) abort(404, 'Meja tidak ditemukan.');

        $products = Produk::where('is_active', true)
            ->select('id', 'name', 'price', 'category', 'stock', 'image')
            ->get();

        return view('order.index', compact('meja', 'products', 'hash'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'hash' => 'required|string',
            'customerName' => 'required|string|max:100',
            'customerPhone' => 'nullable|string|max:20',
            'cart' => 'required|array',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $meja = Meja::where('qr_hash', $data['hash'])->first();
        if (!$meja) {
            return response()->json(['success' => false, 'message' => 'Meja tidak valid.'], 400);
        }

        // Susun nama pelanggan dengan nomor HP (opsional) dan meja
        $phoneStr = !empty($data['customerPhone']) ? ' - ' . $data['customerPhone'] : '';
        $customerFullName = $data['customerName'] . $phoneStr . ' (' . $meja->name . ')';

        // Simpan transaksi
        $invoice = date('ymd') . rand(1000, 9999);
        
        $transaksi = Transaksi::create([
            'invoice_number' => $invoice,
            'customer_name'  => $customerFullName,
            'order_type'     => 'dine_in',
            'items'          => $data['cart'],
            'subtotal'       => $data['subtotal'],
            'discount_amount'=> 0,
            'tax'            => $data['tax'],
            'total'          => $data['total'],
            'payment_method' => 'pending', // Belum dibayar
            'amount_paid'    => 0,
        ]);

        return response()->json([
            'success' => true,
            'invoice' => $invoice,
            'message' => 'Pesanan berhasil dibuat. Makanan akan diantar, silakan bayar nanti ke kasir.'
        ]);
    }

    public function track($invoice)
    {
        $transaksi = Transaksi::where('invoice_number', $invoice)->first();
        if (!$transaksi) {
            abort(404, 'Pesanan tidak ditemukan.');
        }
        return view('order.track', compact('transaksi'));
    }
}

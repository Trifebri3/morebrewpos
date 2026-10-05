<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function invoice(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $dashboardData = $dashboardService->getDashboardData();
        $invoices = \App\Models\Transaksi::orderBy('created_at', 'desc')->get();
        return view('kasir.invoice', array_merge($dashboardData, compact('invoices')));
    }
    
    public function cetakStruk(\Illuminate\Http\Request $request)
    {
        $data = json_decode($request->input('data'), true);

        $cName = $data['customerName'] ?? 'Pelanggan Walk-In';
        $cPhone = !empty($data['customerPhone']) ? trim($data['customerPhone']) : '';
        if (!empty($cPhone) && !str_contains($cName, $cPhone)) {
            $cName .= ' (' . $cPhone . ')';
        }

        // Simpan ke database
        \App\Models\Transaksi::create([
            'invoice_number' => preg_replace('/^INV-?/i', '', (string)($data['invoiceNumber'] ?? (date('ymd') . rand(1000, 9999)))),
            'customer_name'  => $cName,
            'order_type'     => $data['orderType'] ?? 'dine_in',
            'items'          => $data['cart'] ?? [],
            'subtotal'       => $data['subtotal'] ?? 0,
            'discount_amount'=> $data['discountAmount'] ?? 0,
            'tax'            => $data['tax'] ?? 0,
            'total'          => $data['total'] ?? 0,
            'payment_method' => $data['paymentMethod'] ?? 'cash',
            'amount_paid'    => $data['amountPaid'] ?? 0,
        ]);

        // Kurangi stok produk
        if (!empty($data['cart']) && is_array($data['cart'])) {
            foreach ($data['cart'] as $item) {
                if (isset($item['id']) && isset($item['qty'])) {
                    $produk = \App\Models\Produk::find($item['id']);
                    if ($produk) {
                        $produk->stock -= $item['qty'];
                        $produk->save();
                    }
                }
            }
        }

        // Kurangi kuota voucher jika ada dan tambah statistik terpakai
        if (!empty($data['voucherId'])) {
            $voucher = \App\Models\Voucher::find($data['voucherId']);
            if ($voucher) {
                if ($voucher->kuota !== null && $voucher->kuota > 0) {
                    $voucher->kuota -= 1;
                }
                $voucher->terpakai += 1;
                $voucher->save();
            }
        }

        return view('kasir.pdf', compact('data'));
    }

    public function cetakUlang($id)
    {
        $transaksi = \App\Models\Transaksi::findOrFail($id);
        
        $data = [
            'invoiceNumber'  => $transaksi->invoice_number,
            'customerName'   => $transaksi->customer_name,
            'orderType'      => $transaksi->order_type,
            'cart'           => is_array($transaksi->items) ? $transaksi->items : json_decode($transaksi->items, true),
            'subtotal'       => $transaksi->subtotal,
            'discountAmount' => $transaksi->discount_amount,
            'tax'            => $transaksi->tax,
            'total'          => $transaksi->total,
            'paymentMethod'  => $transaksi->payment_method,
            'amountPaid'     => $transaksi->amount_paid,
            'date'           => $transaksi->created_at->format('d/m/Y H:i'),
        ];

        return view('kasir.pdf', compact('data'));
    }
}

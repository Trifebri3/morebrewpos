<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    protected function getViewData($title = 'Voucher Promo')
    {
        $dashboardService = app(\App\Services\Admin\DashboardService::class);
        $data = $dashboardService->getDashboardData();
        $data['title'] = $title;
        return $data;
    }

    public function index()
    {
        $data = $this->getViewData('Daftar Voucher');
        $vouchers = Voucher::where('kedai_id', auth()->user()->kedai_id)->latest()->get();
        return view('admin.voucher.index', compact('data', 'vouchers'));
    }

    public function create()
    {
        $data = $this->getViewData('Tambah Voucher');
        return view('admin.voucher.create', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:vouchers,kode',
            'nama' => 'required|string|max:100',
            'tipe_diskon' => 'required|in:persen,nominal',
            'nilai_diskon' => 'required|numeric|min:0',
            'minimal_belanja' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'hari_berlaku' => 'nullable|array',
            'hari_berlaku.*' => 'string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'kuota' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $validated['kedai_id'] = auth()->user()->kedai_id;
        $validated['status'] = $request->has('status');

        Voucher::create($validated);
        return redirect()->route('admin.penjualan.voucher.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        if ($voucher->kedai_id != auth()->user()->kedai_id) abort(403);
        $data = $this->getViewData('Edit Voucher');
        return view('admin.voucher.edit', compact('data', 'voucher'));
    }

    public function show(Voucher $voucher)
    {
        if ($voucher->kedai_id != auth()->user()->kedai_id) abort(403);
        $data = $this->getViewData('Detail Penggunaan Voucher');
        $transaksis = $voucher->transaksis()->latest()->get();
        return view('admin.voucher.show', compact('data', 'voucher', 'transaksis'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        if ($voucher->kedai_id != auth()->user()->kedai_id) abort(403);

        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:vouchers,kode,' . $voucher->id,
            'nama' => 'required|string|max:100',
            'tipe_diskon' => 'required|in:persen,nominal',
            'nilai_diskon' => 'required|numeric|min:0',
            'minimal_belanja' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'hari_berlaku' => 'nullable|array',
            'hari_berlaku.*' => 'string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'kuota' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        if (!$request->has('hari_berlaku')) {
            $validated['hari_berlaku'] = null;
        }

        $validated['status'] = $request->has('status');

        $voucher->update($validated);
        return redirect()->route('admin.penjualan.voucher.index')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        if ($voucher->kedai_id != auth()->user()->kedai_id) abort(403);
        $voucher->delete();
        return redirect()->route('admin.penjualan.voucher.index')->with('success', 'Voucher berhasil dihapus.');
    }
}

<?php

use App\Models\Kedai;
use App\Models\User;
use App\Models\Voucher;

beforeEach(function () {
    $this->kedai = Kedai::first() ?? Kedai::create(['name' => 'MoreBrew Test', 'is_active' => true]);
    $this->admin = User::firstOrCreate(
        ['email' => 'admin_test@example.com'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'kedai_id' => $this->kedai->id]
    );
    $this->kasir = User::firstOrCreate(
        ['email' => 'kasir_test@example.com'],
        ['name' => 'Kasir Test', 'password' => bcrypt('password'), 'role' => 'kasir', 'kedai_id' => $this->kedai->id]
    );
});

test('admin can access all laporan pages and export excel', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.laporan.penjualan'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.penjualan.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.pengeluaran'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.pengeluaran.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.produk'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.produk.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.kas'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.kas.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.shift'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.shift.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.staff'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.laporan.staff.export'));
    $response->assertStatus(200);
});

test('admin can access absensi recap and export excel', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.staff.absensi'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.staff.absensi.export'));
    $response->assertStatus(200);
});

test('admin can export voucher usage history', function () {
    $voucher = Voucher::firstOrCreate(
        ['kode' => 'TESTVOUCHER', 'kedai_id' => $this->kedai->id],
        ['nama' => 'Test Voucher', 'tipe_diskon' => 'nominal', 'nilai_diskon' => 10000, 'status' => true]
    );

    $response = $this->actingAs($this->admin)->get(route('admin.penjualan.voucher.export', $voucher));
    $response->assertStatus(200);
});

test('kasir can access laporan pages and export excel', function () {
    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.penjualan'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.penjualan.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.pengeluaran'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.pengeluaran.export'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.shift'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->kasir)->get(route('kasir.laporan.shift.export'));
    $response->assertStatus(200);
});

test('admin can access staff karyawan and shift pages', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.staff.karyawan.index'));
    $response->assertStatus(200);

    $response = $this->actingAs($this->admin)->get(route('admin.staff.shift'));
    $response->assertStatus(200);
});

test('kasir can access absensi clock in out kiosk', function () {
    $response = $this->actingAs($this->kasir)->get(route('kasir.absensi'));
    $response->assertStatus(200);
});

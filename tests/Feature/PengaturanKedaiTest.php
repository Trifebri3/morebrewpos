<?php

use App\Models\Kedai;
use App\Models\User;

beforeEach(function () {
    $this->kedai = Kedai::firstOrCreate(
        ['name' => 'MOREBREWW'],
        [
            'address' => 'Jl. Sasmitatmaja No.6, Paledang, Kec. Lengkong, Kota Bandung',
            'phone' => '08123456789',
            'is_active' => true,
            'is_tax_enabled' => true,
            'tax_percentage' => 11.00,
            'tax_name' => 'PB1 (Pajak Restoran)',
            'budget_harian' => 1000000,
            'receipt_header' => 'something, between home and everywhere',
            'receipt_footer' => 'Silakan datang kembali!',
            'wifi_ssid' => 'moreandmore',
            'wifi_password' => 'bolehlihatsenyumnya?',
            'instagram' => '@morebrewcoffee',
            'radius_meter' => 50,
        ]
    );

    $this->admin = User::firstOrCreate(
        ['email' => 'admin_settings_test@example.com'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'kedai_id' => $this->kedai->id]
    );

    $this->kasir = User::firstOrCreate(
        ['email' => 'kasir_settings_test@example.com'],
        ['name' => 'Kasir Test', 'password' => bcrypt('password'), 'role' => 'kasir', 'kedai_id' => $this->kedai->id]
    );
});

test('admin and kasir can access pengaturan page', function () {
    $adminRes = $this->actingAs($this->admin)->get(route('admin.kedai.pengaturan'));
    $adminRes->assertStatus(200);
    $adminRes->assertSee('Pajak Transaksi (PB1 / PPN)');
    $adminRes->assertSee('Aktivasi Pajak Transaksi');

    $kasirRes = $this->actingAs($this->kasir)->get(route('kasir.pengaturan'));
    $kasirRes->assertStatus(200);
    $kasirRes->assertSee('Pajak Transaksi (PB1 / PPN)');
});

test('admin can update tax settings and store profile', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.kedai.pengaturan.update'), [
        'name' => 'MOREBREW SPECIALTY',
        'phone' => '08999999999',
        'address' => 'Bandung, Indonesia',
        'tax_percentage' => 10.00,
        'tax_name' => 'PB1 Restoran',
        'is_tax_enabled' => '1',
        'budget_harian' => 1500000,
        'receipt_header' => 'Brewed with passion',
        'receipt_footer' => 'Sampai jumpa lagi!',
        'wifi_ssid' => 'MoreBrew_VIP',
        'wifi_password' => 'kopiindonesia',
        'instagram' => '@morebrewid',
        'latitude' => -6.9214,
        'longitude' => 107.6166,
        'radius_meter' => 75,
    ]);

    $response->assertRedirect();
    $this->kedai->refresh();

    expect($this->kedai->name)->toBe('MOREBREW SPECIALTY')
        ->and($this->kedai->is_tax_enabled)->toBeTrue()
        ->and((float) $this->kedai->tax_percentage)->toBe(10.00)
        ->and($this->kedai->wifi_password)->toBe('kopiindonesia');
});

test('admin can disable tax completely', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.kedai.pengaturan.update'), [
        'name' => 'MOREBREW SPECIALTY',
        'phone' => '08999999999',
        'address' => 'Bandung, Indonesia',
        'tax_percentage' => 0,
        'tax_name' => 'PB1',
        'radius_meter' => 50,
        // is_tax_enabled omitted -> false
    ]);

    $response->assertRedirect();
    $this->kedai->refresh();

    expect($this->kedai->is_tax_enabled)->toBeFalse();
});

test('api returns store settings and respects tax status', function () {
    $response = $this->getJson(route('api.settings.get'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'name',
                'isTaxEnabled',
                'taxPercentage',
                'taxName',
                'wifiSsid',
                'wifiPassword',
            ],
        ]);
});

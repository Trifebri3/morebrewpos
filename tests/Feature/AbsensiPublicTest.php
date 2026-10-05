<?php

use App\Models\Kedai;
use App\Models\User;

beforeEach(function () {
    $this->kedai = Kedai::first() ?? Kedai::create([
        'name' => 'Kedai MoreBrew Test',
        'is_active' => true,
        'is_link_absen_enabled' => true,
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius_meter' => 500,
    ]);

    $this->kedai->update(['is_link_absen_enabled' => true]);

    $this->bila = User::firstOrCreate(
        ['email' => 'bila_test@no-login.com'],
        [
            'name' => 'Bila',
            'password' => bcrypt('secret'),
            'role' => 'staff',
            'position' => 'Barista',
            'kedai_id' => $this->kedai->id,
        ]
    );

    $this->ajay = User::firstOrCreate(
        ['email' => 'ajay_test@no-login.com'],
        [
            'name' => 'ajay',
            'password' => bcrypt('secret'),
            'role' => 'staff',
            'position' => 'Kitchen',
            'kedai_id' => $this->kedai->id,
        ]
    );
});

test('public attendance page displays staff employees including Bila and ajay', function () {
    $response = $this->get(route('absen'));

    $response->assertStatus(200);
    $response->assertSee('Bila');
    $response->assertSee('ajay');
    $response->assertSee('Portal Presensi Staf');
});

test('live check accepts valid employee by exact or lowercase name', function () {
    $response = $this->postJson(route('absen.check'), [
        'identifier' => 'Bila',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'name' => 'Bila',
            'user_id' => $this->bila->id,
            'suggested_type' => 'Masuk',
        ]);

    $responseLower = $this->postJson(route('absen.check'), [
        'identifier' => 'ajay',
    ]);

    $responseLower->assertStatus(200)
        ->assertJson([
            'success' => true,
            'name' => 'ajay',
            'user_id' => $this->ajay->id,
        ]);
});

test('live check accepts valid employee by numeric or zero padded ID', function () {
    $response = $this->postJson(route('absen.check'), [
        'identifier' => (string) $this->bila->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'name' => 'Bila',
            'user_id' => $this->bila->id,
        ]);

    $paddedId = '00'.$this->bila->id;
    $responsePadded = $this->postJson(route('absen.check'), [
        'identifier' => $paddedId,
    ]);

    $responsePadded->assertStatus(200)
        ->assertJson([
            'success' => true,
            'name' => 'Bila',
            'user_id' => $this->bila->id,
        ]);
});

test('live check strictly rejects invalid input like 00 or unregistered names', function () {
    $response00 = $this->postJson(route('absen.check'), [
        'identifier' => '00',
    ]);

    $response00->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

    $responseRandom = $this->postJson(route('absen.check'), [
        'identifier' => 'PegawaiTidakAda999',
    ]);

    $responseRandom->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);
});

test('submitting attendance with invalid employee identifier is rejected', function () {
    $response = $this->postJson(route('absen.submit'), [
        'identifier' => '00',
        'type' => 'Masuk',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Identitas Karyawan tidak terdaftar. Absensi ditolak!',
        ]);
});

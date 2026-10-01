<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kedais', function (Blueprint $table) {
            $table->string('wifi_ssid')->nullable()->after('phone');
            $table->string('wifi_password')->nullable()->after('wifi_ssid');
            $table->string('instagram')->nullable()->after('wifi_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kedais', function (Blueprint $table) {
            $table->dropColumn(['wifi_ssid', 'wifi_password', 'instagram']);
        });
    }
};

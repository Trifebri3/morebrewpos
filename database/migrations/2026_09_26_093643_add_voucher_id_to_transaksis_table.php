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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->nullable()->after('discount_amount');
            // Jika Anda ingin menambahkan foreign key (opsional, tergantung database setup Anda):
            // $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // $table->dropForeign(['voucher_id']);
            $table->dropColumn('voucher_id');
        });
    }
};

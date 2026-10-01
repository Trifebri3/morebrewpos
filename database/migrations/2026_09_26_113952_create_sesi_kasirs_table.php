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
        Schema::create('sesi_kasirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('waktu_buka')->nullable();
            $table->timestamp('waktu_tutup')->nullable();
            $table->decimal('modal_awal', 15, 2)->default(0);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('uang_fisik', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0);
            $table->string('status')->default('Buka'); // Buka, Tutup
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_kasirs');
    }
};

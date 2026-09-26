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
            $table->boolean('is_link_absen_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kedais', function (Blueprint $table) {
            $table->dropColumn('is_link_absen_enabled');
        });
    }
};

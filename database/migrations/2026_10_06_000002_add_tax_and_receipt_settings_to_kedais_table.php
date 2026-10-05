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
            if (!Schema::hasColumn('kedais', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(11.00)->after('is_active');
            }
            if (!Schema::hasColumn('kedais', 'is_tax_enabled')) {
                $table->boolean('is_tax_enabled')->default(true)->after('tax_percentage');
            }
            if (!Schema::hasColumn('kedais', 'tax_name')) {
                $table->string('tax_name')->default('PB1 (Pajak Restoran)')->after('is_tax_enabled');
            }
            if (!Schema::hasColumn('kedais', 'receipt_header')) {
                $table->string('receipt_header')->nullable()->default('something, between home and everywhere')->after('tax_name');
            }
            if (!Schema::hasColumn('kedais', 'receipt_footer')) {
                $table->string('receipt_footer')->nullable()->default('Silakan datang kembali!')->after('receipt_header');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kedais', function (Blueprint $table) {
            $table->dropColumn([
                'tax_percentage',
                'is_tax_enabled',
                'tax_name',
                'receipt_header',
                'receipt_footer',
            ]);
        });
    }
};

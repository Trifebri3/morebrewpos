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
        Schema::table('sesi_kasirs', function (Blueprint $table) {
            if (!Schema::hasColumn('sesi_kasirs', 'session_number')) {
                $table->string('session_number')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'previous_session_id')) {
                $table->unsignedBigInteger('previous_session_id')->nullable()->after('session_number');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'total_cash_sales')) {
                $table->decimal('total_cash_sales', 15, 2)->default(0)->after('total_pendapatan');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'total_non_cash_sales')) {
                $table->decimal('total_non_cash_sales', 15, 2)->default(0)->after('total_cash_sales');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'cash_in')) {
                $table->decimal('cash_in', 15, 2)->default(0)->after('total_non_cash_sales');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'cash_out')) {
                $table->decimal('cash_out', 15, 2)->default(0)->after('cash_in');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'cash_expense')) {
                $table->decimal('cash_expense', 15, 2)->default(0)->after('cash_out');
            }
            if (!Schema::hasColumn('sesi_kasirs', 'expected_balance')) {
                $table->decimal('expected_balance', 15, 2)->default(0)->after('cash_expense');
            }
        });

        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('transaksis', 'sesi_kasir_id')) {
                $table->unsignedBigInteger('sesi_kasir_id')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_kasirs', function (Blueprint $table) {
            $table->dropColumn([
                'session_number',
                'previous_session_id',
                'total_cash_sales',
                'total_non_cash_sales',
                'cash_in',
                'cash_out',
                'cash_expense',
                'expected_balance',
            ]);
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'sesi_kasir_id']);
        });
    }
};

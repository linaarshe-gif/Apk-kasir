<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** * Run the migrations. */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'uang_bayar')) {
                $table->integer('uang_bayar')->nullable()->after('total_harga');
            }
            if (!Schema::hasColumn('transaksis', 'kembalian')) {
                $table->integer('kembalian')->nullable()->after('uang_bayar');
            }
        });
    }

    /** * Reverse the migrations. */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'kembalian')) {
                $table->dropColumn('kembalian');
            }
            if (Schema::hasColumn('transaksis', 'uang_bayar')) {
                $table->dropColumn('uang_bayar');
            }
        });
    }
};

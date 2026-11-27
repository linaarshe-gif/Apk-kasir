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
            if (Schema::hasColumn('transaksis', 'produk_id')) {
                $table->dropForeign(['produk_id']);
                $table->dropColumn('produk_id');
            }
            if (Schema::hasColumn('transaksis', 'qty')) {
                $table->dropColumn('qty');
            }
            if (Schema::hasColumn('transaksis', 'total_harga')) {
                $table->dropColumn('total_harga');
            }
        });
    }

    /** * Reverse the migrations. */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'produk_id')) {
                $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade')->after('user_id');
            }
            if (!Schema::hasColumn('transaksis', 'qty')) {
                $table->integer('qty')->after('produk_id');
            }
            if (!Schema::hasColumn('transaksis', 'total_harga')) {
                $table->integer('total_harga')->after('qty');
            }
        });
    }
};
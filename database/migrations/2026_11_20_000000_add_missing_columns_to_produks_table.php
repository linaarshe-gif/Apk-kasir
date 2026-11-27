<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** * Run the migrations. */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            if (!Schema::hasColumn('produks', 'kategori_id')) {
                $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->onDelete('cascade')->after('name');
            }
            if (!Schema::hasColumn('produks', 'harga')) {
                $table->integer('harga')->after('kategori_id');
            }
            if (!Schema::hasColumn('produks', 'stok')) {
                $table->integer('stok')->after('harga');
            }
            if (!Schema::hasColumn('produks', 'gambar')) {
                $table->string('gambar')->nullable()->after('stok');
            }
        });
    }

    /** * Reverse the migrations. */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            if (Schema::hasColumn('produks', 'gambar')) {
                $table->dropColumn('gambar');
            }
            if (Schema::hasColumn('produks', 'stok')) {
                $table->dropColumn('stok');
            }
            if (Schema::hasColumn('produks', 'harga')) {
                $table->dropColumn('harga');
            }
            if (Schema::hasColumn('produks', 'kategori_id')) {
                $table->dropForeign(['kategori_id']);
                $table->dropColumn('kategori_id');
            }
        });
    }
};

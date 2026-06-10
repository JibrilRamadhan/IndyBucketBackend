<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom features (JSON) ke tabel products.
     * Menyimpan daftar poin yang ditampilkan sebagai bullet di detail produk.
     * Contoh: ["Wrapping premium + pita eksklusif", "Free kartu ucapan handwritten"]
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('features')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};

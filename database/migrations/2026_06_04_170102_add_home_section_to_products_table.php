<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Menentukan tampil di section mana di halaman Home
            // null       = tidak ditampilkan di section khusus
            // discover   = Koleksi Signature Kami
            // fresh      = Hadiah untuk Setiap Gaya  
            // bestseller = Produk Terlaris Kami (sudah ada via label, tapi bisa override)
            $table->enum('home_section', ['discover', 'fresh', 'bestseller'])
                  ->nullable()
                  ->default(null)
                  ->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('home_section');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lowongans', function (Blueprint $table) {
            // Tambah kolom kategori setelah nama_posisi
            $table->enum('kategori', ['Dosen', 'Tenaga Kependidikan'])
                  ->default('Dosen')
                  ->after('nama_posisi');

            // Jadikan prodi_id nullable (Tendik tidak terikat prodi)
            $table->foreignId('prodi_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lowongans', function (Blueprint $table) {
            $table->dropColumn('kategori');
            $table->foreignId('prodi_id')->nullable(false)->change();
        });
    }
};

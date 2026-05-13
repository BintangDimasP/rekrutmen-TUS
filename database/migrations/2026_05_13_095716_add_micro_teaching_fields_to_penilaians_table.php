<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->integer('kategori_4')->nullable()->after('kategori_3');
            $table->integer('kategori_5')->nullable()->after('kategori_4');
            $table->string('rekomendasi')->nullable()->after('catatan');
            $table->string('prodi_tujuan')->nullable()->after('rekomendasi');
            $table->string('kelompok_keahlian')->nullable()->after('prodi_tujuan');
            $table->string('bidang_keahlian')->nullable()->after('kelompok_keahlian');
        });
    }

    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn(['kategori_4', 'kategori_5', 'rekomendasi', 'prodi_tujuan', 'kelompok_keahlian', 'bidang_keahlian']);
        });
    }
};

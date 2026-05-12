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
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->dropUnique('unique_pelamar_sesi');
            $table->unique(['tanggal', 'pelamar_id', 'penguji_id', 'tipe_seleksi', 'sesi'], 'unique_jadwal_pelamar_penguji_sesi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->dropUnique('unique_jadwal_pelamar_penguji_sesi');
            $table->unique(['tanggal', 'pelamar_id', 'tipe_seleksi', 'sesi'], 'unique_pelamar_sesi');
        });
    }
};

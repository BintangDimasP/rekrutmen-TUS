<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_seleksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('lowongan_id')->constrained('lowongans')->onDelete('cascade');
            $table->foreignId('pelamar_id')->constrained('pelamars')->onDelete('cascade');
            $table->foreignId('penguji_id')->constrained('dosens')->onDelete('cascade');
            $table->enum('tipe_seleksi', ['tahap1', 'tahap2']);
            $table->tinyInteger('sesi'); // 1-4 tahap1, 1-3 tahap2
            $table->timestamps();

            // Index cepat cek penguji/pelamar di tanggal tertentu
            $table->index(['tanggal', 'penguji_id']);
            $table->index(['tanggal', 'pelamar_id']);

            // 1 pelamar tidak boleh di sesi+tipe yang sama di hari yang sama
            $table->unique(['tanggal', 'pelamar_id', 'tipe_seleksi', 'sesi'], 'unique_pelamar_sesi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_seleksis');
    }
};


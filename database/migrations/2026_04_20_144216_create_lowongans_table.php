<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posisi');
            $table->foreignId('prodi_id')->constrained('prodis')->cascadeOnDelete();

            // Persyaratan
            $table->enum('jenjang_minimal', ['D3', 'S1', 'S2', 'S3']);
            $table->decimal('minimal_ipk', 3, 2)->default(3.00);
            $table->string('prodi_prioritas')->nullable();    // Contoh: "Sistem Informasi"
            $table->string('skill_dibutuhkan')->nullable();   // Contoh: "IoT"

            // Kuota & Waktu
            $table->unsignedInteger('kuota');
            $table->date('tanggal_tutup');

            // Deskripsi (teks bebas, pre-filled lewat seeder/default)
            $table->text('deskripsi')->nullable();

            // Status
            $table->enum('status', ['aktif', 'ditutup', 'draft'])->default('aktif');

            $table->timestamps();
        });

        // Tambahkan foreign key ke tabel lamarans yang sudah ada
        Schema::table('lamarans', function (Blueprint $table) {
            $table->foreign('lowongan_id')->references('id')->on('lowongans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropForeign(['lowongan_id']);
        });
        Schema::dropIfExists('lowongans');
    }
};

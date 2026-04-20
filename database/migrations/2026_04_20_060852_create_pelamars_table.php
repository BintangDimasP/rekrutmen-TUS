<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Step 2 — Data Diri
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('no_telepon', 20);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');

            // Step 3 — Riwayat Pendidikan
            $table->enum('jenjang', ['S1', 'S2', 'S3'])->nullable();
            $table->string('institusi')->nullable();
            $table->string('prodi_pendidikan')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('file_ijazah')->nullable();
            $table->string('file_transkrip')->nullable();

            // Step 4 — Dokumen Pendukung
            $table->string('file_cv')->nullable();
            $table->string('file_pas_foto')->nullable();
            $table->string('file_ktp')->nullable();
            $table->enum('kategori_sertifikat', ['kompetensi', 'keahlian_khusus'])->nullable();
            $table->string('file_sertifikat')->nullable();
            $table->enum('jenis_tes_bahasa', ['PBT', 'TOEFL_ITP', 'EPrT', 'CBT', 'IBT', 'IELTS', 'AcEPT'])->nullable();
            $table->decimal('skor_bahasa', 5, 2)->nullable();
            $table->date('tanggal_tes_bahasa')->nullable();
            $table->string('file_sertifikat_bahasa')->nullable();

            // Step 5 — Riwayat Akademik
            $table->string('nidn')->nullable();
            $table->string('homebase')->nullable();
            $table->enum('jabatan_akademik', ['asisten_ahli', 'lektor', 'lektor_kepala', 'profesor'])->nullable();
            $table->text('minat_riset')->nullable();
            $table->unsignedSmallInteger('h_index')->nullable();
            $table->string('file_kartu_dosen')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelamars');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelamar_id')->constrained('pelamars')->cascadeOnDelete();
            $table->unsignedBigInteger('lowongan_id');  // FK ditambah setelah tabel lowongans dibuat
            $table->enum('status', ['menunggu', 'proses', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['pelamar_id', 'lowongan_id']);
            $table->index('lowongan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamarans');
    }
};

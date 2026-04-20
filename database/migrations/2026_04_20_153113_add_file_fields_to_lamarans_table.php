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
        Schema::table('lamarans', function (Blueprint $table) {
            $table->string('file_surat_lamaran')->nullable()->after('lowongan_id');
            $table->string('file_berkas_pendukung')->nullable()->after('file_surat_lamaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropColumn(['file_surat_lamaran', 'file_berkas_pendukung']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan semua kolom selain nama & user_id sebagai nullable
     * agar import minimal (hanya email + nama) dapat berjalan.
     */
    public function up(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->text('nik')->nullable()->change();
            $table->string('tempat_lahir')->nullable()->change();
            $table->date('tanggal_lahir')->nullable()->change();
            $table->text('no_telepon')->nullable()->change();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->change();

            // kolom 'alamat' mungkin sudah diganti / tidak ada di versi terbaru
            // tapi jika masih ada, jadikan nullable juga
            if (Schema::hasColumn('pelamars', 'alamat')) {
                $table->text('alamat')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->text('nik')->nullable(false)->change();
            $table->string('tempat_lahir')->nullable(false)->change();
            $table->date('tanggal_lahir')->nullable(false)->change();
            $table->text('no_telepon')->nullable(false)->change();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(false)->change();
        });
    }
};

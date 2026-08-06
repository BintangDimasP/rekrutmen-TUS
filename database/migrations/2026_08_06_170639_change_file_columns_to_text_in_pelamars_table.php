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
        Schema::table('pelamars', function (Blueprint $table) {
            $table->text('file_sertifikat')->nullable()->change();
            // Just in case other files have multiple links
            $table->text('file_cv')->nullable()->change();
            $table->text('file_pas_foto')->nullable()->change();
            $table->text('file_ktp')->nullable()->change();
            $table->text('file_sertifikat_bahasa')->nullable()->change();
            $table->text('file_ijazah')->nullable()->change();
            $table->text('file_transkrip')->nullable()->change();
            $table->text('file_ijazah_2')->nullable()->change();
            $table->text('file_transkrip_2')->nullable()->change();
            $table->text('file_ijazah_3')->nullable()->change();
            $table->text('file_transkrip_3')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->string('file_sertifikat', 255)->nullable()->change();
            $table->string('file_cv', 255)->nullable()->change();
            $table->string('file_pas_foto', 255)->nullable()->change();
            $table->string('file_ktp', 255)->nullable()->change();
            $table->string('file_sertifikat_bahasa', 255)->nullable()->change();
            $table->string('file_ijazah', 255)->nullable()->change();
            $table->string('file_transkrip', 255)->nullable()->change();
            $table->string('file_ijazah_2', 255)->nullable()->change();
            $table->string('file_transkrip_2', 255)->nullable()->change();
            $table->string('file_ijazah_3', 255)->nullable()->change();
            $table->string('file_transkrip_3', 255)->nullable()->change();
        });
    }
};
